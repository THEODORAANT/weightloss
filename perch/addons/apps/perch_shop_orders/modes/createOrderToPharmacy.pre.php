<?php

$success = false;
$message = '';
$script_output = '';

$Orders = new PerchShop_Orders($API);

if (PerchUtil::get('id')) {
    $shop_id = PerchUtil::get('id');
    $Order = $Orders->find($shop_id);

    if (is_object($Order)) {
        $order_id = (int)$Order->id();
        $php_binary = defined('PHP_BINARY') && PHP_BINARY ? PHP_BINARY : 'php';
        $script_path = realpath(__DIR__ . '/../../../../../scripts/send_specific_orders_to_pharmacy.php');

        if ($script_path && file_exists($script_path)) {
            $command = escapeshellcmd($php_binary)
                . ' ' . escapeshellarg($script_path)
                . ' --orders=' . escapeshellarg((string)$order_id)
                . ' 2>&1';

            $script_output = (string)shell_exec($command);

            if ($script_output !== '' && stripos($script_output, 'Done.') !== false) {
                $success = true;
                $message = 'Order sent to pharmacy successfully.';
            } else {
                $message = 'Unable to create pharmacy order.';
            }
        } else {
            $message = 'Pharmacy script not found.';
        }
    } else {
        $message = 'Order not found.';
    }
}
