<?php
include('../../../../core/inc/api.php');

$API  = new PerchAPI(1.0, 'perch_members');
$HTML = $API->get('HTML');
$Lang = $API->get('Lang');

$Perch->page_title = $Lang->get('Chat thread');

include('../modes/_subnav.php');
include('../modes/chat.thread.pre.php');

include(PERCH_CORE . '/inc/top.php');

include('../modes/chat.thread.post.php');

include(PERCH_CORE . '/inc/btm.php');
