<?php

    $order_reference = $Order->orderInvoiceNumber();
    if ($order_reference === '') {
        $order_reference = '#'.$Order->id();
    }

    echo $HTML->title_panel([
        'heading' => $Lang->get('Questions for %s', $order_reference),
    ], $CurrentUser);

    include('_order_smartbar.php');

    if ($message) {
        echo $message;
    }

    if ($manual_question_message) {
        echo $manual_question_message;
    }

    echo '<div class="inner">';

    $questions_rendered = false;

    foreach ($questionnaire_sections as $section) {
        $questions = isset($section['questions']) ? $section['questions'] : [];
        $answers   = isset($section['answers']) ? $section['answers'] : [];

        $answers_by_slug = [];
        if (PerchUtil::count($answers)) {
            foreach ($answers as $Answer) {
                $slug = $Answer->question_slug();
                if (!isset($answers_by_slug[$slug])) {
                    $answers_by_slug[$slug] = $Answer;
                }
            }
        }

        $missing_questions = [];
        if (is_array($questions) && count($questions)) {
            foreach ($questions as $slug => $label) {
                if (!isset($answers_by_slug[$slug])) {
                    $missing_questions[$slug] = $label;
                }
            }
        }

        $manual_form_html = '';
        if (PerchUtil::count($missing_questions)) {
            ob_start();
            echo '<div class="manual-question">';
            echo '<h3>'.$Lang->get('Manually add an answer').'</h3>';
            echo '<p>'.$Lang->get('Use this form to record an answer for any question that is missing from this questionnaire.').'</p>';
            echo $ManualQuestionForm->form_start(false, 'manual-question-form-'.$section['type']);
            echo '<input type="hidden" name="question_type" value="'.PerchUtil::html($section['type']).'">';
            echo $ManualQuestionForm->select_field('question_slug', 'Question', $missing_questions);
            echo $ManualQuestionForm->textarea_field('manual_answer_text', 'Answer', '', 'input-simple');
            echo $ManualQuestionForm->submit_field('btnAddManual', 'Add answer');
            echo $ManualQuestionForm->form_end();
            echo '</div>';
            $manual_form_html = ob_get_clean();
        }

        $answer_indicates_allergies = static function ($answerText) {
            $normalized = strtolower(trim((string) $answerText));

            return $normalized !== '' && strpos($normalized, 'yes') === 0;
        };

        $should_skip_question = static function ($slug, $answers_by_slug) use ($answer_indicates_allergies) {
            if ($slug === 'allergy_details') {
                if (!isset($answers_by_slug['allergies'])) {
                    return true;
                }

                $allergy_answer = $answers_by_slug['allergies']->answer_text();

                if ($allergy_answer === null || $allergy_answer === '') {
                    $allergy_answer = $answers_by_slug['allergies']->answer();
                }

                if (!$answer_indicates_allergies($allergy_answer)) {
                    return true;
                }
            }

            return false;
        };

        echo '<h2>'.PerchUtil::html($section['title']).'</h2>';

        if (!PerchUtil::count($answers_by_slug)) {
            echo '<p>'.$Lang->get('No responses recorded for this questionnaire.').'</p>';
            echo $manual_form_html;
            continue;
        }

        $questions_rendered = true;

        echo '<div class="form-inner">';
        echo '<table class="tags">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>'.$Lang->get('Question').'</th>';
        echo '<th>'.$Lang->get('Answer').'</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
  $historyPrinted = false;
        foreach ($questions as $slug => $label) {

            if (!isset($answers_by_slug[$slug])) {
                continue;
            }

            if ($should_skip_question($slug, $answers_by_slug)) {
                continue;
            }

            $Answer = $answers_by_slug[$slug];
            $answer_text = $Answer->answer_text();
            if ($answer_text === null || $answer_text === '') {
                $answer_text = $Answer->answer();
            }

            if (is_array($answer_text)) {
                $answer_text = implode(', ', array_map('strval', $answer_text));
            }

            $answer_text = trim((string) $answer_text);
   if (!$historyPrinted) {
                                echo '<tr><td colspan="2"><a class="button button button-simple" target="_blank" href="https://'.$_SERVER['HTTP_HOST'].'/perch/addons/apps/perch_members/questionnaire_logs?userId='.$Answer->uuid().'">History</a></td></tr>';
                                $historyPrinted = true;
                            }
            echo '<tr>';
            echo '<td class="action">'.PerchUtil::html($label).'</td>';

            $output = PerchUtil::html($answer_text);

            if ($slug === 'gp_informed') {
                $normalised_answer = strtolower(trim($answer_text));

                if ($normalised_answer === 'yes') {
                    $email_answer = '';

                    foreach (['email_address', 'GP_email_address'] as $email_slug) {
                        if (!isset($answers_by_slug[$email_slug])) {
                            continue;
                        }

                        $email_entry = $answers_by_slug[$email_slug];
                        $email_answer = trim((string) $email_entry->answer_text());

                        if ($email_answer === '') {
                            $entry_details = $email_entry->to_array();
                            if (is_array($entry_details) && isset($entry_details['answer'])) {
                                $email_answer = trim((string) $entry_details['answer']);
                            }
                        }

                        if ($email_answer !== '') {
                            break;
                        }
                    }

                    if ($email_answer !== '') {
                        $output .= '<br><small>GP email: '.PerchUtil::html($email_answer).'</small>';
                    }
                }
            }

            echo '<td>'.$output.'</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';

        echo $manual_form_html;
    }

    if (!$questions_rendered) {
        echo '<p>'.$Lang->get('No questionnaire responses were recorded for this order.').'</p>';
    }

    echo '<div class="questionnaire-notes">';
    echo $Form->form_start(false, 'questionnaire-notes-form');
    echo $Form->textarea_field('questionnaire_notes', 'Questionnaire notes', $questionnaire_notes, 'input-simple', false);
    echo $Form->submit_field('btnSubmit', 'Save notes');
    echo $Form->form_end();
    echo '</div>';

    echo '</div>';
