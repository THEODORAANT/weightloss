<?php
    include('../../../../core/inc/api.php');

    $API  = new PerchAPI(1.0, 'perch_members');
    $HTML = $API->get('HTML');
    $Lang = $API->get('Lang');
    $Paging = $API->get('Paging');
    $Paging->set_per_page(20);

    $Perch->page_title = $Lang->get('Document approvals');
    $Perch->add_css($API->app_path().'/assets/css/members.css');

    include('../modes/_subnav.php');
    $view = PerchRequest::get('view');
    if ($view === 'orders') {
        include('../modes/members.document_orders.pre.php');
    } else {
        include('../modes/members.document_approvals.pre.php');
    }

    include(PERCH_CORE . '/inc/top.php');

    if ($view === 'orders') {
        include('../modes/members.document_orders.post.php');
    } else {
        include('../modes/members.document_approvals.post.php');
    }

    include(PERCH_CORE . '/inc/btm.php');
