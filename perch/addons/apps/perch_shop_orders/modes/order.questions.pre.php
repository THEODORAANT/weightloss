<?php

        $Orders     = new PerchShop_Orders($API);
        $Customers  = new PerchShop_Customers($API);
        $Form       = $API->get('Form');
        $ManualQuestionForm = $API->get('Form');
        $ManualQuestionForm->set_name('manual_question');
        $ManualQuestionForm->require_field('question_type', 'Please select a questionnaire.');
        $ManualQuestionForm->require_field('question_slug', 'Please select a question.');
        $message    = false;
        $manual_question_message = false;
        $smartbar_selection = 'questions';
        $questionnaire_notes = '';

        if (!class_exists('PerchMembers_Questionnaires')) {
            include_once(PERCH_PATH.'/addons/apps/perch_members/PerchMembers_Questionnaires.class.php');
        }
        if (!function_exists('comms_service_send_order_note')) {
            require_once __DIR__ . '/../../api/routes/lib/comms_service.php';
        }
        if (!function_exists('comms_sync_order')) {
            require_once __DIR__ . '/../../api/routes/lib/comms_sync.php';
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

                        if ($note !== '' && function_exists('comms_sync_order') && function_exists('comms_service_send_order_note')) {
                            $memberID = null;
                            if (method_exists($Order, 'memberID')) {
                                $memberID = $Order->memberID();
                            }

                            $orderLinked = comms_sync_order($order_id, $memberID !== null ? (int) $memberID : null);

                            if ($orderLinked) {
                                $noteDateLabel = date('Y-m-d H:i:s');
                                $noteWithTimestamp = '[' . $noteDateLabel . '] ' . $note;

                                $nameParts = [];
                                if ($CurrentUser->userGivenName()) {
                                    $nameParts[] = $CurrentUser->userGivenName();
                                }
                                if ($CurrentUser->userFamilyName()) {
                                    $nameParts[] = $CurrentUser->userFamilyName();
                                }
                                $addedBy = trim(implode(' ', $nameParts)) ?: $CurrentUser->userUsername();

                                $notePayload = [
                                    'note' => $noteWithTimestamp,
                                    'note_raw' => $note,
                                    'note_date' => $noteDateLabel,
                                    'added_by' => $addedBy,
                                    'note_type' => 'admin_note',
                                    'body' => $note,
                                    'created_by' => [
                                        'name' => $addedBy !== '' ? $addedBy : 'Perch admin',
                                        'role' => 'admin',
                                    ],
                                ];

                                comms_service_send_order_note($order_id, $notePayload);
                            }
                        }
                    } else {
                        $message = $HTML->failure_message('Sorry, the notes could not be saved.');
                    }

                    $questionnaire_notes = $note;
                }

                $buildQuestionnaireSections = function () use ($Questionnaires, $order_id, $Lang) {
                    return [
                        [
                            'type'      => 'first-order',
                            'title'     => $Lang->get('First-order questionnaire'),
                            'questions' => $Questionnaires->get_questions('first-order'),
                            'question_configs' => $Questionnaires->get_questions_answers('first-order'),
                            'answers'   => $Questionnaires->get_for_order($order_id, 'first-order'),
                        ],
                        [
                            'type'      => 're-order',
                            'title'     => $Lang->get('Re-order questionnaire'),
                            'questions' => $Questionnaires->get_questions('re-order'),
                            'question_configs' => $Questionnaires->get_questions_answers('re-order'),
                            'answers'   => $Questionnaires->get_for_order($order_id, 're-order'),
                        ],
                    ];
                };

                $questionnaire_sections = $buildQuestionnaireSections();

                if ($ManualQuestionForm->submitted()) {
                    $postvars = ['question_type', 'question_slug', 'manual_answer_text', 'manual_answer_choice'];
                    $manual_data = $ManualQuestionForm->receive($postvars);

                    $question_type = isset($manual_data['question_type']) ? trim((string) $manual_data['question_type']) : '';
                    $question_slug = isset($manual_data['question_slug']) ? trim((string) $manual_data['question_slug']) : '';
                    $answer_text_input   = isset($manual_data['manual_answer_text']) ? trim((string) $manual_data['manual_answer_text']) : '';
                    $answer_choice_input = isset($manual_data['manual_answer_choice']) ? trim((string) $manual_data['manual_answer_choice']) : '';

                    $section_lookup = [];
                    foreach ($questionnaire_sections as $section) {
                        $section_lookup[$section['type']] = $section;
                    }

                    if ($question_type === '' || !isset($section_lookup[$question_type])) {
                        $ManualQuestionForm->error = true;
                        $ManualQuestionForm->messages['question_type'] = 'Please select a valid questionnaire.';
                        $manual_question_message = $HTML->failure_message('Please select a valid questionnaire.');
                    } elseif ($question_slug === '') {
                        $ManualQuestionForm->error = true;
                        $ManualQuestionForm->messages['question_slug'] = 'Please select a valid question.';
                        $manual_question_message = $HTML->failure_message('Please select a valid question.');
                    } else {
                        $available_questions = $section_lookup[$question_type]['questions'];

                        if (!is_array($available_questions) || !isset($available_questions[$question_slug])) {
                            $ManualQuestionForm->error = true;
                            $ManualQuestionForm->messages['question_slug'] = 'Please select a valid question.';
                            $manual_question_message = $HTML->failure_message('Please select a valid question.');
                        } else {
                            $question_configs = $section_lookup[$question_type]['question_configs'] ?? [];
                            $question_options = [];

                            if (
                                isset($question_configs[$question_slug]['options'])
                                && is_array($question_configs[$question_slug]['options'])
                            ) {
                                $question_options = $question_configs[$question_slug]['options'];
                            }

                            $answer_text = '';
                            if (PerchUtil::count($question_options)) {
                                if (!array_key_exists($answer_choice_input, $question_options)) {
                                    $ManualQuestionForm->error = true;
                                    $ManualQuestionForm->messages['manual_answer_choice'] = 'Please select an answer.';
                                    $manual_question_message = $HTML->failure_message('Please select an answer.');
                                } else {
                                    $answer_text = $answer_choice_input;
                                }
                            } else {
                                $answer_text = $answer_text_input;
                            }

                            if ($ManualQuestionForm->error) {
                                // Stop further processing when answer validation fails.
                            } else {
                            $existing_answers = $section_lookup[$question_type]['answers'];
                            $existing_slugs = [];
                            $existing_uuid = '';

                            if (PerchUtil::count($existing_answers)) {
                                foreach ($existing_answers as $ExistingAnswer) {
                                    $slug = $ExistingAnswer->question_slug();
                                    if ($slug !== null && $slug !== '') {
                                        $existing_slugs[$slug] = true;
                                    }

                                    if ($existing_uuid === '') {
                                        $candidate_uuid = trim((string) $ExistingAnswer->uuid());
                                        if ($candidate_uuid !== '') {
                                            $existing_uuid = $candidate_uuid;
                                        }
                                    }
                                }
                            }

                            if (isset($existing_slugs[$question_slug])) {
                                $ManualQuestionForm->error = true;
                                $ManualQuestionForm->messages['question_slug'] = 'This question already has an answer.';
                                $manual_question_message = $HTML->failure_message('This question already has an answer for this order.');
                            } elseif ($answer_text === '') {
                                $ManualQuestionForm->error = true;
                                $ManualQuestionForm->messages['manual_answer_text'] = 'Please enter an answer.';
                                $manual_question_message = $HTML->failure_message('Please enter an answer.');
                            } else {
                                $uuid = $existing_uuid;

                                if ($uuid === '' && isset($dynamic_fields['uuid']) && is_string($dynamic_fields['uuid'])) {
                                    $uuid_candidate = trim((string) $dynamic_fields['uuid']);
                                    if ($uuid_candidate !== '') {
                                        $uuid = $uuid_candidate;
                                    }
                                }

                                if ($uuid === '') {
                                    $uuid = 'manual-'.$order_id;
                                }

                                $question_order = null;
                                if (method_exists($Questionnaires, 'question_order_column_is_available') && $Questionnaires->question_order_column_is_available()) {
                                    if (method_exists($Questionnaires, 'get_question_order_for_slug')) {
                                        $question_order = $Questionnaires->get_question_order_for_slug($question_type, $question_slug);
                                    }
                                }

                                $member_id = null;
                                if ($Customer && $Customer->memberID()) {
                                    $member_id = (int) $Customer->memberID();
                                    if ($member_id <= 0) {
                                        $member_id = null;
                                    }
                                }

                                $insert_data = [
                                    'type'           => $question_type,
                                    'question_slug'  => $question_slug,
                                    'question_text'  => $available_questions[$question_slug],
                                    'answer'         => $answer_text,
                                    'answer_text'    => $answer_text,
                                    'member_id'      => $member_id,
                                    'order_id'       => $order_id,
                                    'version'        => 'v1',
                                    'uuid'           => $uuid,
                                    'created_at'     => date('Y-m-d H:i:s'),
                                ];

                                if ($question_order !== null) {
                                    $insert_data['question_order'] = (int) $question_order;
                                }

                                $ManualQuestionForm->error = false;
                                $manual_question_message = false;

                                $CreatedAnswer = $Questionnaires->create($insert_data);

                                if ($CreatedAnswer) {
                                    $ManualQuestionForm->clear();
                                    $manual_question_message = $HTML->success_message('The answer has been added to this order.');
                                    $questionnaire_sections = $buildQuestionnaireSections();
                                } else {
                                    $ManualQuestionForm->error = true;
                                    $manual_question_message = $HTML->failure_message('Sorry, the answer could not be saved.');
                                }
                            }
                            }
                        }
                    }
                }

        }else{
            PerchUtil::redirect($API->app_path());
        }
