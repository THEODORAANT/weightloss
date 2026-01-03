<?php
//include(__DIR__ . '/../../../../core/inc/api.php');

$API = new PerchAPI(1.0, 'perch_shop');
$DB  = PerchDB::fetch();
    $orders_table    = PERCH_DB_PREFIX . 'shop_orders';
    $customers_table = PERCH_DB_PREFIX . 'shop_customers';
    $pharmacy_table  = PERCH_DB_PREFIX . 'orders_match_pharmacy';

    $latest_pharmacy_sql = sprintf(
        'SELECT p1.* FROM %1$s p1'
            . ' INNER JOIN ('
            . ' SELECT orderID, MAX(created_at) AS latest_created_at'
            . ' FROM %1$s'
            . " WHERE pharmacy_orderID IS NOT NULL AND pharmacy_orderID <> ''"
            . ' GROUP BY orderID'
            . ' ) latest ON latest.orderID = p1.orderID AND latest.latest_created_at = p1.created_at',
        $pharmacy_table
    );

    $status_expression = 'LOWER(COALESCE(p.status, p.pharmacy_status, p.order_status))';

    $pharmacy_pending_sql = 'SELECT o.orderID, o.orderInvoiceNumber, o.orderStatus, o.orderCreated,'
        . ' c.customerFirstName, c.customerLastName, c.customerEmail,'
        . ' p.pharmacy_orderID, p.created_at,'
        . ' COALESCE(p.status, p.pharmacy_status, p.order_status) AS pharmacy_status,'
        . ' COALESCE(p.trackingno, p.tracking_no, p.trackingnumber, p.tracking_number, p.trackingref, p.tracking_reference) AS tracking_number'
        . ' FROM ' . $orders_table . ' o'
        . ' INNER JOIN (' . $latest_pharmacy_sql . ') p ON p.orderID = o.orderID'
        . ' INNER JOIN ' . $customers_table . ' c ON c.customerID = o.customerID'
        . ' WHERE o.orderStatus != ' . $DB->pdb('refunded')
        . ' AND p.created_at IS NOT NULL'
        . ' AND TIME(p.created_at) < ' . $DB->pdb('14:00:00')
        . ' AND TIMESTAMPDIFF(HOUR, p.created_at, NOW()) > 28'
        . ' AND ' . $status_expression . ' != ' . $DB->pdb('SHIPPED')
        . " AND p.pharmacy_orderID IS NOT NULL AND p.pharmacy_orderID <> ''"
        . ' ORDER BY p.created_at DESC';

    $pharmacy_pending_orders = $DB->get_rows($pharmacy_pending_sql) ?: [];
