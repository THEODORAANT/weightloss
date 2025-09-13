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
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE questionnaireType=' . $this->db->pdb($type) . ' ORDER BY sort ASC';
        return $this->db->get_rows($sql);
    }
}
?>
