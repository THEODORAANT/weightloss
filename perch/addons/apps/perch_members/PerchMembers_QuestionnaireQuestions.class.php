<?php
class PerchMembers_QuestionnaireQuestions extends PerchAPI_Factory
{
    protected $table     = 'members_questionnaire_questions';
    protected $pk        = 'questionID';
    protected $singular_classname = 'PerchMembers_QuestionnaireQuestion';
    protected $default_sort_column = 'sort';
    public $static_fields   = array();
    protected static $schemaEnsured = false;

    protected function ensure_schema()
    {
        if (self::$schemaEnsured) {
            return;
        }

        $table = $this->table;
        $columns = [
            'questionnaireSlug' => "ALTER TABLE `{$table}` ADD COLUMN `questionnaireSlug` varchar(64) DEFAULT NULL AFTER `questionnaireType`",
            'productSlug' => "ALTER TABLE `{$table}` ADD COLUMN `productSlug` varchar(64) DEFAULT NULL AFTER `questionnaireSlug`",
        ];

        foreach ($columns as $column => $alterSql) {
            $exists = $this->db->get_value("SHOW COLUMNS FROM `{$table}` LIKE ".$this->db->pdb($column));
            if (!$exists) {
                try {
                    $this->db->execute($alterSql);
                } catch (Exception $e) {
                    // Keep backward compatibility if schema updates are blocked.
                }
            }
        }

        self::$schemaEnsured = true;
    }

    public function get_for_type($type)
    {
        $this->ensure_schema();
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE questionnaireType=' . $this->db->pdb($type) . ' ORDER BY sort ASC, questionID ASC';
        return $this->db->get_rows($sql);
    }

    public function get_for_assignment($type, $questionnaireSlug = 'default', $productSlug = null)
    {
        $this->ensure_schema();

        $productSlug = trim((string)$productSlug);
        $questionnaireSlug = trim((string)$questionnaireSlug);

        if ($questionnaireSlug === '') {
            $questionnaireSlug = 'default';
        }

        $sql = 'SELECT * FROM ' . $this->table
            . ' WHERE questionnaireType=' . $this->db->pdb($type)
            . ' AND COALESCE(NULLIF(questionnaireSlug, \'\'), \'default\')=' . $this->db->pdb($questionnaireSlug);

        if ($productSlug !== '') {
            $sql .= ' AND (productSlug IS NULL OR productSlug=\'\' OR productSlug=\'all\' OR productSlug=' . $this->db->pdb($productSlug) . ')';
        } else {
            $sql .= ' AND (productSlug IS NULL OR productSlug=\'\' OR productSlug=\'all\')';
        }

        $sql .= ' ORDER BY sort ASC, questionID ASC';

        return $this->db->get_rows($sql);
    }

    public function get_next_sort_for_type($type)
    {
        $this->ensure_schema();
        $sql = 'SELECT MAX(sort) FROM ' . $this->table . ' WHERE questionnaireType=' . $this->db->pdb($type);
        $max = (int)$this->db->get_value($sql);

        if ($max <= 0) {
            return 10;
        }

        return $max + 10;
    }

}
?>
