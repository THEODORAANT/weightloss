<?php
    echo $HTML->title_panel([
        'heading' => $document_review_heading,
    ], $CurrentUser);

    if ($message) {
        echo $message;
    }

    include(__DIR__.'/_document_review_smartbar.php');

    if (PerchUtil::count($orders)) {
        echo $OrderAssignmentForm->form_start(false, 'ordersdocumentclass');

        $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);

        $Listing->add_col([
            'title'     => $Lang->get('Order'),
            'value'     => function ($Order) {
                $invoice_number = $Order->orderInvoiceNumber();
                if ($invoice_number === '') {
                    $invoice_number = 'Order '.$Order->id();
                }
                return PerchUtil::html($invoice_number);
            },
            'sort'      => 'orderInvoiceNumber',
            'edit_link' => function ($Order) {
                return PERCH_LOGINPATH.'/addons/apps/perch_shop_orders/order/?id='.$Order->id();
            },
        ]);

        $Listing->add_col([
            'title'  => $Lang->get('Order date'),
            'value'  => 'orderCreated',
            'sort'   => 'orderCreated',
            'format' => ['type' => 'date', 'format' => PERCH_DATE_SHORT.' '.PERCH_TIME_SHORT],
        ]);

        $Listing->add_col([
            'title'     => $Lang->get('Member'),
            'value'     => function ($Order) use ($order_member_map, $members_by_id, $Lang) {
                $order_id = (int)$Order->id();
                if (!isset($order_member_map[$order_id])) {
                    return '';
                }

                $member_id = $order_member_map[$order_id];
                if (!isset($members_by_id[$member_id]) || !is_object($members_by_id[$member_id])) {
                    return '';
                }

                $Member = $members_by_id[$member_id];
                $details = $Member->to_array();

                $name_parts = [];
                if (isset($details['first_name']) && $details['first_name']) {
                    $name_parts[] = $details['first_name'];
                }
                if (isset($details['last_name']) && $details['last_name']) {
                    $name_parts[] = $details['last_name'];
                }

                $name = trim(implode(' ', $name_parts));
                if ($name === '') {
                    $name = isset($details['memberEmail']) && $details['memberEmail']
                        ? $details['memberEmail']
                        : '#'.$Member->id();
                }

                $out = PerchUtil::html($name);

                if (isset($details['memberEmail']) && $details['memberEmail']) {
                    if (strtolower($details['memberEmail']) !== strtolower($name)) {
                        $out .= '<br><span class="meta">'.PerchUtil::html($details['memberEmail']).'</span>';
                    }
                }

                return $out;
            },
            'edit_link' => function ($Order) use ($order_member_map, $members_by_id) {
                $order_id = (int)$Order->id();
                if (!isset($order_member_map[$order_id])) {
                    return '';
                }

                $member_id = $order_member_map[$order_id];
                if (!isset($members_by_id[$member_id]) || !is_object($members_by_id[$member_id])) {
                    return '';
                }

                return PERCH_LOGINPATH.'/addons/apps/perch_members/edit/?id='.$members_by_id[$member_id]->id();
            },
        ]);

        $Listing->add_col([
            'title' => $Lang->get('Pending documents'),
            'value' => function ($Order) use (
                $order_member_map,
                $documents_by_member,
                $Lang
            ) {
                $order_id = (int)$Order->id();
                if (!isset($order_member_map[$order_id])) {
                    return PerchUtil::html($Lang->get('No documents'));
                }

                $member_id = $order_member_map[$order_id];
                if (!isset($documents_by_member[$member_id])) {
                    return PerchUtil::html($Lang->get('No documents'));
                }

                $docs = $documents_by_member[$member_id];
                if (!PerchUtil::count($docs)) {
                    return PerchUtil::html($Lang->get('No documents'));
                }

                $items = [];

                foreach ($docs as $Document) {
                    $doc_name = $Document->documentName();
                    $doc_url = PERCH_LOGINPATH.'/addons/apps/perch_members/documents/'.$doc_name;

                    $meta_bits = [];
                    if ($Document->documentType()) {
                        $meta_bits[] = $Document->documentType();
                    }

                    if ($Document->documenUploadDate()) {
                        $meta_bits[] = date('d M Y H:i', strtotime($Document->documenUploadDate()));
                    }

                    $meta = '';
                    if (PerchUtil::count($meta_bits)) {
                        $meta = '<br><span class="meta">'.PerchUtil::html(implode(' • ', $meta_bits)).'</span>';
                    }

                    $items[] = '<li><a target="_blank" href="'.PerchUtil::html($doc_url).'">'.
                        PerchUtil::html($doc_name).'</a>'.$meta.'</li>';
                }

                return '<ul class="simple-list">'.implode('', $items).'</ul>';
            },
        ]);

        $Listing->add_col([
            'title' => $Lang->get('Latest upload'),
            'value' => function ($Order) use ($order_member_map, $documents_by_member, $Lang) {
                $order_id = (int)$Order->id();
                if (!isset($order_member_map[$order_id])) {
                    return PerchUtil::html($Lang->get('Unknown'));
                }

                $member_id = $order_member_map[$order_id];
                if (!isset($documents_by_member[$member_id])) {
                    return PerchUtil::html($Lang->get('Unknown'));
                }

                $docs = $documents_by_member[$member_id];
                if (PerchUtil::count($docs)) {
                    $Document = $docs[0];
                    if ($Document->documenUploadDate()) {
                        return PerchUtil::html(date('d M Y H:i', strtotime($Document->documenUploadDate())));
                    }
                }

                return PerchUtil::html($Lang->get('Unknown'));
            },
        ]);

        $Listing->add_col([
            'title' => $Lang->get('Assigned to'),
            'value' => function ($Order) use ($order_assignments, $user_choices, $Lang) {
                $order_id = (int)$Order->id();
                $current = isset($order_assignments[$order_id]) ? (int)$order_assignments[$order_id] : null;

                $options = '<option value="">'.PerchUtil::html($Lang->get('Unassigned')).'</option>';
                foreach ($user_choices as $choice) {
                    $selected = ($current !== null && (int)$choice['id'] === $current)
                        ? ' selected="selected"'
                        : '';
                    $options .= '<option value="'.(int)$choice['id'].'"'.$selected.'>'.
                        PerchUtil::html($choice['label']).'</option>';
                }

                return '<select name="order_assignment['.$order_id.']">'.$options.'</select>';
            },
        ]);

        echo $Listing->render($orders);

        echo $OrderAssignmentForm->submit_field('save_order_assignments', 'Save assignments');
        echo $OrderAssignmentForm->form_end();
    } else {
        echo $HTML->warning_message($Lang->get('No orders currently awaiting approval.'));
    }
