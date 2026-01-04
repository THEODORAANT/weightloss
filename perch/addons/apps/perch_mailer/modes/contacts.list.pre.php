<?php
$Contacts = new PerchMailer_Contacts($API);

$imported = PerchRequest::get('imported');
if ($imported !== false && $imported !== null) {
    $message = $HTML->success_message($Lang->get('%s contacts synced from members.'), (int) $imported);
}

$Paging = $API->get('Paging');
$Paging->set_per_page(20);

$contacts = $Contacts->all($Paging);
