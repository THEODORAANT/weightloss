# Weightloss Project

This repository contains various PHP scripts for managing packages.

## Payment Scheduler

`next_payment_scheduler.php` provides a helper function to determine the next payment
for a monthly subscription package. You can run it from the command line:

```
php next_payment_scheduler.php 2023-01-15
```

This outputs the date when the next payment is due.

## Payment Notifications

`send_payment_notification.php` scans the `shop_packages` table for
pending packages with a `nextBillingDate` one week in the future and uses
Perch's email library to notify the associated customers.

Run the script from the command line:

```
php send_payment_notification.php
```

Each matching customer receives an email reminder to complete the payment
from their portal.

## Advancing Billing Dates

After recording a payment, run `advance_next_billing.php` to move the
package's `nextBillingDate` forward by one month:

```
php advance_next_billing.php <packageID>
```

This keeps the package's billing cycle up to date.

## Package Report

In the admin interface, under the Orders section, use the **Package Report** link to view upcoming payments. The report lists each package along with its customer ID, status, and next billing date.

## Push Notifications Inbox

Clients can view push notifications via the `/api/notifications` endpoint. The endpoint returns a list of messages for the authenticated member, including title, message body, timestamp, and read state, enabling an inbox within the client portal.

The client portal exposes these messages at `/client/notifications`, rendering a simple list of a member's alerts.
Unread notifications display a red dot indicator and are marked as read when the list is viewed.

To create a new notification in code, call `perch_member_add_notification($memberID, $title, $message)`.

