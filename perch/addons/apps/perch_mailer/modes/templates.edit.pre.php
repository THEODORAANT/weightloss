<?php
$Templates = new PerchMailer_Templates($API);
$TemplateItem = false;
$message = false;

if (isset($_GET['id']) && $_GET['id'] != '') {
    $templateID = (int) $_GET['id'];
    $TemplateItem = $Templates->find($templateID);
    $details = $TemplateItem ? $TemplateItem->to_array() : [];
    $heading1 = $Lang->get('Editing a mail template');
} else {
    $templateID = false;
    $details = [];
    $heading1 = $Lang->get('Adding a mail template');
}

$heading2 = $Lang->get('Template details');

$Template = $API->get('Template');
$Template->set('mailer/template.html', 'mailer');

$Form = $API->get('Form');
$Form->handle_empty_block_generation($Template);
$Form->set_required_fields_from_template($Template, $details);

if ($Form->submitted()) {
    $data = $Form->get_posted_content($Template, $Templates, $TemplateItem);

    $existing = $Templates->get_one_by('templateSlug', $data['templateSlug']);
    if ($existing && (!$TemplateItem || $existing->id() !== $TemplateItem->id())) {
        $Form->messages['templateSlug'] = $Lang->get('That slug is already in use.');
    }

    if ($Form->validate() && !isset($Form->messages['templateSlug'])) {
        $data['templateUpdated'] = date('Y-m-d H:i:s');

        if ($TemplateItem) {
            $result = $TemplateItem->update($data);
        } else {
            $data['templateCreated'] = date('Y-m-d H:i:s');
            $TemplateItem = $Templates->create($data);
            $result = (bool) $TemplateItem;
        }

        if ($result) {
            $message = $HTML->success_message('Template successfully saved.');
            $details = $TemplateItem->to_array();
        } else {
            $message = $HTML->failure_message('Sorry, that template could not be saved.');
        }
    }
}
