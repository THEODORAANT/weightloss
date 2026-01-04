<?php

class PerchMailer_Template extends PerchAPI_Base
{
    protected $table = 'mailer_templates';
    protected $pk = 'templateID';

    protected $factory_classname = 'PerchMailer_Templates';

    public function render_subject(array $vars = [])
    {
        $Template = $this->api->get('Template');
        $Template->set_from_string($this->templateSubject(), 'mailer');

        return trim($Template->render($vars));
    }

    public function render_html_body(array $vars = [])
    {
        if (!$this->templateHTML()) {
            return '';
        }

        $Template = $this->api->get('Template');
        $Template->set_from_string($this->templateHTML(), 'mailer');

        $html = $Template->render($vars);
        return $Template->apply_runtime_post_processing($html, $vars);
    }

    public function render_plain_body(array $vars = [])
    {
        if (!$this->templatePlain()) {
            return '';
        }

        $Template = $this->api->get('Template');
        $Template->set_from_string($this->templatePlain(), 'mailer');

        $text = $Template->render($vars);
        return trim($Template->apply_runtime_post_processing($text, $vars));
    }
}
