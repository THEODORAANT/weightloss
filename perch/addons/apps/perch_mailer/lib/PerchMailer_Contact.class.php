<?php

class PerchMailer_Contact extends PerchAPI_Base
{
    protected $table = 'mailer_contacts';
    protected $pk = 'contactID';

    protected $factory_classname = 'PerchMailer_Contacts';

    public function full_name()
    {
        return trim($this->contactFirstName() . ' ' . $this->contactLastName());
    }

    public function to_template_array()
    {
        $data = $this->to_array();
        $dynamic = [];

        if (isset($data['contactDynamicFields'])) {
            $dynamic = PerchUtil::json_safe_decode($data['contactDynamicFields'], true);
            if (!is_array($dynamic)) {
                $dynamic = [];
            }
        }

        unset($data['contactDynamicFields']);

        $data = array_merge($data, $dynamic);
        $data['full_name'] = $this->full_name();

        return $data;
    }
}
