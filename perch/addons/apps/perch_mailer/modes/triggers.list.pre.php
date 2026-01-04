<?php
$Triggers = new PerchMailer_Triggers($API);
$Templates = new PerchMailer_Templates($API);

$template_lookup = $Templates->get_template_map();

$Paging = $API->get('Paging');
$Paging->set_per_page(20);

$triggers = $Triggers->all($Paging);
