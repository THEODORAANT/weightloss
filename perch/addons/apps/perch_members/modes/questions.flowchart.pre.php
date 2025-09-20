<?php
    if (!function_exists('perch_members_flowchart_normalize_option_label')) {
        function perch_members_flowchart_normalize_option_label($value, $option)
        {
            if (is_array($option)) {
                if (isset($option['label'])) {
                    $label = $option['label'];
                } else {
                    $parts = [];
                    foreach ($option as $part) {
                        if (is_scalar($part)) {
                            $parts[] = (string)$part;
                        }
                    }
                    $label = implode(' ', $parts);
                }
            } else {
                $label = $option;
            }

            $label = trim((string)$label);

            if ($label === '') {
                $label = (string)$value;
            }

            return $label;
        }
    }

    if (!function_exists('perch_members_flowchart_option_labels')) {
        function perch_members_flowchart_option_labels($options)
        {
            $map = [];

            if (!is_array($options) || !PerchUtil::count($options)) {
                return $map;
            }

            foreach ($options as $value => $option) {
                $candidates = [];

                if (is_string($value) || is_numeric($value)) {
                    $candidates[] = (string)$value;
                }

                if (is_array($option) && array_key_exists('value', $option)) {
                    $candidate_value = $option['value'];
                    if ($candidate_value !== null) {
                        $candidates[] = (string)$candidate_value;
                    }
                }

                if (!count($candidates)) {
                    if (is_scalar($option)) {
                        $candidates[] = (string)$option;
                    }
                }

                $label = perch_members_flowchart_normalize_option_label($value, $option);

                foreach ($candidates as $candidate) {
                    $map[(string)$candidate] = $label;
                }
            }

            return $map;
        }
    }

    if (!function_exists('perch_members_flowchart_follow_label')) {
        function perch_members_flowchart_follow_label($follow_step, $Lang)
        {
            $display_values = [];
            if (isset($follow_step['displayValues']) && is_array($follow_step['displayValues']) && PerchUtil::count($follow_step['displayValues'])) {
                $display_values = $follow_step['displayValues'];
            }

            $type = isset($follow_step['type']) ? $follow_step['type'] : 'dependency';

            if (PerchUtil::count($display_values)) {
                $label = implode(', ', $display_values);
                if ($type === 'order') {
                    $label .= ' ('.$Lang->get('Default order').')';
                }
                return $label;
            }

            if ($type === 'order') {
                return $Lang->get('Default order');
            }

            return $Lang->get('Any value');
        }
    }

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
                'connections'  => [],
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

                $option_labels = [];
                if (isset($question['options'])) {
                    $option_labels = perch_members_flowchart_option_labels($question['options']);
                }

                $covered_values = [];
                $covers_all_values = false;

                foreach ($dependencies as &$dependency) {
                    $resolved_step = null;
                    $resolved_order = null;
                    $target_question_label = null;
                    $target_question_sort = null;

                    $dependency_values = isset($dependency['values']) && is_array($dependency['values']) ? $dependency['values'] : [];
                    $normalized_values = [];
                    $display_values = [];

                    if (PerchUtil::count($dependency_values)) {
                        foreach ($dependency_values as $value) {
                            if ($value === null) continue;
                            $value_key = (string)$value;
                            if (!in_array($value_key, $normalized_values, true)) {
                                $normalized_values[] = $value_key;
                            }
                            $covered_values[$value_key] = true;
                            if (isset($option_labels[$value_key])) {
                                $display_values[] = $option_labels[$value_key];
                            } else {
                                $display_values[] = $value_key;
                            }
                        }
                    } else {
                        $covers_all_values = true;
                    }

                    $dependency['values'] = $normalized_values;
                    $dependency['valueLabels'] = $display_values;

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

                    $follow_step = [
                        'values'        => $normalized_values,
                        'displayValues' => $display_values,
                        'step'          => $resolved_step,
                        'order'         => $resolved_order,
                        'question'      => $target_question_key,
                        'questionLabel' => $target_question_label,
                        'type'          => 'dependency',
                        'index'         => isset($dependency['index']) ? $dependency['index'] : null,
                    ];
                    $follow_step['label'] = perch_members_flowchart_follow_label($follow_step, $Lang);
                    $follow_steps[] = $follow_step;
                }
                unset($dependency);

                if (isset($default_paths[$question_key])) {
                    $default = $default_paths[$question_key];
                    $default_step = isset($default['next_step']) ? $default['next_step'] : null;
                    $default_question_key = isset($default['next_question']) ? $default['next_question'] : null;
                    $default_question_label = ($default_question_key && isset($data['questions'][$default_question_key]['label'])) ? $data['questions'][$default_question_key]['label'] : null;
                    $default_order = ($default_step && isset($step_order_map[$default_step])) ? $step_order_map[$default_step] : null;

                    $default_values = [];
                    $default_display_values = [];

                    if (!$covers_all_values && PerchUtil::count($option_labels)) {
                        foreach ($option_labels as $value_key => $label_text) {
                            if (!isset($covered_values[$value_key])) {
                                if (!in_array($value_key, $default_values, true)) {
                                    $default_values[] = $value_key;
                                }
                                if (!in_array($label_text, $default_display_values, true)) {
                                    $default_display_values[] = $label_text;
                                }
                            }
                        }
                    }

                    $follow_step = [
                        'values'        => $default_values,
                        'displayValues' => $default_display_values,
                        'step'          => $default_step,
                        'order'         => $default_order,
                        'question'      => $default_question_key,
                        'questionLabel' => $default_question_label,
                        'type'          => 'order',
                        'index'         => null,
                    ];
                    $follow_step['label'] = perch_members_flowchart_follow_label($follow_step, $Lang);
                    $follow_steps[] = $follow_step;
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

                $connections = [];
                if (PerchUtil::count($follow_steps)) {
                    foreach ($follow_steps as $follow_step_item) {
                        $target_question_key = isset($follow_step_item['question']) ? $follow_step_item['question'] : null;
                        $target_step = isset($follow_step_item['step']) ? $follow_step_item['step'] : null;

                        if ($target_question_key === null && ($target_step === null || $target_step === '')) {
                            continue;
                        }

                        $connections[] = [
                            'question'      => $target_question_key,
                            'step'          => $target_step,
                            'type'          => isset($follow_step_item['type']) ? $follow_step_item['type'] : 'dependency',
                            'label'         => isset($follow_step_item['label']) ? $follow_step_item['label'] : null,
                            'values'        => isset($follow_step_item['values']) ? $follow_step_item['values'] : [],
                            'displayValues' => isset($follow_step_item['displayValues']) ? $follow_step_item['displayValues'] : [],
                        ];
                    }
                }

                $question['followSteps'] = $follow_steps;
                $question['connections'] = $connections;
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
                'connections'  => isset($question['connections']) ? $question['connections'] : [],
                'step'         => $question['step'],
            ];
        }
    }
?>
