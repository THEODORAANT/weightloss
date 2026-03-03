<?php

$API  = new PerchAPI(1.0, 'perch_sendgrid');
$HTML = $API->get('HTML');

echo $HTML->title_panel('SendGrid', 'Settings', 'Configure API key and list ID in Settings > Add-ons.');
