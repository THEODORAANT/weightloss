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
        if (isset($details['dependencies']) && $details['dependencies'] !== '') {
            $deps = PerchUtil::json_safe_decode($details['dependencies'], true);
            if (is_array($deps)) {
                $details['dependencies'] = PerchUtil::json_safe_encode($deps, true);
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
        $postvars = ['questionnaireType','questionKey','label','type','options','fieldName','stepSlug','dependencies','sort'];
        $data = $Form->receive($postvars);

        $options_input = isset($data['options']) ? trim($data['options']) : '';
        if (isset($data['options'])) {
            $opts = preg_split('/\r\n|\r|\n/', $options_input);
            $json = [];
            foreach($opts as $line) {
                $line = trim($line);
                if ($line==='') continue;
                $parts = explode(':', $line, 2);
                if (count($parts)==2) {
                    $json[$parts[0]] = $parts[1];
                } else {
                    $json[$line] = $line;
                }
            }
            $data['options'] = PerchUtil::json_safe_encode($json);
        }

        $dependencies_input = isset($data['dependencies']) ? trim($data['dependencies']) : '';
        $dependencies_valid = true;
        if ($dependencies_input === '') {
            $data['dependencies'] = null;
        } else {
            $decoded_dependencies = PerchUtil::json_safe_decode($dependencies_input, true);
            if (!is_array($decoded_dependencies)) {
                $dependencies_valid = false;
                $Form->error = true;
                $Form->messages['dependencies'] = $Lang->get('Dependencies must be valid JSON.');
                $message = $HTML->failure_message($Lang->get('Dependencies must be valid JSON.'));
            } else {
                $encoded = PerchUtil::json_safe_encode($decoded_dependencies);
                if ($encoded === false) {
                    $dependencies_valid = false;
                    $Form->error = true;
                    $Form->messages['dependencies'] = $Lang->get('Dependencies must be valid JSON.');
                    $message = $HTML->failure_message($Lang->get('Dependencies must be valid JSON.'));
                } else {
                    $data['dependencies'] = $encoded;
                }
            }
        }

        if (isset($data['fieldName']) && $data['fieldName'] === '') {
            $data['fieldName'] = null;
        }

        if (isset($data['stepSlug']) && $data['stepSlug'] === '') {
            $data['stepSlug'] = null;
        }

        if (!isset($data['sort']) || !is_numeric($data['sort'])) {
            $data['sort'] = $Questions->get_next_sort_for_type($data['questionnaireType']);
        } else {
            $data['sort'] = (int)$data['sort'];
            if ($data['sort'] < 0) {
                $data['sort'] = $Questions->get_next_sort_for_type($data['questionnaireType']);
            }
        }

        if ($dependencies_valid) {
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
            if (isset($details['dependencies']) && $details['dependencies'] !== '') {
                $deps = PerchUtil::json_safe_decode($details['dependencies'], true);
                if (is_array($deps)) {
                    $details['dependencies'] = PerchUtil::json_safe_encode($deps, true);
                }
            }
        } else {
            $details = array_merge($details, $data);
            $details['options'] = $options_input;
            $details['dependencies'] = $dependencies_input;
        }
    }
?>
