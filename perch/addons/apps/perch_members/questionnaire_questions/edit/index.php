<?php
    include('../../../../../core/inc/api.php');

    $API  = new PerchAPI(1.0, 'perch_members');
    $HTML   = $API->get('HTML');
    $Lang   = $API->get('Lang');

    include('../../PerchMembers_QuestionnaireQuestions.class.php');
    include('../../PerchMembers_QuestionnaireQuestion.class.php');

    $Perch->page_title = $Lang->get('Edit question');

    include('../../modes/_subnav.php');
    include('../../modes/questions.edit.pre.php');

    include(PERCH_CORE . '/inc/top.php');

    include('../../modes/questions.edit.post.php');

    include(PERCH_CORE . '/inc/btm.php');
?>
