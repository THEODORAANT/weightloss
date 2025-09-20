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

                        $dependencies[] = [
                            'values'   => $values,
                            'question' => (isset($rule['question']) && $rule['question'] !== '') ? (string)$rule['question'] : null,
                            'step'     => (isset($rule['step']) && $rule['step'] !== '') ? (string)$rule['step'] : null,
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
                'options'      => $Question->option_list(),
                'optionSummary'=> $Question->option_summary(),
            ];

            if (!isset($flowchart_data[$type]['steps'][$step_slug])) {
                $flowchart_data[$type]['steps'][$step_slug] = [
                    'slug'      => $step_slug,
                    'questions' => [],
                    'sort'      => (int)$Question->sort(),
                ];
            }

            $flowchart_data[$type]['steps'][$step_slug]['questions'][] = $question_key;

            if (!isset($flowchart_data[$type]['steps'][$step_slug]['sort']) || $flowchart_data[$type]['steps'][$step_slug]['sort'] > (int)$Question->sort()) {
                $flowchart_data[$type]['steps'][$step_slug]['sort'] = (int)$Question->sort();
            }
        }

        foreach ($flowchart_data as $type => &$data) {
            uasort($data['steps'], function ($a, $b) {
                if ($a['sort'] === $b['sort']) {
                    return strcmp($a['slug'], $b['slug']);
                }

                return ($a['sort'] < $b['sort']) ? -1 : 1;
            });

            foreach ($data['steps'] as &$step) {
                usort($step['questions'], function ($a_key, $b_key) use ($data) {
                    $a_sort = isset($data['questions'][$a_key]['sort']) ? $data['questions'][$a_key]['sort'] : 0;
                    $b_sort = isset($data['questions'][$b_key]['sort']) ? $data['questions'][$b_key]['sort'] : 0;

                    if ($a_sort === $b_sort) {
                        return strcmp($a_key, $b_key);
                    }

                    return ($a_sort < $b_sort) ? -1 : 1;
                });
            }
            unset($step);
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
