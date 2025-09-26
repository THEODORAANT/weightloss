<?php

    $order_reference = $Order->orderInvoiceNumber();
    if ($order_reference === '') {
        $order_reference = '#'.$Order->id();
    }

    echo $HTML->title_panel([
        'heading' => $Lang->get('Questions for %s', $order_reference),
    ], $CurrentUser);

    include('_order_smartbar.php');

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

        echo '<h2>'.PerchUtil::html($section['title']).'</h2>';

        if (!PerchUtil::count($answers_by_slug)) {
            echo '<p>'.$Lang->get('No responses recorded for this questionnaire.').'</p>';
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

        foreach ($questions as $slug => $label) {
            if (!isset($answers_by_slug[$slug])) {
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

            echo '<tr>';
            echo '<td class="action">'.PerchUtil::html($label).'</td>';
            echo '<td>'.PerchUtil::html($answer_text).'</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }

    if (!$questions_rendered) {
        echo '<p>'.$Lang->get('No questionnaire responses were recorded for this order.').'</p>';
    }

    echo '</div>';
