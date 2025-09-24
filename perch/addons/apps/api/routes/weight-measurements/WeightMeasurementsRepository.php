<?php

class WeightMeasurementsRepository
{
    private $db;
    private $table;

    public function __construct($db = null, $table = null)
    {
       /*  if (is_string($db) && $db !== '' && $table === null) {
            $table = $db;
            $db = null;
        }

       if ($db === null) {
            $this->db = PerchDB::fetch();
        } elseif (is_object($db)) {
            $this->db = $db;
        } else {
            $this->db = PerchDB::fetch($db);
        }*/
         $this->db = PerchDB::fetch();
        if (is_string($table) && $table !== '') {
            $this->table = $this->resolveTableName($table);
        } else {
            $this->table = wl_weight_measurements_table();
        }
    }

    public function countForMember($memberId, $startDate = null, $endDate = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->table . ' WHERE ' . $this->buildDateRangeWhere($memberId, $startDate, $endDate);

        $total = $this->db->get_count($sql);

        if (!is_int($total)) {
            return 0;
        }

        return $total;
    }

    public function fetchPageForMember($memberId, $limit, $offset, $startDate = null, $endDate = null)
    {
        $where = $this->buildDateRangeWhere($memberId, $startDate, $endDate);

        $sql = 'SELECT * FROM ' . $this->table
            . ' WHERE ' . $where
            . ' ORDER BY measurement_date DESC'
            . ' LIMIT ' . (int)$offset . ', ' . (int)$limit;

        $rows = $this->db->get_rows($sql);

        return is_array($rows) ? $rows : [];
    }

    public function fetchChronologicalForMember($memberId, $startDate = null)
    {
        $where = $this->buildDateRangeWhere($memberId, $startDate, null);

        $sql = 'SELECT * FROM ' . $this->table
            . ' WHERE ' . $where
            . ' ORDER BY measurement_date ASC';

        $rows = $this->db->get_rows($sql);

        return is_array($rows) ? $rows : [];
    }

    public function fetchLatestForMember($memberId)
    {
        $sql = 'SELECT * FROM ' . $this->table
            . ' WHERE member_id = ' . $this->db->pdb($memberId)
            . ' ORDER BY measurement_date DESC LIMIT 1';

        $row = $this->db->get_row($sql);

        return is_array($row) ? $row : null;
    }

    public function findForMember($memberId, $measurementId)
    {
        $sql = 'SELECT * FROM ' . $this->table
            . ' WHERE measurement_id = ' . $this->db->pdb($measurementId)
            . ' AND member_id = ' . $this->db->pdb($memberId)
            . ' LIMIT 1';

        $row = $this->db->get_row($sql);

        return is_array($row) ? $row : null;
    }

    public function deleteById($measurementId)
    {
        $result = $this->db->delete($this->table, 'measurement_id', $measurementId, 1);

        if ($result === false) {
            return false;
        }

        return $result > 0;
    }

    public function createMeasurement($memberId, array $data)
    {
        $insert = $this->prepareInsertData($memberId, $data);

        $columns = array_keys($insert);
        $values = array();

        foreach ($insert as $value) {
            $values[] = $this->db->pdb($value);
        }

        $sql = 'INSERT INTO ' . $this->table
            . ' (' . implode(',', $columns) . ')'
            . ' VALUES (' . implode(',', $values) . ')';
 try {
          $insertId = $this->db->execute($sql);
        } catch (Exception $e) {
            wl_weight_measurements_error(400, json_decode($e, true));
        }


        if ($insertId === false) {
            return false;
        }

        return $this->findForMember($memberId, (int)$insertId);
    }

    public function countCreatedSince($memberId, $since)
    {
        if ($this->isDateTime($since)) {
            $since = $since->format('Y-m-d H:i:s');
        }

        $sql = 'SELECT COUNT(*) FROM ' . $this->table
            . ' WHERE member_id = ' . $this->db->pdb($memberId)
            . ' AND created_at >= ' . $this->db->pdb($since);

        $count = $this->db->get_count($sql);

        if (!is_int($count)) {
            return 0;
        }

        return $count;
    }

    private function prepareInsertData($memberId, array $data)
    {
        $insert = array(
            'member_id' => $memberId,
        );

        $columns = array(
            'weight_kg',
            'bmi',
            'body_fat_percent',
            'muscle_percent',
            'moisture_percent',
            'bone_mass',
            'protein_percent',
            'bmr',
            'visceral_fat',
            'skeletal_muscle_percent',
            'physical_age',
            'measurement_date',
            'created_at',
            'updated_at',
        );

        foreach ($columns as $column) {
            if (!array_key_exists($column, $data)) {
                $insert[$column] = null;
                continue;
            }

            $value = $data[$column];

            if ($this->isDateTime($value)) {
                $value = $value->format('Y-m-d H:i:s');
            }

            $insert[$column] = $value;
        }

        return $insert;
    }

    private function buildDateRangeWhere($memberId, $startDate = null, $endDate = null)
    {
        $clauses = array('member_id = ' . $this->db->pdb($memberId));

        if ($startDate !== null) {
            if ($this->isDateTime($startDate)) {
                $startDate = $startDate->format('Y-m-d H:i:s');
            }

            $clauses[] = 'measurement_date >= ' . $this->db->pdb($startDate);
        }

        if ($endDate !== null) {
            if ($this->isDateTime($endDate)) {
                $endDate = $endDate->format('Y-m-d H:i:s');
            }

            $clauses[] = 'measurement_date <= ' . $this->db->pdb($endDate);
        }

        return implode(' AND ', $clauses);
    }

    private function resolveTableName($table)
    {
        if (!defined('PERCH_DB_PREFIX') || PERCH_DB_PREFIX === '') {
            return $table;
        }

        if (strpos($table, PERCH_DB_PREFIX) === 0) {
            return $table;
        }

        return "getweightloss_measurements.".PERCH_DB_PREFIX . $table;
    }

    private function isDateTime($value)
    {
        return ($value instanceof DateTimeImmutable) || ($value instanceof DateTime);
    }
}

