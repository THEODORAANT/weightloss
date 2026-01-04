<?php
include(__DIR__ . '/../../../core/inc/api.php');

$API  = new PerchAPI(1.0, 'perch_mailer');
$Lang = $API->get('Lang');
$HTML = $API->get('HTML');

$Perch->page_title = $Lang->get($title);

include(__DIR__ . '/modes/' . $mode . '.pre.php');

include(PERCH_CORE . '/inc/top.php');

include(__DIR__ . '/modes/' . $mode . '.post.php');

include(PERCH_CORE . '/inc/btm.php');
