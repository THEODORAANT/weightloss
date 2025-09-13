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
            'title'     => 'Type',
            'value'     => 'questionnaireType',
            'sort'      => 'questionnaireType',
        ]);

    echo $Listing->render($questions);
?>
