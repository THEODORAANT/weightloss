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

The script records each notification in `logs/notifications/send_payment_notificationYYYY-MM-DD.log`.
Entries are keyed by the package item ID, so the same item isn't notified
twice. It creates the `logs` directory if needed and ensures it is writable.
Administrators can
review these entries from the **Notification Logs** module in the admin area
(`perch/addons/apps/perch_notification_logs/index.php`).


Run the script from the command line:

```
php send_payment_notification.php
```

Each matching customer receives an email reminder to complete the payment
from their portal.

## Reorder Reminders

Run `scripts/send_reorder_reminders.php` to email customers roughly three
weeks after a paid order. By default the script looks for orders created 21
days ago, skips anyone who has already placed a newer paid order, and sends a
push notification through `perch_member_add_notification` alongside the
email. A log entry is stored in `logs/reorder_reminders/` for each processed
order so follow-up runs stay idempotent.

Common usage:

```
php scripts/send_reorder_reminders.php
```

Use `--dry-run` to preview actions without sending messages, `--date` to target a
specific day (`YYYY-MM-DD`), or `--days` to override the 21-day offset. You can
also limit the run to a single order or customer via `--order-id` or
`--customer-id`.

## Advancing Billing Dates

After recording a payment, run `advance_next_billing.php` to move the
package's `nextBillingDate` forward by one month:

```
php advance_next_billing.php <packageID>
```

This keeps the package's billing cycle up to date.

## Updating an Order Item's Product

Use `scripts/update_order_item_product.php` to switch an order item's product
and adjust its pricing details. Run the script from the project root so it can
load the Perch runtime:

```
php scripts/update_order_item_product.php \
  --order-id=123 \
  --item-id=456 \
  --product-id=789 \
  --price=12.99
```

Replace the option values with the relevant IDs and unit price. You can supply
additional flags such as `--qty`, `--discount`, or `--dry-run`; run the script
with `--help` to see every available option and their descriptions.

## Package Report

In the admin interface, under the Orders section, use the **Package Report** link to view upcoming payments. The report lists each package along with its customer ID, status, and next billing date.

## Orders & Conversion Report

Under **Orders → Reports** administrators can review order volume and customer conversions at a glance. The page summarises the total orders recorded together with the number of members who have become customers, and breaks both metrics down by month and by year for trend analysis.

## Admin Package Module

 In the admin interface, under the Orders section, use the **Package Admin** link to add new packages and review existing ones. The page shows each client's billing type, payment status, next billing date, and highlights pending packages. Each package expands to list its items, including product and variant details, quantity, and payment status.


## Push Notifications Inbox

Clients can view push notifications via the `/api/notifications` endpoint. The endpoint returns a list of messages for the authenticated member, including title, message body, timestamp, and read state, enabling an inbox within the client portal.

The client portal exposes these messages at `/client/notifications`, rendering a simple list of a member's alerts.
Unread notifications display a red dot indicator and are marked as read when the list is viewed.

To create a new notification in code, call `perch_member_add_notification($memberID, $title, $message)`.
Administrators can also add notifications for a member from the member edit screen in the control panel.

## Member Support Chat

The self-hosted chat system lets members talk directly with the support team inside the client portal and Perch admin area.

- Run the SQL in `sql/create_chat_tables.sql` (replace `__PREFIX__` with your database prefix) to create the chat tables before deploying.
- Logged-in members can open `/client/chat` to view the conversation history, send new messages, and receive responses in real time.
- The client navigation shows a red dot when a staff reply is waiting to be read.
- Staff users can access **Members → Chat** in the Perch admin to review all conversations, reply, and close or reopen threads. The menu badge highlights how many chats are waiting for a staff response.

## Product API

- `GET /api/products/{id}` returns the specified product with all of its variants.
- `GET /api/products/{id}/variants` returns only the variants for that product.

`{id}` corresponds to the product slug in Perch Shop.

## Social sign-in API

Two new endpoints enable native mobile and web clients to exchange Google or Apple
identity tokens for a Weightloss member session token:

- `POST /api/login/google` – accepts a Google `id_token` and returns the
  application token together with the member profile.
- `POST /api/login/apple` – accepts an Apple `identity_token` and returns the
  application token and profile data.

If the supplied email address does not yet exist, the member record, associated
shop customer, and relevant tags are created automatically so that follow-up
flows (packages, questionnaires, etc.) continue to work without manual setup.

For additional security you can restrict accepted OAuth clients via environment
variables:

- `GOOGLE_SIGNIN_CLIENT_IDS` – comma-separated list of allowed Google OAuth
  client IDs.
- `APPLE_SIGNIN_CLIENT_IDS` – comma-separated list of allowed Apple Sign In
  client IDs.

When the variables are omitted the endpoints will accept any valid token issued
by the provider.

# perchDocumenttion
