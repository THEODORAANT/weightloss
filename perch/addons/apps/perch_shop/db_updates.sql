ALTER TABLE `__PREFIX__shop_shippings` ADD `shippingOrder` INT(10)  UNSIGNED  NOT NULL  DEFAULT '1'  AFTER `shippingSlug`;

ALTER TABLE `__PREFIX__shop_currencies` ADD `currencyDecimalSeparator` CHAR(16) NOT NULL DEFAULT '.' AFTER `currencyDecimals`;

ALTER TABLE `__PREFIX__shop_currencies` ADD `currencyThousandsSeparator` CHAR(16)  NOT NULL  DEFAULT ','  AFTER `currencyDecimalSeparator`;

ALTER TABLE `__PREFIX__shop_products` ADD `productStatus` TINYINT(1)  UNSIGNED  NOT NULL  DEFAULT '1'  AFTER `productDeleted`;

ALTER TABLE `__PREFIX__shop_products` ADD INDEX `idx_status` (`productStatus`);

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_sales` (
  `saleID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `saleTitle` varchar(255) NOT NULL DEFAULT '',
  `saleDynamicFields` mediumtext,
  `saleFrom` datetime DEFAULT NULL,
  `saleTo` datetime DEFAULT NULL,
  `saleActive` tinyint(1) DEFAULT '1',
  `saleOrder` int(10) unsigned NOT NULL DEFAULT '1',
  `saleCreated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `saleUpdated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `saleDeleted` datetime DEFAULT NULL,
  PRIMARY KEY (`saleID`)
) CHARSET=utf8;

ALTER TABLE `__PREFIX__shop_countries` ADD `countryDynamicFields` TEXT  NULL  AFTER `countryActive`;
CREATE TABLE IF NOT EXISTS `__PREFIX__shop_packages` (
  `packageID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customerID` int(10) unsigned NOT NULL DEFAULT '0',
  `month` char(7) NOT NULL DEFAULT '',
  `status` char(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`packageID`),
  KEY `idx_customer` (`customerID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_package_items` (
  `itemID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `packageID` int(10) unsigned NOT NULL DEFAULT '0',
  `productID` int(10) unsigned NOT NULL DEFAULT '0',
  `variantID` int(10) unsigned DEFAULT NULL,
  `qty` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`itemID`),
  KEY `idx_package` (`packageID`)
) CHARSET=utf8;
