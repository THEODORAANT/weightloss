<?php

require_once __DIR__ . '/comms_service.php';

function comms_sync_member(int $memberID): bool
{
    $memberData = perch_member_profile($memberID);
    if (!is_array($memberData)) {
        $memberData = [];
    }

    return comms_service_link_member($memberID, $memberData);
}

function comms_sync_order(int $orderID, ?int $memberID = null): bool
{
    $API = new PerchAPI(1.0, 'perch_shop');
    $Orders = new PerchShop_Orders($API);
    $Order = $Orders->find($orderID);
    if (!$Order instanceof PerchShop_Order) {
        return false;
    }

    $orderData = $Order->to_array();
    if ($memberID !== null) {
        $orderData['memberID'] = $memberID;
    }

    return comms_service_link_order($orderID, $orderData);
}
