<?php
    echo $HTML->title_panel([
        'heading' => $Lang->get('Questionnaire flowchart'),
        'button'  => [
            'text' => $Lang->get('Add question'),
            'link' => '../edit/',
            'icon' => 'add',
            'priv' => 'perch_members.questionnaires.manage',
        ],
    ], $CurrentUser);

    if ($message) echo $message;

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);
    $Smartbar->add_item([
        'active' => false,
        'title'  => $Lang->get('Questions'),
        'link'   => $API->app_nav().'/questionnaire_questions/',
    ]);
    $Smartbar->add_item([
        'active' => true,
        'title'  => $Lang->get('Flowchart'),
        'link'   => $API->app_nav().'/questionnaire_questions/flowchart/',
    ]);

    echo $Smartbar->render();

    echo '<div class="flowchart-toolbar">';
        echo '<div class="flowchart-toolbar__tabs">';
        foreach ($flowchart_types as $type => $label) {
            $tab_classes = 'flowchart-toolbar__tab';
            if ($type === $active_type) {
                $tab_classes .= ' is-active';
            }
            $url = '?type='.urlencode($type);
            echo '<a class="'.$tab_classes.'" href="'.$HTML->encode($url).'" data-flowchart-tab="'.$HTML->encode($type).'">'.$HTML->encode($label).'</a>';
        }
        echo '</div>';
        echo '<p class="flowchart-toolbar__hint">'.$HTML->encode($Lang->get('Arrows indicate how answers reveal subsequent steps. Click a card to edit the question.')).'</p>';
    echo '</div>';

    echo '<div class="flowchart-legend">';
        echo '<span class="flowchart-legend__item"><span class="flowchart-legend__swatch"></span>'.$HTML->encode($Lang->get('Dependency connection')).'</span>';
        echo '<span class="flowchart-legend__note">'.$HTML->encode($Lang->get('Answer values appear beside each arrow.')).'</span>';
    echo '</div>';

    echo '<div class="questionnaire-flowchart" data-flowchart data-active-type="'.$HTML->encode($active_type).'">';

    foreach ($flowchart_types as $type => $label) {
        $is_active = ($type === $active_type) ? ' is-active' : '';
        echo '<section class="flowchart-canvas'.$is_active.'" data-flowchart-canvas="'.$HTML->encode($type).'">';

        $json = isset($flowchart_payload[$type]) ? PerchUtil::json_safe_encode($flowchart_payload[$type]) : '{}';
        if ($json === false) {
            $json = '{}';
        }
        echo '<script type="application/json" class="js-flowchart-data">'.$json.'</script>';

        if (!PerchUtil::count($flowchart_data[$type]['steps'])) {
            echo '<div class="flowchart-empty">'.$HTML->encode($Lang->get('No questions found for this questionnaire type yet.')).'</div>';
        } else {
            echo '<div class="flowchart-grid">';
            foreach ($flowchart_data[$type]['steps'] as $step_slug => $step_data) {
                $step_classes = 'flowchart-step';
                if (!empty($step_data['isVirtual'])) {
                    $step_classes .= ' flowchart-step--virtual';
                }

                echo '<div class="'.$step_classes.'" data-step-slug="'.$HTML->encode($step_slug).'">';
                    echo '<header class="flowchart-step__header">';
                        if (isset($step_data['order']) && $step_data['order']) {
                            echo '<span class="flowchart-step__index">'.$HTML->encode('#'.(string)$step_data['order']).'</span>';
                        }
                        echo '<span class="flowchart-step__title">'.$HTML->encode($step_slug).'</span>';
                    echo '</header>';

                    echo '<div class="flowchart-step__questions">';
                    if (!PerchUtil::count($step_data['questions'])) {
                        if (!empty($step_data['isVirtual'])) {
                            echo '<p class="flowchart-step__empty">'.$HTML->encode($Lang->get('No questions assigned to this step yet.')).'</p>';
                        }
                    }
                    foreach ($step_data['questions'] as $question_key) {
                        if (!isset($flowchart_data[$type]['questions'][$question_key])) continue;

                        $question = $flowchart_data[$type]['questions'][$question_key];
                        $field_name = $question['fieldName'];
                        if ($field_name === '' || $field_name === null) {
                            $field_name = $question['key'];
                        }
                        $option_list = $question['options'];
                        $option_summary = trim($question['optionSummary']);
                        $dependencies = $question['dependencies'];
                        $follow_steps = isset($question['followSteps']) ? $question['followSteps'] : [];
                        $edit_url = $API->app_path().'/questionnaire_questions/edit/?id='.$question['id'];

                        $aria_label = $Lang->get('Edit question').' – '.$question['label'];
                        echo '<article class="flowchart-node" data-question-key="'.$HTML->encode($question['key']).'" data-question-id="'.$HTML->encode((string)$question['id']).'" data-edit-url="'.$HTML->encode($edit_url).'" tabindex="0" role="link" aria-label="'.$HTML->encode($aria_label).'">';
                            echo '<header class="flowchart-node__header">';
                                echo '<h3 class="flowchart-node__title">'.$HTML->encode($question['label']).'</h3>';
                            echo '</header>';

                            echo '<div class="flowchart-node__meta">';
                                echo '<dl>';
                                    echo '<div class="flowchart-node__meta-row"><dt>'.$HTML->encode($Lang->get('Key')).'</dt><dd>'.$HTML->encode($question['key']).'</dd></div>';
                                    echo '<div class="flowchart-node__meta-row"><dt>'.$HTML->encode($Lang->get('Field')).'</dt><dd>'.$HTML->encode($field_name).'</dd></div>';
                                    echo '<div class="flowchart-node__meta-row"><dt>'.$HTML->encode($Lang->get('Type')).'</dt><dd>'.$HTML->encode($question['type']).'</dd></div>';
                                    echo '<div class="flowchart-node__meta-row"><dt>'.$HTML->encode($Lang->get('Order')).'</dt><dd>'.$HTML->encode((string)$question['sort']).'</dd></div>';
                                echo '</dl>';
                            echo '</div>';

                            if ($option_summary !== '') {
                                echo '<div class="flowchart-node__options">';
                                    echo '<strong>'.$HTML->encode($Lang->get('Options')).'</strong>';
                                    echo '<p>'.$HTML->encode($option_summary).'</p>';
                                echo '</div>';
                            } elseif (PerchUtil::count($option_list)) {
                                $display_options = array_slice($option_list, 0, 5, true);
                                echo '<div class="flowchart-node__options">';
                                    echo '<strong>'.$HTML->encode($Lang->get('Options')).'</strong>';
                                    echo '<ul>';
                                        foreach ($display_options as $value => $label) {
                                            if (is_array($label) && isset($label['label'])) {
                                                $option_label = $label['label'];
                                            } elseif (is_array($label)) {
                                                $option_label = implode(' ', $label);
                                            } else {
                                                $option_label = $label;
                                            }
                                            echo '<li>'.$HTML->encode($option_label).' <span class="flowchart-node__option-value">'.$HTML->encode('['.$value.']').'</span></li>';
                                        }
                                        $option_count = PerchUtil::count($option_list);
                                        if ($option_count > 5) {
                                            $remaining = $option_count - 5;
                                            echo '<li class="flowchart-node__option-more">'.$HTML->encode('+'.$remaining.' '.$Lang->get('more')).'</li>';
                                        }
                                    echo '</ul>';
                                echo '</div>';
                            }

                            if (PerchUtil::count($follow_steps)) {
                                echo '<div class="flowchart-node__follow-steps">';
                                    echo '<strong>'.$HTML->encode($Lang->get('Follow-up steps')).'</strong>';
                                    echo '<ul>';
                                        foreach ($follow_steps as $follow_step) {
                                            $step_type = isset($follow_step['type']) ? $follow_step['type'] : 'dependency';
                                            $answer_classes = 'flowchart-node__follow-step-answer';
                                            if ($step_type === 'order') {
                                                $values_label = $Lang->get('Default order');
                                                $answer_classes .= ' flowchart-node__follow-step-answer--default';
                                            } else {
                                                $values_label = PerchUtil::count($follow_step['values']) ? implode(', ', $follow_step['values']) : $Lang->get('Any value');
                                            }

                                            $step_label = (isset($follow_step['step']) && $follow_step['step'] !== '') ? $follow_step['step'] : $Lang->get('Unknown step');
                                            $order_number = isset($follow_step['order']) ? $follow_step['order'] : null;
                                            $question_text = null;
                                            if (!empty($follow_step['question'])) {
                                                $question_text = $Lang->get('Question').': '.$follow_step['question'];
                                                if (!empty($follow_step['questionLabel'])) {
                                                    $question_text .= ' – '.$follow_step['questionLabel'];
                                                }
                                            }

                                            echo '<li>';
                                                echo '<span class="'.$answer_classes.'">'.$HTML->encode($values_label).'</span>';
                                                echo '<span class="flowchart-node__follow-step-arrow">&rarr;</span>';
                                                echo '<span class="flowchart-node__follow-step-target">'.$HTML->encode($Lang->get('Step').': '.$step_label).'</span>';
                                                if ($order_number !== null) {
                                                    echo '<span class="flowchart-node__follow-step-order">'.$HTML->encode('#'.(string)$order_number).'</span>';
                                                }
                                                if ($question_text !== null) {
                                                    echo '<span class="flowchart-node__follow-step-question">'.$HTML->encode($question_text).'</span>';
                                                }
                                            echo '</li>';
                                        }
                                    echo '</ul>';
                                echo '</div>';
                            }

                            if (PerchUtil::count($dependencies)) {
                                echo '<div class="flowchart-node__dependencies">';
                                    echo '<strong>'.$HTML->encode($Lang->get('Dependencies')).'</strong>';
                                    echo '<ul>';
                                        foreach ($dependencies as $dependency) {
                                            $values_label = PerchUtil::count($dependency['values']) ? implode(', ', $dependency['values']) : $Lang->get('Any value');
                                            $parts = [];

                                            if (isset($dependency['resolvedStep']) && $dependency['resolvedStep']) {
                                                $step_text = $Lang->get('Step').': '.$dependency['resolvedStep'];
                                                if (isset($dependency['resolvedStepOrder']) && $dependency['resolvedStepOrder'] !== null) {
                                                    $step_text .= ' (#'.$dependency['resolvedStepOrder'].')';
                                                }
                                                $parts[] = $step_text;
                                            } elseif (!empty($dependency['step'])) {
                                                $parts[] = $Lang->get('Step').': '.$dependency['step'];
                                            }

                                            if (!empty($dependency['question'])) {
                                                $question_target = $Lang->get('Question').': '.$dependency['question'];
                                                if (!empty($dependency['targetQuestionLabel'])) {
                                                    $question_target .= ' – '.$dependency['targetQuestionLabel'];
                                                }
                                                $parts[] = $question_target;
                                            }

                                            if (!PerchUtil::count($parts)) {
                                                $parts[] = $Lang->get('Unknown target');
                                            }

                                            $encoded_parts = [];
                                            foreach ($parts as $part_text) {
                                                $encoded_parts[] = $HTML->encode($part_text);
                                            }

                                            echo '<li>'.$HTML->encode($values_label).' &rarr; '.implode(' | ', $encoded_parts).'</li>';
                                        }
                                    echo '</ul>';
                                echo '</div>';
                            }

                            echo '<footer class="flowchart-node__footer">';
                                echo '<a class="flowchart-node__edit" href="'.$HTML->encode($edit_url).'">'.$HTML->encode($Lang->get('Edit question')).'</a>';
                            echo '</footer>';
                        echo '</article>';
                    }
                    echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        }

        echo '<svg class="flowchart-connections" aria-hidden="true"></svg>';
        echo '</section>';
    }

    echo '</div>';
?>
