<?php
$Templates = new PerchMailer_Templates($API);
$TemplateItem = false;
$details = [];
$render_html = '';
$render_plain = '';
$message = false;

if (isset($_GET['id']) && $_GET['id'] !== '') {
    $templateID = (int) $_GET['id'];
    $TemplateItem = $Templates->find($templateID);
    if ($TemplateItem) {
        $details = $TemplateItem->to_array();
        $render_html = $TemplateItem->templateHTML();
        $render_plain = $TemplateItem->templatePlain();
    }
}

if (!$TemplateItem) {
    PerchUtil::redirect($API->app_path() . '/templates/');
}

$heading1 = $Lang->get('Viewing mail template');
$heading2 = $Lang->get('Template details');
