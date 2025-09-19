<?php
    $HTML = $API->get('HTML');

    $Questions = new PerchMembers_QuestionnaireQuestions($API);

    $Paging = $API->get('Paging');
    $Paging->set_per_page(20);

    $Form = $API->get('Form');

    if ($Form->submitted() && isset($_POST['btnSaveOrder'])) {
        $items = $Form->find_items('order-');
        $updated = false;

        if (PerchUtil::count($items)) {
            foreach ($items as $questionID => $order) {
                $Question = $Questions->find((int)$questionID);
                if (!$Question) continue;

                $order = (int)$order;
                if ($order < 0) {
                    $order = $Questions->get_next_sort_for_type($Question->questionnaireType());
                }

                if ((int)$Question->sort() !== $order) {
                    $Question->update(['sort' => $order]);
                    $updated = true;
                }
            }
        }

        if ($updated) {
            $message = $HTML->success_message($Lang->get('Question order has been updated.'));
        }
    }

    $questions = $Questions->all();

    $question_groups = [];
    if (PerchUtil::count($questions)) {
        foreach ($questions as $Question) {
            $type = $Question->questionnaireType();
            if (!isset($question_groups[$type])) {
                $question_groups[$type] = [];
            }
            $question_groups[$type][] = $Question;
        }
    }


    $questions = $Questions->all();
?>
