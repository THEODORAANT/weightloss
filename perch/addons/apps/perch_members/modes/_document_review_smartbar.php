<?php
    if (!isset($document_review_view)) {
        $document_review_view = 'members';
    }

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

    $Smartbar->add_item([
        'active' => $document_review_view === 'members',
        'title'  => $Lang->get('Pending documents'),
        'link'   => $API->app_nav().'/document-review/',
    ]);

    $Smartbar->add_item([
        'active' => $document_review_view === 'orders',
        'title'  => $Lang->get('Pending orders'),
        'link'   => $API->app_nav().'/document-review/?view=orders',
    ]);

    $Smartbar->add_item([
        'title' => $Lang->get('View members'),
        'link'  => $API->app_nav().'/?status=pending',
    ]);

    echo $Smartbar->render();
