<?php
    include('../../../../core/inc/api.php');

    $API  = new PerchAPI(1.0, 'perch_members');
    $Lang = $API->get('Lang');

    include('../PerchMembers_QuestionnaireQuestions.class.php');
    include('../PerchMembers_QuestionnaireQuestion.class.php');

    $Perch->page_title = $Lang->get('Questionnaire questions');

    include('../modes/_subnav.php');
    include('../modes/questions.list.pre.php');

    include(PERCH_CORE . '/inc/top.php');

    include('../modes/questions.list.post.php');

    include(PERCH_CORE . '/inc/btm.php');
?>
