<?php
    echo $HTML->title_panel([
        'heading' => $Lang->get('Questionnaire questions'),
        'button'  => [
            'text' => $Lang->get('Add question'),
            'link' => 'edit',
            'icon' => 'add'
        ]
    ], $CurrentUser);

    if (isset($message)) echo $message;

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);
    $Smartbar->add_item([
        'active' => true,
        'title'  => $Lang->get('Questions'),
        'link'   => $API->app_nav().'/questionnaire_questions/',
    ]);
    $Smartbar->add_item([
        'active' => false,
        'title'  => $Lang->get('Flowchart'),
        'link'   => $API->app_nav().'/questionnaire_questions/flowchart/',
    ]);

    echo $Smartbar->render();

    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'Question',
            'value'     => 'label',
            'sort'      => 'label',
            'edit_link' => 'edit',
            'priv'      => 'perch_members.questionnaires.manage',
        ]);

    $Listing->add_col([
            'title'     => $Lang->get('Display order'),
            'value'     => 'sort',
            'sort'      => 'sort',
        ]);

    $Listing->add_col([
            'title'     => $Lang->get('Questionnaire type'),
            'value'     => 'questionnaireType',
            'sort'      => 'questionnaireType',
        ]);

    $Listing->add_col([
            'title'     => $Lang->get('Answer type'),
            'value'     => 'type',
            'sort'      => 'type',
        ]);

    $Listing->add_col([
            'title' => $Lang->get('Field name'),
            'value' => function ($Question, $HTML, $Lang) {
                $field = $Question->fieldName();
                if ($field === null || $field === '') {
                    $field = $Question->questionKey();
                }

                return $HTML->encode($field);
            },
            'sort'  => 'fieldName',
        ]);

    $Listing->add_col([
            'title' => $Lang->get('Step'),
            'value' => function ($Question, $HTML, $Lang) {
                $step = $Question->stepSlug();
                if ($step === null || $step === '') {
                    return $HTML->encode('—');
                }

                return $HTML->encode($step);
            },
            'sort'  => 'stepSlug',
        ]);

    $Listing->add_col([
            'title' => $Lang->get('Dependencies'),
            'value' => function ($Question, $HTML, $Lang) {
                $raw = $Question->dependencies();
                if (!$raw) {
                    return $HTML->encode('—');
                }

                $decoded = PerchUtil::json_safe_decode($raw, true);
                if (!is_array($decoded)) {
                    return $HTML->encode('—');
                }

                $count = PerchUtil::count($decoded);
                if ($count === false || $count === 0) {
                    return $HTML->encode('—');
                }

                return $HTML->encode((string)$count);
            },
        ]);

    $Listing->add_col([
            'title' => $Lang->get('Answers'),
            'value' => function ($Question, $HTML, $Lang) {
                $summary = $Question->option_summary();
                if ($summary === '') {
                    return $HTML->encode($Lang->get('None'));
                }

                return $HTML->encode($summary);
            },
        ]);

    echo $Listing->render($questions);

    if (isset($question_groups) && PerchUtil::count($question_groups)) {
        echo $HTML->heading2($Lang->get('Adjust display order'));

        echo $Form->form_start();
        echo $Form->hidden('orders', '1');

        $type_labels = [
            'first-order' => $Lang->get('First-order questionnaire'),
            'reorder'     => $Lang->get('Re-order questionnaire'),
        ];

        foreach ($type_labels as $type => $label) {
            if (!isset($question_groups[$type]) || !PerchUtil::count($question_groups[$type])) continue;

            echo $HTML->heading3($label);
            echo '<div class="form-inner">';
            echo '<table class="d">';
            echo '<thead><tr>';
            echo '<th class="action">'.$Lang->get('Order').'</th>';
            echo '<th>'.$Lang->get('Question').'</th>';
            echo '<th>'.$Lang->get('Key').'</th>';
            echo '</tr></thead>';
            echo '<tbody>';

            foreach ($question_groups[$type] as $Question) {
                echo '<tr>';
                echo '<td class="action">'.$Form->text('order-'.$Question->id(), $Question->sort(), 'input-simple ', false, 'number', 'min="0" step="1"').'</td>';
                echo '<td>'.PerchUtil::html($Question->label()).'</td>';
                echo '<td>'.PerchUtil::html($Question->questionKey()).'</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        }

        echo $Form->submit_field('btnSaveOrder', 'Save order');
        echo $Form->form_end();
    }

?>
