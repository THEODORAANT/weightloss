<?php
    echo $HTML->title_panel([
        'heading' => $Lang->get('Questionnaire questions'),
        'button'  => [
            'text' => $Lang->get('Add question'),
            'link' => 'questionnaire_questions/edit/',
            'icon' => 'add'
        ]
    ], $CurrentUser);

    if (isset($message)) echo $message;

    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'Question',
            'value'     => 'label',
            'sort'      => 'label',
            'edit_link' => 'questionnaire_questions/edit',
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
                echo '<td class="action">'.$Form->text('order-'.$Question->id(), $Question->sort(), 'input-simple xs', false, 'number', 'min="0" step="1"').'</td>';
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
