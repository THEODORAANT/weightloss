<?php
    include(__DIR__.'/../../../core/inc/api.php');

    $API  = new PerchAPI(1.0, 'perch_products_match_pharmacy');
    $Lang = $API->get('Lang');
    $HTML = $API->get('HTML');
    $Paging = $API->get('Paging');

    $Perch->page_title = $Lang->get($title);

    include('modes/_subnav.php');
    include('modes/'.$mode.'.pre.php');

    include(PERCH_CORE . '/inc/top.php');
    include('modes/'.$mode.'.post.php');
    include(PERCH_CORE . '/inc/btm.php');
