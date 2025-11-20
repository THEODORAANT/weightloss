<?php

function format_pharmacy_details(array $row): array
{
    $details = [
        'orderId' => isset($row['orderID']) ? (int)$row['orderID'] : null,
        'pharmacyOrderId' => $row['pharmacy_orderID'] ?? null,
        'sentAt' => $row['created_at'] ?? null,
    ];

    $status = $row['status'] ?? $row['pharmacy_status'] ?? $row['order_status'] ?? '';
    $tracking = $row['trackingno'] ?? $row['tracking_no'] ?? $row['trackingnumber'] ?? $row['tracking_number'] ?? $row['trackingref'] ?? $row['tracking_reference'] ?? '';
    $dispatch = $row['dispatch_date'] ?? $row['dispatchdate'] ?? $row['dispatched_at'] ?? $row['dispatcheddate'] ?? '';

    if ($status !== '') {
        $details['status'] = $status;
        $details['statusText'] = $row['status_text'] ?? $status;
    }

    if ($dispatch !== '') {
        $details['dispatchDate'] = $dispatch;
    }

    if ($tracking !== '') {
        $details['trackingNo'] = $tracking;
        $details['trackingUrl'] = 'https://www.royalmail.com/track-your-item#/tracking-results/' . urlencode($tracking);
    }

    if (!empty($details['sentAt']) && (!isset($details['status']) || strcasecmp($details['status'], 'completed') !== 0)) {
        $sentTs = strtotime((string)$details['sentAt']);
        if ($sentTs) {
            $details['daysSinceSent'] = (int)floor((time() - $sentTs) / 86400);
        }
    }

    return $details;
}

function get_pharmacy_lookup(array $orderIDs): array
{
    if (empty($orderIDs)) {
        return [];
    }

    $db = PerchDB::fetch();
    $table = PERCH_DB_PREFIX . 'orders_match_pharmacy';
    $idsSql = $db->implode_for_sql_in($orderIDs);
    $sql = 'SELECT * FROM ' . $table . ' WHERE orderID IN (' . $idsSql . ') ORDER BY created_at DESC';
    $rows = $db->get_rows($sql);

    $lookup = [];
    if (PerchUtil::count($rows)) {
        foreach ($rows as $row) {
            $orderId = isset($row['orderID']) ? (int)$row['orderID'] : null;
            if ($orderId === null) {
                continue;
            }

            if (!isset($lookup[$orderId])) {
                $lookup[$orderId] = format_pharmacy_details($row);
            }
        }
    }

    return $lookup;
}
