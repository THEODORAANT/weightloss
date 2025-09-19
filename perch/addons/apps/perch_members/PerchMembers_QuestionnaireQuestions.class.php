<?php
class PerchMembers_QuestionnaireQuestions extends PerchAPI_Factory
{
    protected $table     = 'members_questionnaire_questions';
    protected $pk        = 'questionID';
    protected $singular_classname = 'PerchMembers_QuestionnaireQuestion';
    protected $default_sort_column = 'sort';
    public $static_fields   = array();

    public function get_for_type($type)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE questionnaireType=' . $this->db->pdb($type) . ' ORDER BY sort ASC, questionID ASC';
        return $this->db->get_rows($sql);
    }

    public function get_next_sort_for_type($type)
    {
        $sql = 'SELECT MAX(sort) FROM ' . $this->table . ' WHERE questionnaireType=' . $this->db->pdb($type);
        $max = (int)$this->db->get_value($sql);

        if ($max <= 0) {
            return 10;
        }

        return $max + 10;
    }

}
?>
