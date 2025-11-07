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

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON payload"]);
    exit;
}

$shippingFields = [
    'first_name',
    'last_name',
    'company',
    'address_1',
    'address_2',
    'postcode',
    'city',
    'county',
    'country',
    'phone',
    'instructions',
];

$shippingData = [];
foreach ($shippingFields as $field) {
    $inputKey = 'shipping_' . $field;
    if (array_key_exists($inputKey, $data)) {
        $value = $data[$inputKey];

        if (is_string($value)) {
            $value = trim($value);
        } elseif (is_null($value)) {
            $value = '';
        } elseif (is_scalar($value)) {
            $value = (string) $value;
        } else {
            http_response_code(400);
            echo json_encode(["error" => sprintf('Invalid value for %s', $inputKey)]);
            exit;
        }

        $shippingData[$field] = $value;
    }
}

if (!count($shippingData)) {
    http_response_code(400);
    echo json_encode(["error" => "No shipping address data supplied"]);
    exit;
}

$requiredFields = [
    'first_name' => 'shipping_first_name',
    'last_name' => 'shipping_last_name',
    'address_1' => 'shipping_address_1',
    'postcode' => 'shipping_postcode',
    'country' => 'shipping_country',
];

foreach ($requiredFields as $field => $inputKey) {
    if (!isset($shippingData[$field]) || $shippingData[$field] === '') {
        http_response_code(400);
        echo json_encode(["error" => sprintf('%s is required', $inputKey)]);
        exit;
    }
}

if (!isset($shippingData['country']) || $shippingData['country'] === '' || !is_numeric($shippingData['country'])) {
    http_response_code(400);
    echo json_encode(["error" => "shipping_country must be a numeric country ID"]);
    exit;
}

$countryID = (int) $shippingData['country'];
if ($countryID <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "shipping_country must be a positive numeric country ID"]);
    exit;
}
$shippingData['country'] = (string) $countryID;

$API = new PerchAPI(1.0, 'perch_shop');
$Customers = new PerchShop_Customers($API);
$Customer = $Customers->find_by_memberID((int) $payload['user_id']);

if (!$Customer instanceof PerchShop_Customer) {
    http_response_code(404);
    echo json_encode(["error" => "Customer not found"]);
    exit;
}

$ShopRuntime = PerchShop_Runtime::fetch();
$ShippingAddress = $ShopRuntime->update_shipping_address_for_api($Customer, $shippingData, $countryID);

if (!$ShippingAddress instanceof PerchShop_Address) {
    http_response_code(500);
    echo json_encode(["error" => "Unable to store shipping address"]);
    exit;
}

$ShopRuntime->update_member_shipping_profile((int) $payload['user_id'], $shippingData);

$responseAddress = [];
foreach ($shippingData as $field => $value) {
    if ($field === 'country') {
        $value = $countryID;
    }
    $responseAddress['shipping_' . $field] = $value;
}

http_response_code(200);
echo json_encode([
    "success" => true,
    "address" => $responseAddress,
]);
