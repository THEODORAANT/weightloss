<?php
include(__DIR__ .'/../../../../core/runtime/runtime.php');

require_once __DIR__ . '/../auth.php';

$token = get_bearer_token();
$payload = verify_token($token);

if (!$payload) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$details = perch_member_profile($payload['user_id']);

if ($details) {
    // Fetch shipping address if available
    $API = new PerchAPI(1.0, 'perch_shop');
    $Customers = new PerchShop_Customers($API);
    $Customer = $Customers->find_by_memberID((int) $payload['user_id']);

    if ($Customer instanceof PerchShop_Customer) {
        $Addresses = new PerchShop_Addresses($API);
        $ShippingAddress = $Addresses->find_for_customer($Customer->id(), 'shipping');

        if ($ShippingAddress) {
            $addressData = $ShippingAddress->to_array();

            // Add shipping address fields to the response
            if (isset($addressData['addressDynamicFields'])) {
                $dynamicFields = json_decode($addressData['addressDynamicFields'], true);
                if (is_array($dynamicFields)) {
                    foreach ($dynamicFields as $key => $value) {
                        $details['shipping_' . $key] = $value;
                    }
                }
            }
        }
    }

    echo json_encode($details);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Member not found"]);
}
