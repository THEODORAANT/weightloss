<?php
    $message = false;
    $Questions = new PerchMembers_QuestionnaireQuestions($API);

    $flowchart_types = [
        'first-order' => $Lang->get('First-order questionnaire'),
        'reorder'     => $Lang->get('Re-order questionnaire'),
    ];

    $requested_type = PerchRequest::get('type');
    if (!$requested_type || !isset($flowchart_types[$requested_type])) {
        $requested_type = 'first-order';
    }

    $flowchart_data = [];
    $pending_steps = [];
    foreach ($flowchart_types as $type => $label) {
        $flowchart_data[$type] = [
            'label'     => $label,
            'steps'     => [],
            'questions' => [],
        ];
    }

    $questions = $Questions->all();

    if (PerchUtil::count($questions)) {
        foreach ($questions as $Question) {
            $type = $Question->questionnaireType();
            if (!isset($flowchart_data[$type])) {
                continue;
            }

            $question_key = $Question->questionKey();
            if ($question_key === '') {
                continue;
            }

            $step_slug = $Question->stepSlug();
            if ($step_slug === '' || $step_slug === null) {
                $step_slug = $question_key;
            }

            $dependencies = [];
            $raw_dependencies = $Question->dependencies();
            if ($raw_dependencies) {
                $decoded_dependencies = PerchUtil::json_safe_decode($raw_dependencies, true);
                if (is_array($decoded_dependencies)) {
                    $dependency_index = 0;
                    foreach ($decoded_dependencies as $rule) {
                        if (!is_array($rule)) continue;

                        $values = [];
                        if (isset($rule['values'])) {
                            if (is_array($rule['values'])) {
                                foreach ($rule['values'] as $value) {
                                    if ($value === null) continue;
                                    $values[] = (string)$value;
                                }
                            } else {
                                $values[] = (string)$rule['values'];
                            }
                        }

                        $target_question = (isset($rule['question']) && $rule['question'] !== '') ? (string)$rule['question'] : null;
                        $target_step     = (isset($rule['step']) && $rule['step'] !== '') ? (string)$rule['step'] : null;

                        if ($target_step) {
                            if (!isset($pending_steps[$type])) {
                                $pending_steps[$type] = [];
                            }
                            if (!isset($pending_steps[$type][$target_step])) {
                                $pending_steps[$type][$target_step] = [];
                            }
                            $pending_steps[$type][$target_step][] = [
                                'source_sort'     => (float)$Question->sort(),
                                'source_question' => $question_key,
                                'source_step'     => $step_slug,
                            ];
                        }

                        $dependencies[] = [
                            'values'   => $values,
                            'question' => $target_question,
                            'step'     => $target_step,
                            'index'    => $dependency_index++,
                        ];
                    }
                }
            }

            $flowchart_data[$type]['questions'][$question_key] = [
                'id'           => (int)$Question->id(),
                'key'          => $question_key,
                'label'        => $Question->label(),
                'fieldName'    => $Question->fieldName(),
                'step'         => $step_slug,
                'type'         => $Question->type(),
                'sort'         => (int)$Question->sort(),
                'dependencies' => $dependencies,
                'followSteps'  => [],
                'options'      => $Question->option_list(),
                'optionSummary'=> $Question->option_summary(),
            ];

            if (!isset($flowchart_data[$type]['steps'][$step_slug])) {
                $flowchart_data[$type]['steps'][$step_slug] = [
                    'slug'      => $step_slug,
                    'questions' => [],
                    'sort'      => (float)$Question->sort(),
                    'order'     => null,
                    'isVirtual' => false,
                ];
            }

            $flowchart_data[$type]['steps'][$step_slug]['questions'][] = $question_key;

            if (!isset($flowchart_data[$type]['steps'][$step_slug]['sort']) || $flowchart_data[$type]['steps'][$step_slug]['sort'] > (float)$Question->sort()) {
                $flowchart_data[$type]['steps'][$step_slug]['sort'] = (float)$Question->sort();
            }
        }

        if (!empty($pending_steps)) {
            foreach ($pending_steps as $type => $step_sources) {
                if (!isset($flowchart_data[$type])) continue;

                foreach ($step_sources as $step_slug => $sources) {
                    if (isset($flowchart_data[$type]['steps'][$step_slug])) continue;

                    $min_sort = null;
                    if (is_array($sources)) {
                        foreach ($sources as $source) {
                            if (!is_array($source)) continue;
                            $source_sort = isset($source['source_sort']) ? (float)$source['source_sort'] : null;
                            if ($source_sort === null) continue;

                            $adjusted = $source_sort + 0.25;
                            if ($min_sort === null || $adjusted < $min_sort) {
                                $min_sort = $adjusted;
                            }
                        }
                    }

                    if ($min_sort === null) {
                        $min_sort = 0.0;
                    }

                    $flowchart_data[$type]['steps'][$step_slug] = [
                        'slug'      => $step_slug,
                        'questions' => [],
                        'sort'      => $min_sort,
                        'order'     => null,
                        'isVirtual' => true,
                    ];
                }
            }
        }

        foreach ($flowchart_data as $type => &$data) {
            uasort($data['steps'], function ($a, $b) {
                $a_sort = isset($a['sort']) ? $a['sort'] : 0;
                $b_sort = isset($b['sort']) ? $b['sort'] : 0;

                if ($a_sort === $b_sort) {
                    $a_slug = isset($a['slug']) ? $a['slug'] : '';
                    $b_slug = isset($b['slug']) ? $b['slug'] : '';
                    return strcmp($a_slug, $b_slug);
                }

                return ($a_sort < $b_sort) ? -1 : 1;
            });

            $step_order_map = [];
            $step_position = 1;
            foreach ($data['steps'] as $slug => &$step) {
                if (!isset($step['questions']) || !is_array($step['questions'])) {
                    $step['questions'] = [];
                }

                usort($step['questions'], function ($a_key, $b_key) use ($data) {
                    $a_sort = isset($data['questions'][$a_key]['sort']) ? $data['questions'][$a_key]['sort'] : 0;
                    $b_sort = isset($data['questions'][$b_key]['sort']) ? $data['questions'][$b_key]['sort'] : 0;

                    if ($a_sort === $b_sort) {
                        return strcmp($a_key, $b_key);
                    }

                    return ($a_sort < $b_sort) ? -1 : 1;
                });

                $step['order'] = $step_position;
                $step_order_map[$slug] = $step_position;
                $step_position++;
            }
            unset($step);

            $question_sequence = [];
            foreach ($data['questions'] as $question_key => $question_details) {
                $question_sequence[] = [
                    'key'  => $question_key,
                    'sort' => isset($question_details['sort']) ? (float)$question_details['sort'] : 0.0,
                ];
            }

            usort($question_sequence, function ($a, $b) {
                if ($a['sort'] === $b['sort']) {
                    return strcmp($a['key'], $b['key']);
                }

                return ($a['sort'] < $b['sort']) ? -1 : 1;
            });

            $default_paths = [];
            $sequence_count = PerchUtil::count($question_sequence);
            if ($sequence_count) {
                for ($i = 0; $i < $sequence_count; $i++) {
                    $current = $question_sequence[$i];
                    $next = ($i + 1 < $sequence_count) ? $question_sequence[$i + 1] : null;
                    if (!$next) {
                        continue;
                    }

                    $next_key = $next['key'];
                    $default_paths[$current['key']] = [
                        'next_question' => $next_key,
                        'next_step'     => isset($data['questions'][$next_key]) ? $data['questions'][$next_key]['step'] : null,
                    ];
                }
            }

            foreach ($data['questions'] as $question_key => &$question) {
                $dependencies = isset($question['dependencies']) && is_array($question['dependencies']) ? $question['dependencies'] : [];
                $follow_steps = [];

                foreach ($dependencies as &$dependency) {
                    $resolved_step = null;
                    $resolved_order = null;
                    $target_question_label = null;
                    $target_question_sort = null;

                    $target_question_key = isset($dependency['question']) ? $dependency['question'] : null;
                    if ($target_question_key && isset($data['questions'][$target_question_key])) {
                        $target_question = $data['questions'][$target_question_key];
                        if (isset($target_question['step']) && $target_question['step'] !== '') {
                            $resolved_step = $target_question['step'];
                        }
                        $target_question_label = isset($target_question['label']) ? $target_question['label'] : null;
                        $target_question_sort = isset($target_question['sort']) ? $target_question['sort'] : null;
                    }

                    if (isset($dependency['step']) && $dependency['step'] !== null && $dependency['step'] !== '') {
                        $resolved_step = $dependency['step'];
                    }

                    if ($resolved_step && isset($step_order_map[$resolved_step])) {
                        $resolved_order = $step_order_map[$resolved_step];
                    }

                    $dependency['resolvedStep'] = $resolved_step;
                    $dependency['resolvedStepOrder'] = $resolved_order;
                    $dependency['targetQuestionLabel'] = $target_question_label;
                    $dependency['targetQuestionSort'] = $target_question_sort;

                    $follow_steps[] = [
                        'values'        => isset($dependency['values']) ? $dependency['values'] : [],
                        'step'          => $resolved_step,
                        'order'         => $resolved_order,
                        'question'      => $target_question_key,
                        'questionLabel' => $target_question_label,
                        'type'          => 'dependency',
                        'index'         => isset($dependency['index']) ? $dependency['index'] : null,
                    ];
                }
                unset($dependency);

                if (isset($default_paths[$question_key])) {
                    $default = $default_paths[$question_key];
                    $default_step = isset($default['next_step']) ? $default['next_step'] : null;
                    $default_question_key = isset($default['next_question']) ? $default['next_question'] : null;
                    $default_question_label = ($default_question_key && isset($data['questions'][$default_question_key]['label'])) ? $data['questions'][$default_question_key]['label'] : null;
                    $default_order = ($default_step && isset($step_order_map[$default_step])) ? $step_order_map[$default_step] : null;

                    $follow_steps[] = [
                        'values'        => [],
                        'step'          => $default_step,
                        'order'         => $default_order,
                        'question'      => $default_question_key,
                        'questionLabel' => $default_question_label,
                        'type'          => 'order',
                        'index'         => null,
                    ];
                }

                if (PerchUtil::count($follow_steps)) {
                    usort($follow_steps, function ($a, $b) {
                        $a_weight = ($a['type'] === 'order') ? 1 : 0;
                        $b_weight = ($b['type'] === 'order') ? 1 : 0;
                        if ($a_weight !== $b_weight) {
                            return ($a_weight < $b_weight) ? -1 : 1;
                        }

                        $a_order = isset($a['order']) ? $a['order'] : null;
                        $b_order = isset($b['order']) ? $b['order'] : null;
                        if ($a_order !== $b_order) {
                            if ($a_order === null) return 1;
                            if ($b_order === null) return -1;
                            return ($a_order < $b_order) ? -1 : 1;
                        }

                        if ($a['type'] === 'dependency' && $b['type'] === 'dependency') {
                            $a_index = isset($a['index']) ? $a['index'] : 0;
                            $b_index = isset($b['index']) ? $b['index'] : 0;
                            if ($a_index !== $b_index) {
                                return ($a_index < $b_index) ? -1 : 1;
                            }
                        }

                        $a_step = isset($a['step']) ? $a['step'] : '';
                        $b_step = isset($b['step']) ? $b['step'] : '';
                        return strcmp($a_step, $b_step);
                    });
                }

                $question['followSteps'] = $follow_steps;
                $question['dependencies'] = $dependencies;
            }
            unset($question);
        }
        unset($data);
    }

    if (!isset($flowchart_types[$requested_type])) {
        $requested_type = key($flowchart_types);
    }

    if (!PerchUtil::count($flowchart_data[$requested_type]['steps'])) {
        foreach ($flowchart_types as $type => $label) {
            if (PerchUtil::count($flowchart_data[$type]['steps'])) {
                $requested_type = $type;
                break;
            }
        }
    }

    $active_type = $requested_type;

    $flowchart_payload = [];
    foreach ($flowchart_data as $type => $data) {
        $flowchart_payload[$type] = [
            'questions' => [],
        ];

        foreach ($data['questions'] as $key => $question) {
            $flowchart_payload[$type]['questions'][$key] = [
                'dependencies' => $question['dependencies'],
                'step'         => $question['step'],
            ];
        }
    }
?>
