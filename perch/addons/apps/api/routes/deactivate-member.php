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

$method = $_SERVER['REQUEST_METHOD'] ?? 'POST';
if ($method !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

$memberId = (int) $payload['user_id'];
if ($memberId <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid member ID"]);
    exit;
}

$MembersApi = new PerchAPI(1.0, 'perch_members');
$Members = new PerchMembers_Members($MembersApi);
$Member = $Members->find($memberId);

if (!$Member instanceof PerchMembers_Member) {
    http_response_code(404);
    echo json_encode(["error" => "Member not found"]);
    exit;
}

$memberUpdated = $Member->update(['memberStatus' => 'inactive']);

if (!$memberUpdated) {
    http_response_code(500);
    echo json_encode(["error" => "Unable to deactivate member"]);
    exit;
}

$customerId = null;
$customerDeactivated = false;

$ShopApi = new PerchAPI(1.0, 'perch_shop');
$Customers = new PerchShop_Customers($ShopApi);
$Customer = $Customers->find_by_memberID($memberId);

if ($Customer instanceof PerchShop_Customer) {
    $customerId = (int) $Customer->id();
    $customerDeactivated = $Customer->delete();
}

echo json_encode([
    'success' => true,
    'member_id' => $memberId,
    'member_status' => 'inactive',
    'customer_id' => $customerId,
    'customer_deactivated' => $customerDeactivated,
]);
