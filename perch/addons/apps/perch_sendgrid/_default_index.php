<?php

# include the API
include(__DIR__.'/../../../core/inc/api.php');

$API  = new PerchAPI(1.0, 'perch_sendgrid');
$Lang = $API->get('Lang');
$HTML = $API->get('HTML');

# Set the page title
$Perch->page_title = $Lang->get($title);

# Top layout
include(PERCH_CORE . '/inc/top.php');

echo $HTML->title_panel('SendGrid', 'Settings', 'Configure API key and list ID in Settings > Add-ons.');

# Bottom layout
include(PERCH_CORE . '/inc/btm.php');
