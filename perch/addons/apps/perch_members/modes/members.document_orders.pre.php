<?php
    include_once(__DIR__.'/_document_assignment_helpers.php');

    $Members = new PerchMembers_Members($API);
    $MemberDocuments = new PerchMembers_Documents($API);
    $Orders = new PerchShop_Orders($API);
    $Customers = new PerchShop_Customers($API);
    $Users = new PerchUsers();

    $document_review_view = 'orders';
    $document_review_heading = $Lang->get('Orders awaiting document approval');

    $OrderAssignmentForm = $API->get('Form');
    $OrderAssignmentForm->set_name('assign_order_documents');

    $message = false;

    $user_choices = [];
    $user_labels_by_id = [];
    $enabled_users = [];

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
                'id'    => (int)$User->id(),
                'label' => $label,
            ];

            $user_labels_by_id[(int)$User->id()] = $label;
            $enabled_users[(int)$User->id()] = $User;
        }
    }

    if ($OrderAssignmentForm->posted()) {
        $assignment = PerchRequest::post('order_assignment', []);
        if (!is_array($assignment)) {
            $assignment = [];
        }

        $updated = false;
        $members_to_sync = [];

        foreach ($assignment as $orderID => $userID) {
            $orderID = (int)$orderID;
            if ($orderID < 1) {
                continue;
            }

            $Order = $Orders->find($orderID);
            if (!is_object($Order)) {
                continue;
            }

            $userID = trim($userID);
            $target_user_id = null;

            if ($userID !== '') {
                $userID = (int)$userID;
                if (!isset($enabled_users[$userID])) {
                    continue;
                }
                $target_user_id = $userID;
            }

            $fields = PerchUtil::json_safe_decode($Order->orderDynamicFields(), true);
            if (!is_array($fields)) {
                $fields = [];
            }

            if ($target_user_id === null) {
                if (isset($fields['document_reviewer_id'])) {
                    unset($fields['document_reviewer_id']);
                    $Order->update(['orderDynamicFields' => PerchUtil::json_safe_encode($fields)]);
                    $updated = true;
                }
            } else {
                if (!isset($fields['document_reviewer_id']) || (int)$fields['document_reviewer_id'] !== $target_user_id) {
                    $fields['document_reviewer_id'] = $target_user_id;
                    $Order->update(['orderDynamicFields' => PerchUtil::json_safe_encode($fields)]);
                    $updated = true;
                }
            }

            $customerID = (int)$Order->customerID();
            if ($customerID > 0) {
                $Customer = $Customers->find($customerID);
                if ($Customer && $Customer->memberID()) {
                    $members_to_sync[(int)$Customer->memberID()] = true;
                }
            }
        }

        if (PerchUtil::count($members_to_sync)) {
            foreach (array_keys($members_to_sync) as $memberID) {
                if (perch_members_sync_member_document_assignment($memberID, $Members, $Orders)) {
                    $updated = true;
                }
            }
        }

        $redirect_target = '/perch'.$API->app_nav().'/document-review/?view=orders&updated='.($updated ? '1' : '0');
        PerchUtil::redirect($redirect_target);
    }

    $orders = $Orders->get_with_pending_documents($Paging);

    $customers_by_id = [];
    $members_by_id = [];
    $order_member_map = [];
    $member_ids = [];
    $order_assignments = [];

    if (PerchUtil::count($orders)) {
        foreach ($orders as $Order) {
            $order_id = (int)$Order->id();
            $fields = PerchUtil::json_safe_decode($Order->orderDynamicFields(), true);
            if (is_array($fields) && isset($fields['document_reviewer_id']) && $fields['document_reviewer_id'] !== '') {
                $order_assignments[$order_id] = (int)$fields['document_reviewer_id'];
            }

            $customerID = (int)$Order->customerID();
            if ($customerID > 0) {
                if (!isset($customers_by_id[$customerID])) {
                    $customers_by_id[$customerID] = $Customers->find($customerID);
                }

                $Customer = $customers_by_id[$customerID];
                if ($Customer && $Customer->memberID()) {
                    $memberID = (int)$Customer->memberID();
                    $order_member_map[$order_id] = $memberID;
                    $member_ids[$memberID] = $memberID;

                    if (!isset($members_by_id[$memberID])) {
                        $members_by_id[$memberID] = $Members->find($memberID);
                    }
                }
            }
        }
    }

    $documents_by_member = [];
    if (PerchUtil::count($member_ids)) {
        $documents_by_member = $MemberDocuments->get_pending_for_members(array_values($member_ids));
    }

    $updated_flag = PerchRequest::get('updated');
    if ($updated_flag !== false && $updated_flag !== null && $updated_flag !== '') {
        if ($updated_flag === '1') {
            $message = $HTML->success_message($Lang->get('Assignments updated'));
        } else {
            $message = $HTML->warning_message($Lang->get('No changes to save'));
        }
    }
