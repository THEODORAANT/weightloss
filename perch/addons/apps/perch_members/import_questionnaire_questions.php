<?php
include('../../../core/inc/api.php');

$API  = new PerchAPI(1.0, 'perch_members');

include('PerchMembers_QuestionnaireQuestions.class.php');
include('PerchMembers_QuestionnaireQuestion.class.php');

$Questions = new PerchMembers_QuestionnaireQuestions($API);

$existing = $Questions->all();

if (PerchUtil::count($existing)) {
    echo "Questionnaire questions already exist.\n";
    return;
}

$data = include(__DIR__ . '/questionnaire_default_questions.php');

$sort = 0;
foreach ($data['reorder'] as $key => $q) {
    $sort += 10;
    $Questions->create([
        'questionnaireType' => 'reorder',
        'questionKey'       => $key,
        'label'             => $q['label'],
        'type'              => $q['type'],
        'options'           => isset($q['options']) ? PerchUtil::json_safe_encode($q['options']) : null,
        'sort'              => $sort,
    ]);
}

$sort = 0;
foreach ($data['first-order'] as $key => $q) {
    $sort += 10;
    $Questions->create([
        'questionnaireType' => 'first-order',
        'questionKey'       => $key,
        'label'             => $q['label'],
        'type'              => $q['type'],
        'options'           => isset($q['options']) ? PerchUtil::json_safe_encode($q['options']) : null,
        'sort'              => $sort,
    ]);
}

echo "Imported " . count($data['first-order']) . " first-order and " . count($data['reorder']) . " reorder questions.\n";
