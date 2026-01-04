<?php

class PerchMailer_Templates extends PerchMailer_Factory
{
    protected $table = 'mailer_templates';
    protected $pk = 'templateID';
    protected $singular_classname = 'PerchMailer_Template';

    protected $default_sort_column = 'templateTitle';
    protected $created_date_column = 'templateCreated';
    protected $updated_date_column = 'templateUpdated';

    public $static_fields = [
        'templateTitle',
        'templateSlug',
        'templateSubject',
        'templateFromName',
        'templateFromEmail',
        'templateHTML',
        'templatePlain',
        'templateCreated',
        'templateUpdated',
    ];

    public function get_template_map()
    {
        $map = [];
        $templates = $this->all();
        if (PerchUtil::count($templates)) {
            foreach ($templates as $Template) {
                $map[(int) $Template->id()] = $Template->templateTitle();
            }
        }

        return $map;
    }

    public function find_by_slug($slug)
    {
        return $this->get_one_by('templateSlug', $slug);
    }
}
