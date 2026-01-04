<?php
$Contacts = new PerchMailer_Contacts($API);
$ContactItem = false;
$message = false;
$details = [];

if (isset($_GET['id']) && $_GET['id'] != '') {
    $contactID = (int) $_GET['id'];
    $ContactItem = $Contacts->find($contactID);
    $details = $ContactItem ? $ContactItem->to_array() : [];
    $heading1 = $Lang->get('Editing a mail contact');
} else {
    $contactID = false;
    $heading1 = $Lang->get('Adding a mail contact');
}

$heading2 = $Lang->get('Contact details');

$Template = $API->get('Template');
$Template->set('mailer/contact.html', 'mailer');

$Form = $API->get('Form');
$Form->handle_empty_block_generation($Template);
$Form->set_required_fields_from_template($Template, $details);

if ($Form->submitted()) {
    $data = $Form->get_posted_content($Template, $Contacts, $ContactItem);

    if (!isset($data['contactStatus']) || $data['contactStatus'] === '') {
        $data['contactStatus'] = 'active';
    }

    $existing = $Contacts->get_one_by('contactEmail', $data['contactEmail']);
    if ($existing && (!$ContactItem || $existing->id() !== $ContactItem->id())) {
        $Form->messages['contactEmail'] = $Lang->get('That email is already stored as a contact.');
    }

    if ($Form->validate() && !isset($Form->messages['contactEmail'])) {
        $data['contactUpdated'] = date('Y-m-d H:i:s');

        if ($ContactItem) {
            $result = $ContactItem->update($data);
        } else {
            $data['contactCreated'] = date('Y-m-d H:i:s');
            $ContactItem = $Contacts->create($data);
            $result = (bool) $ContactItem;
        }

        if ($result) {
            $message = $HTML->success_message('Contact successfully saved.');
            $details = $ContactItem->to_array();
        } else {
            $message = $HTML->failure_message('Sorry, that contact could not be saved.');
        }
    }
}
