<?php
    $message = false;
    $Questions = new PerchMembers_QuestionnaireQuestions($API);

    if (isset($_GET['id']) && $_GET['id']!='') {
        $questionID = (int) $_GET['id'];
        $Question = $Questions->find($questionID);
        $details = $Question->to_array();
        if (isset($details['options'])) {
            $opts = PerchUtil::json_safe_decode($details['options'], true);
            if (is_array($opts)) {
                $lines = [];
                foreach($opts as $k=>$v) $lines[] = $k.':'.$v;
                $details['options'] = implode("\n", $lines);
            }
        }
        $heading1 = $Lang->get('Editing question');
    } else {
        $Question = false;
        $details = [];
        $heading1 = $Lang->get('Creating question');
    }

    $Form = $API->get('Form');
    $Form->require_field('label', 'Required');
    $Form->require_field('questionKey', 'Required');

    if ($Form->submitted()) {
        $postvars = ['questionnaireType','questionKey','label','type','options','sort'];
        $data = $Form->receive($postvars);
        if (isset($data['options'])) {
            $opts = preg_split('/\r\n|\r|\n/', trim($data['options']));
            $json = [];
            foreach($opts as $line) {
                if ($line==='') continue;
                $parts = explode(':', $line, 2);
                if (count($parts)==2) {
                    $json[$parts[0]] = $parts[1];
                } else {
                    $json[$line] = $line;
                }
            }
            $data['options'] = json_encode($json);
        }
        if ($Question) {
            $Question->update($data);
        } else {
            $Question = $Questions->create($data);
        }
        $message = $HTML->success_message('Question has been successfully saved. Return to %squestion listing%s', '<a href="'.$API->app_path().'/questionnaire_questions/">', '</a>');
        $details = $Question->to_array();
        if (isset($details['options'])) {
            $opts = PerchUtil::json_safe_decode($details['options'], true);
            if (is_array($opts)) {
                $lines = [];
                foreach($opts as $k=>$v) $lines[] = $k.':'.$v;
                $details['options'] = implode("\n", $lines);
            }
        }
    }
?>
