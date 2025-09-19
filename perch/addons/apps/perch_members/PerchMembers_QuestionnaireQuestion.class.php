<?php
class PerchMembers_QuestionnaireQuestion extends PerchAPI_Base
{
    protected $table  = 'members_questionnaire_questions';
    protected $pk     = 'questionID';

    /**
     * Return the stored options decoded into an array.
     *
     * @return array
     */
    public function option_list()
    {
        $raw = $this->options();
        if (!$raw) {
            return [];
        }

        $decoded = PerchUtil::json_safe_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return [];
    }

    /**
     * Provide a human readable summary of the available options.
     *
     * @return string
     */
    public function option_summary()
    {
        $options = $this->option_list();
        if (!PerchUtil::count($options)) {
            return '';
        }

        $pairs = [];
        foreach ($options as $value => $label) {
            if (is_array($label)) {
                $display = isset($label['label']) ? $label['label'] : implode(' ', $label);
            } else {
                $display = $label;
            }

            $pairs[] = trim($display.' ['.$value.']');
        }

        return implode(', ', $pairs);
    }
}
?>
