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

    $selected_type = isset($_GET['type']) ? trim((string)$_GET['type']) : '';
    $selected_product = isset($_GET['product']) ? trim((string)$_GET['product']) : '';

    $questions = $Questions->all();

    $product_options = ['all' => 'All products'];
    if (PerchUtil::count($questions)) {
        foreach ($questions as $Question) {
            $product = $Question->productSlug();
            if ($product === null || $product === '') {
                continue;
            }
            $product_options[$product] = $product;
        }
    }

    if ($selected_type !== '' || $selected_product !== '') {
        $questions = array_values(array_filter($questions, function ($Question) use ($selected_type, $selected_product) {
            if ($selected_type !== '' && $Question->questionnaireType() !== $selected_type) {
                return false;
            }

            if ($selected_product !== '') {
                $product = $Question->productSlug();
                $normalized = ($product === null || $product === '') ? 'all' : $product;
                if ($normalized !== $selected_product) {
                    return false;
                }
            }

            return true;
        }));
    }

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
?>
