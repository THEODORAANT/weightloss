<?php

class PerchMailer_Triggers extends PerchMailer_Factory
{
    protected $table = 'mailer_triggers';
    protected $pk = 'triggerID';
    protected $singular_classname = 'PerchMailer_Trigger';

    protected $default_sort_column = 'triggerTitle';
    protected $created_date_column = 'triggerCreated';
    protected $updated_date_column = 'triggerUpdated';

    public $static_fields = [
        'triggerTitle',
        'triggerSlug',
        'triggerDescription',
        'triggerTemplateID',
        'triggerActive',
        'triggerCreated',
        'triggerUpdated',
    ];

    public function find_by_slug($slug)
    {
        return $this->get_one_by('triggerSlug', $slug);
    }
}
