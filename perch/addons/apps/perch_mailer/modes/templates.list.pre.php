<?php
$Templates = new PerchMailer_Templates($API);

$Paging = $API->get('Paging');
$Paging->set_per_page(20);

$templates = $Templates->all($Paging);
