<?php

declare(strict_types=1);

require_once __DIR__ . '/email_unsubscribe_list.php';

$memberID = isset($_REQUEST['member_id']) ? (int) $_REQUEST['member_id'] : null;
$customerID = isset($_REQUEST['customer_id']) ? (int) $_REQUEST['customer_id'] : null;
$emailAddress = isset($_REQUEST['email']) ? trim((string) $_REQUEST['email']) : null;

if ($memberID !== null && $memberID <= 0) {
    $memberID = null;
}

if ($customerID !== null && $customerID <= 0) {
    $customerID = null;
}

if ($emailAddress !== null && $emailAddress !== '' && !filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
    $emailAddress = null;
}

if ($memberID === null && $customerID === null && ($emailAddress === null || $emailAddress === '')) {
    http_response_code(400);
    echo '<!DOCTYPE html><html lang="en"><body>';
    echo '<p>We could not process your unsubscribe request because no recipient details were provided.</p>';
    echo '</body></html>';
    exit(0);
}

$saved = add_email_unsubscribe_entry($memberID, $customerID, $emailAddress);

echo '<!DOCTYPE html><html lang="en"><body>';
if ($saved) {
    echo '<p>You have been unsubscribed from scripted emails. Please allow a few minutes for the change to take effect.</p>';
} else {
    echo '<p>We were unable to save your unsubscribe request. Please contact support for assistance.</p>';
}
echo '</body></html>';
