<?php
# include the API
include(__DIR__.'/../../../core/inc/api.php');

$API  = new PerchAPI(1.0, 'perch_notification_logs');
$Lang = $API->get('Lang');
$HTML = $API->get('HTML');

$Perch->page_title = $Lang->get($title);

include('modes/'.$mode.'.pre.php');

include(PERCH_CORE . '/inc/top.php');

include('modes/'.$mode.'.post.php');

include(PERCH_CORE . '/inc/btm.php');
