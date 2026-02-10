<?php
include(__DIR__ .'/../../../../core/runtime/runtime.php');
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/lib/product_format.php';

$token = get_bearer_token();
$payload = verify_token($token);

if (!$payload) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$member_id = $payload['user_id'];
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

switch ($method) {
    case 'GET':
        wl_handle_cart_get($member_id);
        break;
    case 'POST':
        wl_handle_cart_post($member_id);
        break;
    case 'DELETE':
        wl_handle_cart_delete($member_id);
        break;
    default:
        header('Allow: GET, POST, DELETE');
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        exit;
}

function wl_handle_cart_get($member_id)
{
    $result = perch_shop_get_cart_for_api($member_id);

    if (!$result['cart_id']) {
        echo json_encode(["cart_id" => null, "items" => []]);
        exit;
    }

    $items = [];
    if (!empty($result['raw_items'])) {
        foreach ($result['raw_items'] as $item) {
            $productData = [];
            if (isset($item['Product']) && $item['Product'] instanceof PerchShop_Product) {
                $productData = $item['Product']->to_array_for_api();
            }

            $image = wl_format_image($productData['image'] ?? null);

            $items[] = [
                'productID'    => (int)$item['id'],
                'productTitle' => $item['title'] ?? '',
                'productImage' => $image['url'] ?? null,
                'productSku'   => $item['sku'] ?? '',
                'variantLabel' => $productData['productVariantDesc'] ?? '',
                'itemPrice'    => isset($item['price_with_tax']) ? number_format((float)$item['price_with_tax'], 2, '.', '') : '0.00',
                'itemQty'      => (int)$item['qty'],
            ];
        }
    }

    echo json_encode(["cart_id" => $result['cart_id'], "items" => $items]);
    exit;
}

function wl_handle_cart_post($member_id)
{
    $data = json_decode(file_get_contents('php://input'), true);
    $product = $data["product"];
    $qty = $data["qty"];
    $cart_id = isset($data["cartID"]) ? (int)$data["cartID"] : 0;
    $cart_id = perch_shop_add_to_cart_for_api($member_id, $product, $qty, $cart_id);

    if (!empty($errors)) {
        http_response_code(500);
        echo json_encode(["errors" => $errors]);
    } else {
        echo json_encode(["cart_id" => $cart_id]);
    }
    exit;
}

function wl_handle_cart_delete($member_id)
{
    perch_shop_clear_cart_for_api($member_id);
    echo json_encode(["success" => true]);
    exit;
}
?>
