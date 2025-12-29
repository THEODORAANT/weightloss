<?php

class SupportIssueLogRepository
{
    /** @var PerchDB */
    private $db;

    /** @var string */
    private $table;

    public function __construct($db = null)
    {
        $this->db = $db ?: PerchDB::fetch();
        $this->table = PERCH_DB_PREFIX . 'support_issue_log';
    }

    public function ensure_table_exists()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . $this->table . '` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `issueType` varchar(32) NOT NULL,
            `memberID` int unsigned DEFAULT NULL,
            `orderID` int unsigned DEFAULT NULL,
            `orderNumber` varchar(64) DEFAULT NULL,
            `trackingNumber` varchar(64) DEFAULT NULL,
            `summary` varchar(255) NOT NULL,
            `details` text,
            `status` varchar(32) NOT NULL DEFAULT "open",
            `resolution` text,
            `eventDate` date DEFAULT NULL,
            `loggedBy` int unsigned DEFAULT NULL,
            `loggedByName` varchar(255) DEFAULT NULL,
            `createdAt` datetime NOT NULL,
            `updatedAt` datetime NOT NULL,
            PRIMARY KEY (`id`),
            KEY `issueType` (`issueType`),
            KEY `status` (`status`),
            KEY `memberID` (`memberID`),
            KEY `orderID` (`orderID`),
            KEY `orderNumber` (`orderNumber`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

        $this->db->execute($sql);
    }

    public function table_ready()
    {
        $table = $this->db->get_value('SHOW TABLES LIKE ' . $this->db->pdb($this->table));

        return $table !== false && $table !== null;
    }

    public function save(array $data, $id = null)
    {
        $now = date('Y-m-d H:i:s');
        $data['updatedAt'] = $now;

        if ($id !== null) {
            $id = (int)$id;
            return $this->db->update($this->table, $data, 'id', $id);
        }

        $data['createdAt'] = $now;

        return $this->db->insert($this->table, $data);
    }

    public function find($id)
    {
        $id = (int)$id;

        if ($id < 1) {
            return null;
        }

        return $this->db->get_row('SELECT * FROM ' . $this->table . ' WHERE id = ' . $this->db->pdb($id) . ' LIMIT 1');
    }

    public function list(array $filters = [], $limit = 200)
    {
        $sql = 'SELECT * FROM ' . $this->table;
        $where = [];

        if (!empty($filters['issueType'])) {
            $where[] = 'issueType = ' . $this->db->pdb($filters['issueType']);
        }

        if (!empty($filters['status'])) {
            $where[] = 'status = ' . $this->db->pdb($filters['status']);
        }

        if (!empty($filters['memberID'])) {
            $where[] = 'memberID = ' . $this->db->pdb((int)$filters['memberID']);
        }

        if (!empty($filters['orderID'])) {
            $where[] = 'orderID = ' . $this->db->pdb((int)$filters['orderID']);
        }

        if (count($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY createdAt DESC, id DESC';

        if ($limit && (int)$limit > 0) {
            $sql .= ' LIMIT ' . (int)$limit;
        }

        return $this->db->get_rows($sql) ?: [];
    }
}

