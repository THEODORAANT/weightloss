-- SQL statements to ensure pharmacy order tracking columns exist on p4_orders_match_pharmacy.
-- Run each statement individually if your MySQL version does not support `ADD COLUMN IF NOT EXISTS`.

ALTER TABLE `p4_orders_match_pharmacy`
    ADD COLUMN IF NOT EXISTS `status` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `p4_orders_match_pharmacy`
    ADD COLUMN IF NOT EXISTS `pharmacy_status` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `p4_orders_match_pharmacy`
    ADD COLUMN IF NOT EXISTS `order_status` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `p4_orders_match_pharmacy`
    ADD COLUMN IF NOT EXISTS `status_text` VARCHAR(255) NULL DEFAULT NULL;

ALTER TABLE `p4_orders_match_pharmacy`
    ADD COLUMN IF NOT EXISTS `dispatchdate` DATETIME NULL DEFAULT NULL;
ALTER TABLE `p4_orders_match_pharmacy`
    ADD COLUMN IF NOT EXISTS `dispatch_date` DATETIME NULL DEFAULT NULL;
ALTER TABLE `p4_orders_match_pharmacy`
    ADD COLUMN IF NOT EXISTS `dispatched_at` DATETIME NULL DEFAULT NULL;
ALTER TABLE `p4_orders_match_pharmacy`
    ADD COLUMN IF NOT EXISTS `dispatcheddate` DATETIME NULL DEFAULT NULL;

ALTER TABLE `p4_orders_match_pharmacy`
    ADD COLUMN IF NOT EXISTS `trackingno` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `p4_orders_match_pharmacy`
    ADD COLUMN IF NOT EXISTS `tracking_no` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `p4_orders_match_pharmacy`
    ADD COLUMN IF NOT EXISTS `trackingnumber` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `p4_orders_match_pharmacy`
    ADD COLUMN IF NOT EXISTS `tracking_number` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `p4_orders_match_pharmacy`
    ADD COLUMN IF NOT EXISTS `trackingref` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `p4_orders_match_pharmacy`
    ADD COLUMN IF NOT EXISTS `tracking_reference` VARCHAR(255) NULL DEFAULT NULL;
