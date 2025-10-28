<?php

class PerchMembers_InjectionLogsRepository
{
    private $db;
    private $table;

    public function __construct($db = null, $table = "injection_logs")
    {
        if ($db === null) {
            $this->db = PerchDB::fetch();
        } elseif (is_object($db)) {
            $this->db = $db;
        } else {
            $this->db = PerchDB::fetch($db);
        }

        if (is_string($table) && $table !== '') {
            $this->table = $this->resolveTableName($table);
        } else {
            $this->table = $this->defaultTableName();
        }
    }

    public function countForMember($memberId, $startDate = null, $endDate = null)
    {
        $where = $this->buildDateRangeWhere($memberId, $startDate, $endDate);
        $sql = 'SELECT COUNT(*) FROM ' . $this->table . ' WHERE ' . $where;

        $count = $this->db->get_count($sql);

        return is_int($count) ? $count : 0;
    }

    public function fetchPageForMember($memberId, $limit, $offset, $startDate = null, $endDate = null)
    {
        $where = $this->buildDateRangeWhere($memberId, $startDate, $endDate);
        $sql = 'SELECT * FROM ' . $this->table
            . ' WHERE ' . $where
            . ' ORDER BY injection_date DESC'
            . ' LIMIT ' . (int)$offset . ', ' . (int)$limit;

        $rows = $this->db->get_rows($sql);

        return is_array($rows) ? $rows : [];
    }

    public function findForMember($memberId, $logId)
    {
        $sql = 'SELECT * FROM ' . $this->table
            . ' WHERE log_id = ' . $this->db->pdb($logId)
            . ' AND member_id = ' . $this->db->pdb($memberId)
            . ' LIMIT 1';

        $row = $this->db->get_row($sql);

        return is_array($row) ? $row : null;
    }

    public function deleteById($logId)
    {
        $result = $this->db->delete($this->table, 'log_id', $logId, 1);

        if ($result === false) {
            return false;
        }

        return $result > 0;
    }

    public function createLog($memberId, array $data)
    {
        $insert = $this->prepareInsertData($memberId, $data);

        $columns = array_keys($insert);
        $values = [];

        foreach ($insert as $value) {
            $values[] = $this->db->pdb($value);
        }

        $sql = 'INSERT INTO ' . $this->table
            . ' (' . implode(',', $columns) . ')'
            . ' VALUES (' . implode(',', $values) . ')';
echo $sql;
        $insertId = $this->db->execute($sql);

        if ($insertId === false) {
            return false;
        }

        return $this->findForMember($memberId, (int)$insertId);
    }

    public function fetchLatestForMember($memberId)
    {
        $sql = 'SELECT * FROM ' . $this->table
            . ' WHERE member_id = ' . $this->db->pdb($memberId)
            . ' ORDER BY injection_date DESC, log_id DESC LIMIT 1';

        $row = $this->db->get_row($sql);

        return is_array($row) ? $row : null;
    }

    public function fetchChronologicalForMember($memberId)
    {
        $sql = 'SELECT * FROM ' . $this->table
            . ' WHERE member_id = ' . $this->db->pdb($memberId)
            . ' ORDER BY injection_date ASC, log_id ASC';

        $rows = $this->db->get_rows($sql);

        return is_array($rows) ? $rows : [];
    }

    private function prepareInsertData($memberId, array $data)
    {
        $insert = [
            'member_id' => $memberId,
            'injection_date' => null,
            'dose_mg' => null,
            'medication_type' => null,
            'notes' => null,
            'created_at' => null,
            'updated_at' => null,
        ];

        foreach ($insert as $column => $default) {
            if (!array_key_exists($column, $data)) {
                continue;
            }

            $value = $data[$column];
            if ($value instanceof DateTimeInterface) {
                $value = $value->format('Y-m-d H:i:s');
            }

            $insert[$column] = $value;
        }

        return $insert;
    }

    private function buildDateRangeWhere($memberId, $startDate = null, $endDate = null)
    {
        $clauses = ['member_id = ' . $this->db->pdb($memberId)];

        if ($startDate !== null) {
            if ($startDate instanceof DateTimeInterface) {
                $startDate = $startDate->format('Y-m-d H:i:s');
            }

            $clauses[] = 'injection_date >= ' . $this->db->pdb($startDate);
        }

        if ($endDate !== null) {
            if ($endDate instanceof DateTimeInterface) {
                $endDate = $endDate->format('Y-m-d H:i:s');
            }

            $clauses[] = 'injection_date <= ' . $this->db->pdb($endDate);
        }

        return implode(' AND ', $clauses);
    }

    private function resolveTableName($table)
    {
        if (strpos($table, PERCH_DB_PREFIX) === 0) {
            return $table;
        }

      //  return PERCH_DB_PREFIX . ltrim($table, '_');
         return "getweightloss_measurements.".PERCH_DB_PREFIX . $table;
    }

    private function defaultTableName()
    {
        return PERCH_DB_PREFIX . 'injection_logs';
    }
}
