<?php
if (!defined('PERCH_DB_PREFIX')) exit;

$API = new PerchAPI(1.0, 'perch_mailer');
$Settings = $API->get('Settings');
$UserPrivileges = $API->get('UserPrivileges');

$UserPrivileges->create_privilege('perch_mailer', 'Access PerchMailer');
$UserPrivileges->create_privilege('perch_mailer.templates.manage', 'Manage mail templates');
$UserPrivileges->create_privilege('perch_mailer.triggers.manage', 'Manage mail triggers');
$UserPrivileges->create_privilege('perch_mailer.contacts.manage', 'Manage mail contacts');

$sql = "
    CREATE TABLE IF NOT EXISTS `__PREFIX__mailer_templates` (
      `templateID` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `templateTitle` varchar(255) NOT NULL DEFAULT '',
      `templateSlug` varchar(255) NOT NULL DEFAULT '',
      `templateSubject` varchar(255) NOT NULL DEFAULT '',
      `templateFromName` varchar(255) DEFAULT NULL,
      `templateFromEmail` varchar(255) DEFAULT NULL,
      `templateHTML` mediumtext,
      `templatePlain` mediumtext,
      `templateDynamicFields` mediumtext,
      `templateCreated` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
      `templateUpdated` datetime DEFAULT NULL,
      PRIMARY KEY (`templateID`),
      UNIQUE KEY `templateSlug` (`templateSlug`)
    ) CHARSET=utf8;

    CREATE TABLE IF NOT EXISTS `__PREFIX__mailer_triggers` (
      `triggerID` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `triggerTitle` varchar(255) NOT NULL DEFAULT '',
      `triggerSlug` varchar(255) NOT NULL DEFAULT '',
      `triggerDescription` text,
      `triggerTemplateID` int(10) unsigned DEFAULT NULL,
      `triggerActive` tinyint(1) NOT NULL DEFAULT '1',
      `triggerDynamicFields` mediumtext,
      `triggerCreated` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
      `triggerUpdated` datetime DEFAULT NULL,
      PRIMARY KEY (`triggerID`),
      UNIQUE KEY `triggerSlug` (`triggerSlug`),
      KEY `idx_template` (`triggerTemplateID`)
    ) CHARSET=utf8;

    CREATE TABLE IF NOT EXISTS `__PREFIX__mailer_contacts` (
      `contactID` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `contactFirstName` varchar(255) DEFAULT NULL,
      `contactLastName` varchar(255) DEFAULT NULL,
      `contactEmail` varchar(255) NOT NULL DEFAULT '',
      `contactStatus` varchar(32) NOT NULL DEFAULT 'active',
      `contactDynamicFields` mediumtext,
      `memberID` int(10) unsigned DEFAULT NULL,
      `contactCreated` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
      `contactUpdated` datetime DEFAULT NULL,
      PRIMARY KEY (`contactID`),
      UNIQUE KEY `contactEmail` (`contactEmail`),
      KEY `idx_member` (`memberID`)
    ) CHARSET=utf8;
";

$sql = str_replace('__PREFIX__', PERCH_DB_PREFIX, $sql);

$statements = explode(';', $sql);
foreach ($statements as $statement) {
    $statement = trim($statement);
    if ($statement != '') {
        $this->db->execute($statement);
    }
}
