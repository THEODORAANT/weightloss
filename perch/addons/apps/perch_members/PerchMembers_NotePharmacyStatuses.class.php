<?php

class PerchMembers_NotePharmacyStatuses extends PerchAPI_Factory
{
    protected $table               = 'members_note_pharmacy_statuses';
    protected $pk                  = 'statusID';
    protected $singular_classname  = 'PerchMembers_NotePharmacyStatus';
    protected $default_sort_column = 'sentAt';

    private $table_verified = false;

    public function __construct($api)
    {
        parent::__construct($api);
        $this->ensure_table();
    }

    public function record_sent_status($memberID, $noteID, $status = 'Sent', $message = null)
    {
        $memberID = (int) $memberID;
        $noteID   = (int) $noteID;

        if ($memberID <= 0 || $noteID <= 0) {
            return false;
        }

        $this->ensure_table();

        $existing = $this->find_one_by_member_and_note($memberID, $noteID);

        $data = [
            'memberID' => $memberID,
            'noteID'   => $noteID,
            'status'   => $status !== '' ? $status : 'Sent',
            'sentAt'   => date('Y-m-d H:i:s'),
        ];

        if ($message !== null && $message !== '') {
            $data['message'] = $message;
        }

        if ($existing instanceof PerchMembers_NotePharmacyStatus) {
            $existing->update($data);
            return $existing;
        }

        return $this->create($data);
    }

    public function find_one_by_member_and_note($memberID, $noteID)
    {
        $memberID = (int) $memberID;
        $noteID   = (int) $noteID;

        if ($memberID <= 0 || $noteID <= 0) {

            return false;
        }

        $this->ensure_table();

        $sql = 'SELECT * FROM '.$this->table.' WHERE memberID='.$this->db->pdb($memberID).' AND noteID='.$this->db->pdb($noteID).' LIMIT 1';

        $row = $this->db->get_row($sql);



        if ($row) {
            return $this->return_instance($row);
        }

        return false;
    }

    public function get_indexed_for_member($memberID)
    {
        $memberID = (int) $memberID;

        if ($memberID <= 0) {
            return [];
        }

        $this->ensure_table();

        $sql = 'SELECT * FROM '.PERCH_DB_PREFIX.$this->table.' WHERE memberID='.$this->db->pdb($memberID);
        $rows = $this->db->get_rows($sql);

        $out = [];
        $instances = $this->return_instances($rows);

        if (PerchUtil::count($instances)) {
            foreach ($instances as $Instance) {
                if ($Instance instanceof PerchMembers_NotePharmacyStatus) {
                    $out[(int) $Instance->noteID()] = $Instance;
                }
            }
        }

        return $out;
    }

    private function ensure_table()
    {
        if ($this->table_verified) {
            return;
        }

        $table = PERCH_DB_PREFIX.$this->table;
        $exists = $this->db->get_value('SHOW TABLES LIKE '.$this->db->pdb($table));

        if (!$exists) {
            $sql = 'CREATE TABLE `'.$table.'` (
                `statusID` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `memberID` int(10) unsigned NOT NULL,
                `noteID` int(10) unsigned NOT NULL,
                `status` varchar(64) NOT NULL DEFAULT "Sent",
                `message` text DEFAULT NULL,
                `sentAt` datetime NOT NULL,
                PRIMARY KEY (`statusID`),
                UNIQUE KEY `member_note` (`memberID`,`noteID`),
                KEY `note_lookup` (`noteID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
            $this->db->execute($sql);
        } else {
            $columns = $this->db->get_rows('SHOW COLUMNS FROM `'.$table.'`');
            $column_names = [];
            if (PerchUtil::count($columns)) {
                foreach ($columns as $column) {
                    if (isset($column['Field'])) {
                        $column_names[] = $column['Field'];
                    }
                }
            }

            if (!in_array('status', $column_names, true)) {
                $this->db->execute('ALTER TABLE `'.$table.'` ADD COLUMN `status` varchar(64) NOT NULL DEFAULT "Sent" AFTER `noteID`');
            }

            if (!in_array('message', $column_names, true)) {
                $this->db->execute('ALTER TABLE `'.$table.'` ADD COLUMN `message` text DEFAULT NULL AFTER `status`');
            }

            if (!in_array('sentAt', $column_names, true)) {
                $this->db->execute('ALTER TABLE `'.$table.'` ADD COLUMN `sentAt` datetime NOT NULL AFTER `message`');
            }

            $has_unique = false;
            if (PerchUtil::count($columns)) {
                foreach ($columns as $column) {
                    if (isset($column['Key']) && $column['Key'] === 'UNI' && isset($column['Field']) && $column['Field'] === 'memberID') {
                        $has_unique = true;
                        break;
                    }
                }
            }

            if (!$has_unique) {
                $indexes = $this->db->get_rows('SHOW INDEX FROM `'.$table.'`');
                $unique_exists = false;
                if (PerchUtil::count($indexes)) {
                    foreach ($indexes as $index) {
                        if (isset($index['Key_name']) && $index['Key_name'] === 'member_note') {
                            $unique_exists = true;
                            break;
                        }
                    }
                }
                if (!$unique_exists) {
                    $this->db->execute('ALTER TABLE `'.$table.'` ADD UNIQUE KEY `member_note` (`memberID`,`noteID`)');
                }
            }
        }

        $this->table_verified = true;
    }
}
