<?php
    // Prevent running directly:
    if (!defined('PERCH_DB_PREFIX')) exit;

    // Let's go
    $sql = "
    CREATE TABLE IF NOT EXISTS `__PREFIX__appointments` (
      `appointmentID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
       `memberID` INT UNSIGNED NULL DEFAULT NULL,
       `productSlug` VARCHAR(255) NOT NULL,
       `productName` VARCHAR(255) NOT NULL,
       `productPrice` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
       `appointmentDate` DATE NOT NULL,
       `appointmentDateLabel` VARCHAR(255) NOT NULL,
       `slotLabel` VARCHAR(255) NOT NULL,
       `goal` TEXT NOT NULL,
       `medical` TEXT NOT NULL,
       `notes` TEXT NULL,
       `createdAt` DATETIME NOT NULL,
      PRIMARY KEY (`appointmentID`),
      KEY `idx_member` (`memberID`)
    ) CHARSET=utf8;

  ";
    
    $sql = str_replace('__PREFIX__', PERCH_DB_PREFIX, $sql);
    
    $statements = explode(';', $sql);
    foreach($statements as $statement) {
        $statement = trim($statement);
        if ($statement!='') $this->db->execute($statement);
    }


    $API = new PerchAPI(1.0, 'perch_appointments');
    //$UserPrivileges = $API->get('UserPrivileges');
    //$UserPrivileges->create_privilege('perch_events', 'Access events');
    //$UserPrivileges->create_privilege('perch_events.categories.manage', 'Manage categories');
        
    $sql = 'SHOW TABLES LIKE "'.$this->table.'"';
    $result = $this->db->get_value($sql);
    
    return $result;

