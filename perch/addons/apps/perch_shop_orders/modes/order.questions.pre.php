<?php

        $Orders     = new PerchShop_Orders($API);
        $Customers  = new PerchShop_Customers($API);
        $Form       = $API->get('Form');
        $message    = false;
        $smartbar_selection = 'questions';

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

