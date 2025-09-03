DROP TABLE IF EXISTS `__PREFIX__emailoctopus_campaigns`;

CREATE TABLE `__PREFIX__emailoctopus_campaigns` (
  `campaignID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `listID` int(10) unsigned NOT NULL,
  `campaignemailoctopusID` char(32) NOT NULL DEFAULT '',
  `campaignSendTime` datetime DEFAULT NULL,
  `campaignArchiveURL` varchar(255) NOT NULL,
  `campaignStatus` enum('save','pause','schedule','sending','sent') DEFAULT NULL,
  `campaignEmailsSent` int(10) unsigned DEFAULT NULL,
  `campaignSubject` varchar(255) NOT NULL DEFAULT '',
  `campaignTitle` varchar(255) NOT NULL DEFAULT '',
  `campaignText` text,
  `campaignHTML` text,
  `campaignSlug` varchar(255) NOT NULL DEFAULT '',
  `campaignCreated` datetime DEFAULT NULL,
  `campaignUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`campaignID`),
  UNIQUE KEY `idx_cid` (`campaignemailoctopusID`),
  KEY `idx_slug` (`campaignSlug`),
  FULLTEXT KEY `idx_search` (`campaignSubject`,`campaignHTML`)
) ENGINE=InnoDB CHARSET=utf8;

DROP TABLE IF EXISTS `__PREFIX__emailoctopus_imports`;

CREATE TABLE `__PREFIX__emailoctopus_imports` (
  `importID` int(10) NOT NULL AUTO_INCREMENT,
  `importType` char(32) NOT NULL DEFAULT 'list',
  `importSourceID` int(10) DEFAULT NULL,
  `importCount` int(10) NOT NULL DEFAULT '100',
  `importOffset` int(10) NOT NULL DEFAULT '0',
  `importUpdated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`importID`)
) CHARSET=utf8;

DROP TABLE IF EXISTS `__PREFIX__emailoctopus_lists`;

CREATE TABLE `__PREFIX__emailoctopus_lists` (
  `listID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `listemailoctopusID` char(64) NOT NULL DEFAULT '',
  `listTitle` char(255) NOT NULL DEFAULT '',
  `listMemberCount` int(10) unsigned NOT NULL DEFAULT '0',
  `listMemberCountSinceLastSend` int(10) unsigned NOT NULL DEFAULT '0',
  `listUnsubsSinceLastSend` int(10) unsigned NOT NULL DEFAULT '0',
  `listOpenRate` int(10) unsigned NOT NULL DEFAULT '0',
  `listClickRate` int(10) unsigned NOT NULL DEFAULT '0',
  `listLastSend` datetime DEFAULT NULL,
  `listPublic` tinyint(1) unsigned DEFAULT '1',
  `listSearchable` tinyint(1) unsigned DEFAULT '1',
  `listDynamicFields` text,
  `listCreated` datetime NOT NULL,
  `listUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`listID`)
) CHARSET=utf8;

DROP TABLE IF EXISTS `__PREFIX__emailoctopus_subscribers`;

CREATE TABLE `__PREFIX__emailoctopus_subscribers` (
  `subscriberID` int(11) NOT NULL AUTO_INCREMENT,
  `subscriberemailoctopusID` char(255) NOT NULL DEFAULT '',
  `subscriberEmail` char(255) NOT NULL DEFAULT '',
  `subscriberEmailMD5` char(255) DEFAULT NULL,
  `subscriberFirstName` char(255) DEFAULT NULL,
  `subscriberLastName` char(255) DEFAULT NULL,
  `subscriberCreated` datetime DEFAULT NULL,
  `subscriberUpdated` datetime DEFAULT NULL,
  PRIMARY KEY (`subscriberID`)
) CHARSET=utf8;

DROP TABLE IF EXISTS `__PREFIX__emailoctopus_subscriptions`;

CREATE TABLE `__PREFIX__emailoctopus_subscriptions` (
  `subID` int(10) NOT NULL AUTO_INCREMENT,
  `subscriberID` int(10) NOT NULL,
  `listID` int(10) NOT NULL,
  `subStatus` enum('subscribed','unsubscribed','cleaned','pending') NOT NULL DEFAULT 'subscribed',
  `subRating` int(10) unsigned DEFAULT NULL,
  `subCreated` datetime DEFAULT NULL,
  `subUpdated` datetime DEFAULT NULL,
  PRIMARY KEY (`subID`)
) CHARSET=utf8;

DROP TABLE IF EXISTS `__PREFIX__emailoctopus_webhooks`;

CREATE TABLE `__PREFIX__emailoctopus_webhooks` (
  `webhookID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `webhookemailoctopusID` char(255) NOT NULL DEFAULT '',
  `listID` int(10) unsigned NOT NULL,
  `webhookURL` char(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`webhookID`)
) CHARSET=utf8;
