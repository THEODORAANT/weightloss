<?php
$Triggers = new PerchMailer_Triggers($API);
$Templates = new PerchMailer_Templates($API);

$TriggerItem = false;
$message = false;
$details = [];

if (isset($_GET['id']) && $_GET['id'] != '') {
    $triggerID = (int) $_GET['id'];
    $TriggerItem = $Triggers->find($triggerID);
    $details = $TriggerItem ? $TriggerItem->to_array() : [];
    $heading1 = $Lang->get('Editing a mail trigger');
} else {
    $triggerID = false;
    $heading1 = $Lang->get('Adding a mail trigger');
}

$heading2 = $Lang->get('Trigger details');

$template_options = [];
$templates_list = $Templates->all();
if (PerchUtil::count($templates_list)) {
    foreach ($templates_list as $TemplateItem) {
        $template_options[] = $TemplateItem->templateTitle() . '|' . $TemplateItem->id();
    }
}

$options_str = htmlspecialchars(implode(',', $template_options), ENT_QUOTES);

$template_def = '
    <perch:mailer type="text" id="triggerTitle" label="Title" required="true" />
    <perch:mailer type="slug" id="triggerSlug" label="Slug" for="triggerTitle" required="true" help="Used when calling perch_mailer_trigger." />
    <perch:mailer type="textarea" id="triggerDescription" label="Description" size="l" />
    <perch:mailer type="select" id="triggerTemplateID" label="Template" options="' . $options_str . '" allowempty="true" />
    <perch:mailer type="checkbox" id="triggerActive" label="Active" value="1" divider-before="Status" />
';

$Template = $API->get('Template');
$Template->set_from_string($template_def, 'mailer');

$Form = $API->get('Form');
$Form->handle_empty_block_generation($Template);
$Form->set_required_fields_from_template($Template, $details);

if ($Form->submitted()) {
    $data = $Form->get_posted_content($Template, $Triggers, $TriggerItem);

    if (!isset($data['triggerActive'])) {
        $data['triggerActive'] = 0;
    }

    $existing = $Triggers->get_one_by('triggerSlug', $data['triggerSlug']);
    if ($existing && (!$TriggerItem || $existing->id() !== $TriggerItem->id())) {
        $Form->messages['triggerSlug'] = $Lang->get('That slug is already in use.');
    }

    if ($Form->validate() && !isset($Form->messages['triggerSlug'])) {
        $data['triggerUpdated'] = date('Y-m-d H:i:s');
        if ($TriggerItem) {
            $result = $TriggerItem->update($data);
        } else {
            $data['triggerCreated'] = date('Y-m-d H:i:s');
            $TriggerItem = $Triggers->create($data);
            $result = (bool) $TriggerItem;
        }

        if ($result) {
            $message = $HTML->success_message('Trigger successfully saved.');
            $details = $TriggerItem->to_array();
        } else {
            $message = $HTML->failure_message('Sorry, that trigger could not be saved.');
        }
    }
}
