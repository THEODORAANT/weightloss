<?php

class PerchMailer_Trigger extends PerchAPI_Base
{
    protected $table = 'mailer_triggers';
    protected $pk = 'triggerID';

    protected $factory_classname = 'PerchMailer_Triggers';

    public function template()
    {
        $Templates = new PerchMailer_Templates($this->api);

        if ($this->triggerTemplateID()) {
            return $Templates->find((int) $this->triggerTemplateID());
        }

        return false;
    }
}
