<?php

        $Members = new PerchMembers_Members($API);
        $pending_mod_count = $Members->get_count('pending');

        $MemberDocuments = new PerchMembers_Documents($API);
        $pending_document_count = $MemberDocuments->count_by_status('pending');
        $chat_unread_count = 0;
        try {
            $ChatNavRepo = new PerchMembers_ChatRepository($API);
            $chat_unread_count = $ChatNavRepo->count_unread_for_staff();
        } catch (Throwable $ignored) {
            $chat_unread_count = 0;
        }

        PerchUI::set_subnav([
                ['page'=>[
                                        'perch_members/?sort=^memberCreated',
                                        'perch_members/delete',
                                        'perch_members/edit',
                        ], 'label'=>'Members', 'badge'=>$pending_mod_count, 'priv'=>'perch_members.moderate'],
                ['page'=>[
                                        'perch_members/chat',
                                        'perch_members/chat/thread',
                        ], 'label'=>'Chat', 'badge'=>$chat_unread_count, 'priv'=>'perch_members.moderate'],
                ['page'=>[
                                        'perch_members/document-review',
                        ], 'label'=>'Document approvals', 'badge'=>$pending_document_count, 'priv'=>'perch_members.moderate'],
                ['page'=>[
                                        'perch_members/affiliates',
                                        'perch_members/affiliates/edit',
			], 'label'=>'Affiliates', 'priv'=>'perch_members.affiliates.manage'],
					['page'=>[
            					'perch_members/affiliates/payouts',
            					'perch_members/affiliates/payouts',
            			], 'label'=>'Affiliate Payouts', 'priv'=>'perch_members.affiliates.manage'],
                ['page'=>[
                                        'perch_members/forms',
                                        'perch_members/forms/edit',
                        ], 'label'=>'Forms',  'priv'=>'perch_members.forms.manage'],
                ['page'=>[
                                        'perch_members/questionnaire_questions',
                                        'perch_members/questionnaire_questions/edit',
                                        'perch_members/questionnaire_questions/flowchart',
                        ], 'label'=>'Questionnaires',  'priv'=>'perch_members.questionnaires.manage'],
        ], $CurrentUser);
