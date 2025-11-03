<?php

    $Members = new PerchMembers_Members($API);
    $MemberDocuments = new PerchMembers_Documents($API);
    $Users = new PerchUsers();

    $HTML = $API->get('HTML');
    $Lang = $API->get('Lang');

    $AssignmentForm = $API->get('Form');
    $AssignmentForm->set_name('assign_documents');

    $message = false;

    if ($AssignmentForm->posted()) {
        $assignment = PerchRequest::post('assignment', []);
        if (!is_array($assignment)) {
            $assignment = [];
        }

        $updated = false;

        foreach ($assignment as $memberID => $userID) {
            $memberID = (int)$memberID;
            if ($memberID < 1) {
                continue;
            }

            $Member = $Members->find($memberID);
            if (!is_object($Member)) {
                continue;
            }

            $properties = PerchUtil::json_safe_decode($Member->memberProperties(), true);
            if (!is_array($properties)) {
                $properties = [];
            }

            $userID = trim($userID);
            if ($userID === '') {
                if (isset($properties['document_reviewer_id'])) {
                    unset($properties['document_reviewer_id']);
                    $Member->update(['memberProperties' => PerchUtil::json_safe_encode($properties)]);
                    $updated = true;
                }
                continue;
            }

            $userID = (int)$userID;

            $User = $Users->find($userID);
            if (!is_object($User) || !$User->userEnabled()) {
                continue;
            }

            if (!isset($properties['document_reviewer_id']) || (int)$properties['document_reviewer_id'] !== $userID) {
                $properties['document_reviewer_id'] = $userID;
                $Member->update(['memberProperties' => PerchUtil::json_safe_encode($properties)]);
                $updated = true;
            }
        }

        $redirect_target ='/perch'. $API->app_nav().'/document-review/?updated=' . ($updated ? '1' : '0');
        PerchUtil::redirect($redirect_target);
    }

    $members = $Members->get_with_pending_documents($Paging);
    $member_ids = [];

    if (PerchUtil::count($members)) {
        foreach ($members as $Member) {
            $member_ids[] = (int)$Member->id();
        }
    }

    $documents_by_member = $MemberDocuments->get_pending_for_members($member_ids);

    $user_choices = [];
    $users_by_id = [];

    $all_users = $Users->all();
    if (PerchUtil::count($all_users)) {
        foreach ($all_users as $User) {
            if (!$User->userEnabled()) {
                continue;
            }

            $name_parts = [];
            if ($User->userGivenName()) {
                $name_parts[] = $User->userGivenName();
            }
            if ($User->userFamilyName()) {
                $name_parts[] = $User->userFamilyName();
            }

            $label = trim(implode(' ', $name_parts));
            if ($label === '') {
                $label = $User->userUsername();
            }

            if ($User->userEmail()) {
                $label .= ' ('.$User->userEmail().')';
            }

            $user_choices[] = [
                'id'    =>(int) $User->id(),
                'label' => $label,
            ];

            $users_by_id[(int)$User->id()] = $User;
        }
    }

    $updated_flag = PerchRequest::get('updated');
    if ($updated_flag !== false && $updated_flag !== null && $updated_flag !== '') {
        if ($updated_flag === '1') {
            $message = $HTML->success_message($Lang->get('Assignments updated'));
        } else {
            $message = $HTML->warning_message($Lang->get('No changes to save'));
        }
    }
