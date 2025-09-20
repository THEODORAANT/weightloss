<?php
    include('../../../../../core/inc/api.php');

    $API  = new PerchAPI(1.0, 'perch_members');
    $HTML = $API->get('HTML');
    $Lang = $API->get('Lang');

    include('../../PerchMembers_QuestionnaireQuestions.class.php');
    include('../../PerchMembers_QuestionnaireQuestion.class.php');

    $Perch->page_title = $Lang->get('Questionnaire flowchart');

    $Perch->add_css($API->app_path().'/assets/css/questionnaire-flowchart.css');
    $Perch->add_javascript($API->app_path().'/assets/js/questionnaire-flowchart.js');

    include('../../modes/_subnav.php');
    include('../../modes/questions.flowchart.pre.php');

    include(PERCH_CORE . '/inc/top.php');

    include('../../modes/questions.flowchart.post.php');

    include(PERCH_CORE . '/inc/btm.php');
?>
