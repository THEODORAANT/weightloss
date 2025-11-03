<?php
if (!function_exists('perch_members_update_order_document_assignments')) {
    function perch_members_update_order_document_assignments($memberID, $userID, PerchShop_Orders $Orders)
    {
        $memberID = (int)$memberID;
        if ($memberID < 1) {
            return false;
        }

        $updated = false;
        $orders = $Orders->get_pending_document_orders_for_member($memberID);

        if (PerchUtil::count($orders)) {
            foreach ($orders as $Order) {
                $fields = PerchUtil::json_safe_decode($Order->orderDynamicFields(), true);
                if (!is_array($fields)) {
                    $fields = [];
                }

                if ($userID === null) {
                    if (isset($fields['document_reviewer_id'])) {
                        unset($fields['document_reviewer_id']);
                        $Order->update(['orderDynamicFields' => PerchUtil::json_safe_encode($fields)]);
                        $updated = true;
                    }
                } else {
                    if (!isset($fields['document_reviewer_id']) || (int)$fields['document_reviewer_id'] !== (int)$userID) {
                        $fields['document_reviewer_id'] = (int)$userID;
                        $Order->update(['orderDynamicFields' => PerchUtil::json_safe_encode($fields)]);
                        $updated = true;
                    }
                }
            }
        }

        return $updated;
    }
}

if (!function_exists('perch_members_sync_member_document_assignment')) {
    function perch_members_sync_member_document_assignment($memberID, PerchMembers_Members $Members, PerchShop_Orders $Orders)
    {
        $memberID = (int)$memberID;
        if ($memberID < 1) {
            return false;
        }

        $Member = $Members->find($memberID);
        if (!$Member) {
            return false;
        }

        $properties = PerchUtil::json_safe_decode($Member->memberProperties(), true);
        if (!is_array($properties)) {
            $properties = [];
        }

        $orders = $Orders->get_pending_document_orders_for_member($memberID);
        $assigned_ids = [];

        if (PerchUtil::count($orders)) {
            foreach ($orders as $Order) {
                $fields = PerchUtil::json_safe_decode($Order->orderDynamicFields(), true);
                if (is_array($fields) && isset($fields['document_reviewer_id']) && $fields['document_reviewer_id'] !== '') {
                    $assigned_ids[] = (int)$fields['document_reviewer_id'];
                }
            }
        }

        $assigned_ids = array_values(array_unique(array_filter($assigned_ids, function ($value) {
            return $value !== null && $value !== '';
        })));

        if (count($assigned_ids) === 1) {
            $user_id = $assigned_ids[0];
            if (!isset($properties['document_reviewer_id']) || (int)$properties['document_reviewer_id'] !== $user_id) {
                $properties['document_reviewer_id'] = $user_id;
                $Member->update(['memberProperties' => PerchUtil::json_safe_encode($properties)]);
                return true;
            }
        } else {
            if (isset($properties['document_reviewer_id'])) {
                unset($properties['document_reviewer_id']);
                $Member->update(['memberProperties' => PerchUtil::json_safe_encode($properties)]);
                return true;
            }
        }

        return false;
    }
}
