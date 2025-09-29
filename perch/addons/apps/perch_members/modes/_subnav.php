<?php

	$Members = new PerchMembers_Members($API);
	$pending_mod_count = $Members->get_count('pending');

	PerchUI::set_subnav([
		['page'=>[
					'perch_members/?sort=^memberCreated',
					'perch_members/delete',
					'perch_members/edit',
			], 'label'=>'Members', 'badge'=>$pending_mod_count, 'priv'=>'perch_members.moderate'],
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
