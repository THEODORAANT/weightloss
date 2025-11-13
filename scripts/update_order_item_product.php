<?php
declare(strict_types=1);

require_once __DIR__ . '/../perch/runtime.php';

if (!function_exists('write_to_stderr')) {
    function write_to_stderr(string $message): void
    {
        file_put_contents('php://stderr', $message, FILE_APPEND);
    }
}

if (PHP_SAPI !== 'cli') {
    write_to_stderr('This script must be run from the command line.' . PHP_EOL);
    exit(1);
}

$options = getopt('', [
    'order-id:',
    'item-id:',
    'product-id:',
    'price:',
    'total::',
    'tax::',
    'tax-rate::',
    'discount::',
    'tax-discount::',
    'qty::',
    'dry-run',
    'help',
]);

$dryRun = array_key_exists('dry-run', $options);

if (array_key_exists('help', $options)) {
    echo 'Usage: php update_order_item_product.php --order-id=<id> --item-id=<id> --product-id=<id> --price=<amount> [options]' . PHP_EOL;
    echo PHP_EOL;
    echo 'Required options:' . PHP_EOL;
    echo '  --order-id        The numeric ID of the order containing the item.' . PHP_EOL;
    echo '  --item-id         The numeric ID of the order item to update.' . PHP_EOL;
    echo '  --product-id      The product ID that should be assigned to the order item.' . PHP_EOL;
    echo '  --price           The exclusive (pre-tax) price for a single quantity of the product.' . PHP_EOL;
    echo PHP_EOL;
    echo 'Optional options:' . PHP_EOL;
    echo '  --total           Inclusive (taxed) unit price. Defaults to price plus calculated tax.' . PHP_EOL;
    echo '  --tax             Override the per-unit tax amount for the item.' . PHP_EOL;
    echo '  --tax-rate        Override the stored tax rate for the item.' . PHP_EOL;
    echo '  --discount        Override the line discount amount stored for the item.' . PHP_EOL;
    echo '  --tax-discount    Override the tax discount amount stored for the item.' . PHP_EOL;
    echo '  --qty             Override the quantity for the item (defaults to existing quantity).' . PHP_EOL;
    echo '  --dry-run         Show the changes that would be made without persisting them.' . PHP_EOL;
    echo '  --help            Display this message.' . PHP_EOL;
    exit(0);
}

$orderID = isset($options['order-id']) ? (int) $options['order-id'] : 0;
$itemID = isset($options['item-id']) ? (int) $options['item-id'] : 0;
$newProductID = isset($options['product-id']) ? (int) $options['product-id'] : 0;

if ($orderID < 1 || $itemID < 1 || $newProductID < 1 || !isset($options['price'])) {
    write_to_stderr('Missing required options. Use --help for usage information.' . PHP_EOL);
    exit(1);
}

$price = normalise_decimal($options['price']);

if ($price === null) {
    write_to_stderr('The --price value must be numeric.' . PHP_EOL);
    exit(1);
}

$DB = PerchDB::fetch();

$order = $DB->get_row(
    'SELECT orderID, orderInvoiceNumber, orderItemsSubtotal, orderItemsTax, orderTotal '
    . 'FROM ' . PERCH_DB_PREFIX . 'shop_orders '
    . 'WHERE orderID = ' . $DB->pdb($orderID)
    . ' LIMIT 1'
);

if (!$order) {
    write_to_stderr('Order #' . $orderID . ' was not found.' . PHP_EOL);
    exit(1);
}

$orderItem = $DB->get_row(
    'SELECT * FROM ' . PERCH_DB_PREFIX . 'shop_order_items '
    . 'WHERE itemID = ' . $DB->pdb($itemID)
    . ' AND orderID = ' . $DB->pdb($orderID)
    . ' LIMIT 1'
);

if (!$orderItem) {
    write_to_stderr('Order item #' . $itemID . ' was not found for order #' . $orderID . '.' . PHP_EOL);
    exit(1);
}

