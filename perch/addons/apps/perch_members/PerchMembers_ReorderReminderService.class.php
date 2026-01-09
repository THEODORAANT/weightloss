<?php

$unsubscribeListPath = realpath(__DIR__ . '/../../../../scripts/email_unsubscribe_list.php');
if ($unsubscribeListPath && file_exists($unsubscribeListPath)) {
    require_once $unsubscribeListPath;
}

class PerchMembers_ReorderReminderService
{
    /**
     * @var PerchAPI
     */
    protected $api;

    public function __construct(PerchAPI $api)
    {
       // $this->api = $api;
                $this->api = $api instanceof PerchAPI ? $api : new PerchAPI(1.0, 'perch_members');

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
        string $siteURL,
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

        $memberID = (int) $Customer->memberID();
        if (
            (function_exists('is_customer_unsubscribed') && is_customer_unsubscribed($customerID))
            || (function_exists('is_member_unsubscribed') && is_member_unsubscribed($memberID))
            || (function_exists('is_email_unsubscribed') && is_email_unsubscribed($emailAddress))
        ) {
            echo 'Skipping order ' . $orderID . ' – recipient unsubscribed from scripted emails.' . PHP_EOL;
            if (!$dryRun) {
                $appendLog($orderID, $customerID, 'skipped-email-unsubscribed');
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

        $unsubscribeURL = '';
        if (function_exists('build_scripted_email_unsubscribe_url')) {
            $unsubscribeURL = build_scripted_email_unsubscribe_url($siteURL, $memberID, $customerID, $emailAddress);
        }

        $emailData = [
            'first_name'  => $firstName,
            'order_date'  => $orderDateHuman,
            'reorder_url' => $reorderURL,
            'sender_name' => $senderName,
            'unsubscribe_url' => $unsubscribeURL,
        ];

        echo 'Preparing reminder for order ' . $orderID . ' (customer ' . $customerID . ').' . PHP_EOL;

        if ($dryRun) {
            $notifiedCustomers[$customerID] = true;
            return;
        }
echo "emailData"; print_r($emailData);
       // try {
            $Email = $this->api->get('Email');
           // $Email->set_template('/perch/addons/apps/perch_members/templates/members/emails/reorder_reminder.html','members');
             $Email->set_template('members/emails/reorder_reminder.html');

            $Email->set_bulk($emailData);
            $Email->subject('Time to reorder your medication');
           // $Email->senderName($senderName);
          //  $Email->senderEmail($senderEmail);
          //  $Email->recipientEmail($emailAddress);

                    $Email->senderName(PERCH_EMAIL_FROM_NAME);
                    $Email->senderEmail(PERCH_EMAIL_FROM);
                    $Email->recipientEmail($emailAddress);
                   // $Email->recipientEmail('perchrunway@gmail.com');

  echo "Email";
            print_r($Email);
            $emailSent = $Email->send();

            echo "email";
            print_r($emailSent);
     /*   } catch (Exception $exception) {
            echo 'Failed to send email for order ' . $orderID . ': ' . $exception->getMessage() . PHP_EOL;
            $appendLog($orderID, $customerID, 'error-email');
            $skippedCount++;
            return;
        }*/

        if (!$emailSent) {
            echo 'Failed to send email for order ' . $orderID . ' – send() returned false.' . PHP_EOL;
            $appendLog($orderID, $customerID, 'error-email');
            $skippedCount++;
            return;
        }

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
