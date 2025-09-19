<?php
    $HTML = $API->get('HTML');

    $Questions = new PerchMembers_QuestionnaireQuestions($API);

    $Paging = $API->get('Paging');
    $Paging->set_per_page(20);

    $questions = $Questions->all();
?>