if ($orderItem['itemType'] !== 'product') {
    write_to_stderr('Warning: order item #' . $itemID . ' is of type "' . $orderItem['itemType'] . '".' . PHP_EOL);
}

$product = $DB->get_row(
    'SELECT productID, productTitle FROM ' . PERCH_DB_PREFIX . 'shop_products '
    . 'WHERE productID = ' . $DB->pdb($newProductID)
    . ' LIMIT 1'
);

if (!$product) {
    write_to_stderr('Product #' . $newProductID . ' does not exist.' . PHP_EOL);
    exit(1);
}

$qty = isset($options['qty']) ? (int) $options['qty'] : (int) $orderItem['itemQty'];
if ($qty < 1) {
    write_to_stderr('Quantity must be at least 1.' . PHP_EOL);
    exit(1);
}

$taxRate = array_key_exists('tax-rate', $options)
    ? trim((string) $options['tax-rate'])
    : (string) $orderItem['itemTaxRate'];

$tax = null;
if (array_key_exists('tax', $options)) {
    $tax = normalise_decimal($options['tax']);
    if ($tax === null) {
        write_to_stderr('The --tax value must be numeric.' . PHP_EOL);
        exit(1);
    }
}

if ($tax === null && $taxRate !== '') {
    $tax = calculate_tax_amount($price, $taxRate);
}

if ($tax === null) {
    $existingTotal = normalise_decimal($orderItem['itemTotal']);
    $existingPrice = normalise_decimal($orderItem['itemPrice']);
    if ($existingTotal !== null && $existingPrice !== null) {
        $tax = $existingTotal - $existingPrice;
    } else {
        $tax = 0.0;
    }
}

$total = null;
if (array_key_exists('total', $options)) {
    $total = normalise_decimal($options['total']);
    if ($total === null) {
        write_to_stderr('The --total value must be numeric.' . PHP_EOL);
        exit(1);
    }
}

if ($total === null) {
    $total = $price + $tax;
}

$discount = null;
if (array_key_exists('discount', $options)) {
    $discount = normalise_decimal($options['discount']);
    if ($discount === null) {
        write_to_stderr('The --discount value must be numeric.' . PHP_EOL);
        exit(1);
    }
} else {
    $discount = normalise_decimal($orderItem['itemDiscount']);
    if ($discount === null) {
        $discount = 0.0;
    }
}

$taxDiscount = null;
if (array_key_exists('tax-discount', $options)) {
    $taxDiscount = normalise_decimal($options['tax-discount']);
    if ($taxDiscount === null) {
        write_to_stderr('The --tax-discount value must be numeric.' . PHP_EOL);
        exit(1);
    }
} else {
    $taxDiscount = normalise_decimal($orderItem['itemTaxDiscount']);
    if ($taxDiscount === null) {
        $taxDiscount = 0.0;
    }
}

$updatePayload = [
    'productID'       => $newProductID,
    'itemPrice'       => format_decimal($price),
    'itemTotal'       => format_decimal($total),
    'itemTax'         => format_decimal($tax),
    'itemQty'         => $qty,
    'itemTaxRate'     => $taxRate,
    'itemDiscount'    => format_decimal($discount),
    'itemTaxDiscount' => format_decimal($taxDiscount),
];

$newTotals = recalculate_order_totals(
    $DB,
    $orderID,
    [
        'itemID'         => $itemID,
        'itemType'       => $orderItem['itemType'],
        'itemPrice'      => $updatePayload['itemPrice'],
        'itemTotal'      => $updatePayload['itemTotal'],
        'itemTax'        => $updatePayload['itemTax'],
        'itemQty'        => $qty,
        'itemDiscount'   => $updatePayload['itemDiscount'],
        'itemTaxDiscount'=> $updatePayload['itemTaxDiscount'],
        'itemTaxRate'    => $taxRate,
    ]
);

