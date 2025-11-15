<?php

class PerchMembers_ReorderReminderService
{
    /**
     * @var PerchAPI
     */
    protected $api;

    public function __construct(PerchAPI $api)
    {
        $this->api = $api;
    }

    /**
     * Process a single order for reorder reminder delivery.
     *
     * @param array<string,mixed>              $order
     * @param array<int,bool>                  $notifiedCustomers
     * @param PerchDB_MySQL|PerchDB_MySQLi|PerchDB $DB
     */
    public function sendReminder(
        array $order,
        bool $dryRun,
        callable $appendLog,
        array &$notifiedCustomers,
        int &$sentCount,
        int &$skippedCount,
        $DB,
        string $ordersTable,
        PerchShop_Customers $Customers,
        string $reorderURL,
        string $senderName,
        string $senderEmail
    ): void {
        $orderID = (int) $order['orderID'];
        $customerID = (int) $order['customerID'];

        $laterOrderSQL = 'SELECT orderID FROM ' . $ordersTable
            . ' WHERE customerID=' . $DB->pdb($customerID)
            . ' AND orderStatus=' . $DB->pdb('paid')
            . ' AND orderDeleted IS NULL'
            . ' AND orderCreated>' . $DB->pdb($order['orderCreated'])
            . ' ORDER BY orderCreated DESC LIMIT 1';

        $laterOrderID = $DB->get_value($laterOrderSQL);
        if ($laterOrderID) {
            echo 'Skipping order ' . $orderID . ' – customer has a later paid order #' . $laterOrderID . '.' . PHP_EOL;
            if (!$dryRun) {
                $appendLog($orderID, $customerID, 'skipped-later-order');
            }
            $skippedCount++;
            return;
        }

        $Customer = $Customers->find($customerID);
        if (!$Customer) {
            echo 'Skipping order ' . $orderID . ' – customer record not found.' . PHP_EOL;
            if (!$dryRun) {
                $appendLog($orderID, $customerID, 'skipped-missing-customer');
            }
            $skippedCount++;
            return;
        }

        $emailAddress = trim((string) $Customer->customerEmail());
        if ($emailAddress === '' || !PerchUtil::is_valid_email($emailAddress)) {
            echo 'Skipping order ' . $orderID . ' – customer has no valid email address.' . PHP_EOL;
            if (!$dryRun) {
                $appendLog($orderID, $customerID, 'skipped-missing-email');
            }
            $skippedCount++;
            return;
        }

        $firstName = trim((string) $Customer->customerFirstName());
        if ($firstName === '') {
            $firstName = 'there';
        }

        try {
            $orderDate = new DateTimeImmutable($order['orderCreated']);
        } catch (Exception $exception) {
            echo 'Skipping order ' . $orderID . ' – invalid order date (' . $exception->getMessage() . ').' . PHP_EOL;
            if (!$dryRun) {
                $appendLog($orderID, $customerID, 'skipped-invalid-date');
            }
            $skippedCount++;
            return;
        }

        $orderDateHuman = $orderDate->format('j F Y');

        $title = 'Time to reorder';
        $message = "It's been about three weeks since your order on {$orderDateHuman}. You can place your next order and pay online at {$reorderURL}.";

        $emailData = [
            'first_name'  => $firstName,
            'order_date'  => $orderDateHuman,
            'reorder_url' => $reorderURL,
            'sender_name' => $senderName,
        ];

        echo 'Preparing reminder for order ' . $orderID . ' (customer ' . $customerID . ').' . PHP_EOL;

        if ($dryRun) {
            $notifiedCustomers[$customerID] = true;
            return;
        }

        try {
            $Email = $this->api->get('Email');
            $Email->set_template('members/emails/reorder_reminder.html');
            $Email->set_bulk($emailData);
            $Email->subject('Time to reorder your medication');
            $Email->senderName($senderName);
            $Email->senderEmail($senderEmail);
            $Email->recipientEmail($emailAddress);

            $emailSent = $Email->send();
        } catch (Exception $exception) {
            echo 'Failed to send email for order ' . $orderID . ': ' . $exception->getMessage() . PHP_EOL;
            $appendLog($orderID, $customerID, 'error-email');
            $skippedCount++;
            return;
        }

        if (!$emailSent) {
            echo 'Failed to send email for order ' . $orderID . ' – send() returned false.' . PHP_EOL;
            $appendLog($orderID, $customerID, 'error-email');
            $skippedCount++;
            return;
        }

        $memberID = (int) $Customer->memberID();
        if ($memberID > 0) {
            try {
                perch_member_add_notification($memberID, $title, $message);
            } catch (Exception $exception) {
                echo 'Failed to create notification for order ' . $orderID . ': ' . $exception->getMessage() . PHP_EOL;
                $appendLog($orderID, $customerID, 'sent-notification-error');
                $notifiedCustomers[$customerID] = true;
                $sentCount++;
                return;
            }
        }

        $appendLog($orderID, $customerID, 'sent');
        $notifiedCustomers[$customerID] = true;
        $sentCount++;
    }
}

