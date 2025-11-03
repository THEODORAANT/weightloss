<?php

        $Orders     = new PerchShop_Orders($API);
        $Customers  = new PerchShop_Customers($API);
        $Form       = $API->get('Form');
        $message    = false;
        $smartbar_selection = 'questions';
        $questionnaire_notes = '';

        if (!class_exists('PerchMembers_Questionnaires')) {
            include_once(PERCH_PATH.'/addons/apps/perch_members/PerchMembers_Questionnaires.class.php');
        }

        $MembersAPI = new PerchAPI(1.0, 'perch_members');
        $Questionnaires = new PerchMembers_Questionnaires($MembersAPI);

        $questionnaire_sections = [];

        if (PerchUtil::get('id')) {

                if (!$CurrentUser->has_priv('perch_shop.orders.edit')) {
                    PerchUtil::redirect($API->app_path());
                }

                $order_id = (int) PerchUtil::get('id');

                $Order = $Orders->find($order_id);

                if (!$Order) {
                    PerchUtil::redirect($API->app_path());
                }

                $Customer = $Customers->find($Order->customerID());

                $dynamic_fields = PerchUtil::json_safe_decode($Order->orderDynamicFields(), true);
                if (!is_array($dynamic_fields)) {
                    $dynamic_fields = [];
                }

                if (isset($dynamic_fields['questionnaire_notes'])) {
                    $questionnaire_notes = (string) $dynamic_fields['questionnaire_notes'];
                }

                if ($Form->submitted()) {
                    $postvars = ['questionnaire_notes'];
                    $data = $Form->receive($postvars);

                    $note = isset($data['questionnaire_notes']) ? trim((string) $data['questionnaire_notes']) : '';

                    $dynamic_fields['questionnaire_notes'] = $note;

                    $updated = $Order->update([
                        'orderDynamicFields' => PerchUtil::json_safe_encode($dynamic_fields),
                    ]);

                    if ($updated) {
                        $message = $HTML->success_message('Questionnaire notes have been saved.');
                    } else {
                        $message = $HTML->failure_message('Sorry, the notes could not be saved.');
                    }

                    $questionnaire_notes = $note;
                }

                $questionnaire_sections = [
                    [
                        'type'      => 'first-order',
                        'title'     => $Lang->get('First-order questionnaire'),
                        'questions' => $Questionnaires->get_questions('first-order'),
                        'answers'   => $Questionnaires->get_for_order($order_id, 'first-order'),
                    ],
                    [
                        'type'      => 're-order',
                        'title'     => $Lang->get('Re-order questionnaire'),
                        'questions' => $Questionnaires->get_questions('re-order'),
                        'answers'   => $Questionnaires->get_for_order($order_id, 're-order'),
                    ],
                ];

        }else{
            PerchUtil::redirect($API->app_path());
        }

