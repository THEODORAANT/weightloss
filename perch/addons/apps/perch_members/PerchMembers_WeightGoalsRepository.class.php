<?php

class PerchMembers_WeightGoalsRepository
{
    private $db;
    private $table;

    public function __construct($db = null, $table = "weight_goals")
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

    public function findForMember($memberId)
    {
        $sql = 'SELECT * FROM ' . $this->table
            . ' WHERE member_id = ' . $this->db->pdb($memberId)
            . ' LIMIT 1';

        $row = $this->db->get_row($sql);

        return is_array($row) ? $row : null;
    }

    public function upsertGoal($memberId, array $data)
    {
        $existing = $this->findForMember($memberId);
        $payload = $this->preparePayload($memberId, $data, $existing);

        if ($existing) {
            $assignments = [];
            foreach ($payload as $column => $value) {
                if ($column === 'member_id' || $column === 'created_at') {
                    continue;
                }

                $assignments[] = $column . ' = ' . $this->db->pdb($value);
            }

            $sql = 'UPDATE ' . $this->table
                . ' SET ' . implode(', ', $assignments)
                . ' WHERE goal_id = ' . $this->db->pdb($existing['goal_id'])
                . ' LIMIT 1';

            $result = $this->db->execute($sql);

            if ($result === false) {
                return false;
            }
        } else {
            $columns = array_keys($payload);
            $values = [];
            foreach ($payload as $value) {
                $values[] = $this->db->pdb($value);
            }

            $sql = 'INSERT INTO ' . $this->table
                . ' (' . implode(',', $columns) . ')'
                . ' VALUES (' . implode(',', $values) . ')';

            $insertId = $this->db->execute($sql);

            if ($insertId === false) {
                return false;
            }
        }

        return $this->findForMember($memberId);
    }

    private function preparePayload($memberId, array $data, $existing)
    {
        $payload = [
            'member_id' => $memberId,
            'goal_weight' => null,
            'unit' => null,
            'target_date' => null,
            'starting_weight_kg' => null,
            'starting_weight_recorded_at' => null,
            'created_at' => null,
            'updated_at' => null,
        ];

        foreach ($payload as $column => $default) {
            if (!array_key_exists($column, $data)) {
                if ($existing && array_key_exists($column, $existing)) {
                    $payload[$column] = $existing[$column];
                }

                continue;
            }

            $value = $data[$column];
            if ($value instanceof DateTimeInterface) {
                $value = $value->format('Y-m-d H:i:s');
            }

            $payload[$column] = $value;
        }

        if ($existing) {
            $payload['created_at'] = $existing['created_at'];
        }

        return $payload;
    }

    private function resolveTableName($table)
    {
        if (strpos($table, PERCH_DB_PREFIX) === 0) {
            return $table;
        }

       // return PERCH_DB_PREFIX . ltrim($table, '_');
         return PERCH_MEASUREMENTS_DB . "." . PERCH_DB_PREFIX . $table;
    }

    private function defaultTableName()
    {
        return PERCH_DB_PREFIX . 'weight_goals';
    }
}