if ($dryRun) {
    echo '[DRY RUN] Would update order item #' . $itemID . ' in order #' . $orderID . PHP_EOL;
    echo '  Product:  #' . $newProductID . ' (' . ($product['productTitle'] ?? 'untitled') . ')' . PHP_EOL;
    echo '  Quantity: ' . $qty . PHP_EOL;
    echo '  Price:    ' . $updatePayload['itemPrice'] . PHP_EOL;
    echo '  Total:    ' . $updatePayload['itemTotal'] . PHP_EOL;
    echo PHP_EOL;
    output_totals_preview($order, $newTotals);
    exit(0);
}

$result = $DB->update(
    PERCH_DB_PREFIX . 'shop_order_items',
    $updatePayload,
    'itemID',
    $itemID
);

if (!$result) {
    write_to_stderr('Failed to update order item #' . $itemID . '.' . PHP_EOL);
    exit(1);
}

echo 'Updated order item #' . $itemID . ' in order #' . $orderID . '.' . PHP_EOL;

$orderUpdate = array_merge(
    $newTotals,
    [
        'orderUpdated' => date('Y-m-d H:i:s'),
    ]
);

$updated = $DB->update(
    PERCH_DB_PREFIX . 'shop_orders',
    $orderUpdate,
    'orderID',
    $orderID
);

if (!$updated) {
    write_to_stderr('Warning: failed to update totals for order #' . $orderID . '.' . PHP_EOL);
    exit(1);
}

echo 'Recalculated totals for order #' . $orderID . '.' . PHP_EOL;

exit(0);

function normalise_decimal($value): ?float
{
    if (is_float($value) || is_int($value)) {
        return (float) $value;
    }

    if (is_string($value)) {
        $filtered = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND);
        if ($filtered === '' || !is_numeric($filtered)) {
            return null;
        }

        return (float) $filtered;
    }

    return null;
}

function format_decimal(float $value): string
{
    if (abs($value) < 0.0005) {
        $value = 0.0;
    }

    return number_format($value, 2, '.', '');
}

function calculate_tax_amount(float $price, string $taxRate): ?float
{
    $numeric = normalise_decimal($taxRate);
    if ($numeric === null) {
        return null;
    }

    return $price * ($numeric / 100);
}

function recalculate_order_totals(PerchDB_MySQL $DB, int $orderID, ?array $override = null): array
{
    $rows = $DB->get_rows(
        'SELECT itemID, itemType, itemPrice, itemTotal, itemTax, itemDiscount, itemTaxDiscount, itemQty '
        . 'FROM ' . PERCH_DB_PREFIX . 'shop_order_items '
        . 'WHERE orderID = ' . $DB->pdb($orderID)
    );

    if (!$rows) {
        return [
            'orderItemsSubtotal'        => format_decimal(0.0),
            'orderItemsTax'             => format_decimal(0.0),
            'orderItemsTotal'           => format_decimal(0.0),
            'orderShippingSubtotal'     => format_decimal(0.0),
            'orderShippingDiscounts'    => format_decimal(0.0),
            'orderShippingTax'          => format_decimal(0.0),
            'orderShippingTaxDiscounts' => format_decimal(0.0),
            'orderShippingTotal'        => format_decimal(0.0),
            'orderDiscountsTotal'       => format_decimal(0.0),
            'orderTaxDiscountsTotal'    => format_decimal(0.0),
            'orderSubtotal'             => format_decimal(0.0),
            'orderTaxTotal'             => format_decimal(0.0),
            'orderTotal'                => format_decimal(0.0),
        ];
    }

    if ($override !== null) {
        $replaced = false;
        foreach ($rows as &$row) {
            if ((int) $row['itemID'] === (int) $override['itemID']) {
                $row = array_merge($row, $override);
                $replaced = true;
                break;
            }
        }
        unset($row);

        if (!$replaced) {
            $rows[] = $override;
        }
    }

    $itemsSubtotal = 0.0;
    $itemsTax = 0.0;
    $itemDiscounts = 0.0;
    $itemTaxDiscounts = 0.0;

    $shippingSubtotal = 0.0;
    $shippingTax = 0.0;
    $shippingDiscounts = 0.0;
    $shippingTaxDiscounts = 0.0;

    foreach ($rows as $row) {
        $type = $row['itemType'];
        $price = normalise_decimal($row['itemPrice']) ?? 0.0;
        $total = normalise_decimal($row['itemTotal']) ?? 0.0;
        $tax = normalise_decimal($row['itemTax']) ?? ($total - $price);
        $discount = normalise_decimal($row['itemDiscount']) ?? 0.0;
        $taxDiscount = normalise_decimal($row['itemTaxDiscount']) ?? 0.0;
        $qty = isset($row['itemQty']) ? (int) $row['itemQty'] : 1;

        if ($type === 'shipping') {
            $shippingSubtotal += $price * $qty;
            $shippingTax += $tax * $qty;
            $shippingDiscounts += $discount;
            $shippingTaxDiscounts += $taxDiscount;
        } elseif ($type === 'product') {
            $itemsSubtotal += $price * $qty;
            $itemsTax += ($total - $price) * $qty;
            $itemDiscounts += $discount;
            $itemTaxDiscounts += $taxDiscount;
        } elseif ($type === 'discount') {
            $itemDiscounts += $discount + ($price * $qty);
            $itemTaxDiscounts += $taxDiscount + ($tax * $qty);
        }
    }

    $orderDiscountsTotal = $itemDiscounts + $shippingDiscounts;
    $orderTaxDiscountsTotal = $itemTaxDiscounts + $shippingTaxDiscounts;

    $orderItemsSubtotal = $itemsSubtotal;
    $orderItemsTax = $itemsTax - $itemTaxDiscounts;
    $orderItemsTotal = $orderItemsSubtotal + $orderItemsTax;

    $orderShippingSubtotal = $shippingSubtotal;
    $orderShippingTax = $shippingTax - $shippingTaxDiscounts;
    $orderShippingTotal = $orderShippingSubtotal + $orderShippingTax;

    $orderSubtotal = ($orderItemsSubtotal + $orderShippingSubtotal) - $orderDiscountsTotal;
    $orderTaxTotal = $orderItemsTax + $orderShippingTax;
    $orderTotal = $orderSubtotal + $orderTaxTotal;

    return [
        'orderItemsSubtotal'        => format_decimal($orderItemsSubtotal),
        'orderItemsTax'             => format_decimal($orderItemsTax),
        'orderItemsTotal'           => format_decimal($orderItemsTotal),
        'orderShippingSubtotal'     => format_decimal($orderShippingSubtotal),
        'orderShippingDiscounts'    => format_decimal($shippingDiscounts),
        'orderShippingTax'          => format_decimal($orderShippingTax),
        'orderShippingTaxDiscounts' => format_decimal($shippingTaxDiscounts),
        'orderShippingTotal'        => format_decimal($orderShippingTotal),
        'orderDiscountsTotal'       => format_decimal($orderDiscountsTotal),
        'orderTaxDiscountsTotal'    => format_decimal($orderTaxDiscountsTotal),
        'orderSubtotal'             => format_decimal($orderSubtotal),
        'orderTaxTotal'             => format_decimal($orderTaxTotal),
        'orderTotal'                => format_decimal($orderTotal),
    ];
}

function output_totals_preview(array $original, array $updated): void
{
    echo 'Order totals before:' . PHP_EOL;
    echo '  Items subtotal: ' . ($original['orderItemsSubtotal'] ?? 'n/a') . PHP_EOL;
    echo '  Items tax:      ' . ($original['orderItemsTax'] ?? 'n/a') . PHP_EOL;
    echo '  Order total:    ' . ($original['orderTotal'] ?? 'n/a') . PHP_EOL;
    echo PHP_EOL;
    echo 'Order totals after:' . PHP_EOL;
    echo '  Items subtotal: ' . $updated['orderItemsSubtotal'] . PHP_EOL;
    echo '  Items tax:      ' . $updated['orderItemsTax'] . PHP_EOL;
    echo '  Order total:    ' . $updated['orderTotal'] . PHP_EOL;
}
