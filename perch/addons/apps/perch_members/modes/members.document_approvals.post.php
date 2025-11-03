<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get('Members awaiting document approval'),
    ], $CurrentUser);

    if ($message) {
        echo $message;
    }

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);
    $Smartbar->add_item([
        'active' => true,
        'title'  => $Lang->get('Pending documents'),
        'link'   => $API->app_nav().'/document-review/',
    ]);
    $Smartbar->add_item([
        'title' => $Lang->get('View members'),
        'link'  => $API->app_nav().'/?status=pending',
    ]);

    echo $Smartbar->render();

    if (PerchUtil::count($members)) {
        echo $AssignmentForm->form_start(false, 'membersclass');

        $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);

        $Listing->add_col([
            'title'     => $Lang->get('Member'),
            'value'     => function($Member) {
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
            'edit_link' => 'edit',
        ]);

        $document_base_path = PERCH_LOGINPATH.'/addons/apps/perch_members/documents/';

        $Listing->add_col([
            'title' => $Lang->get('Pending documents'),
            'value' => function($Member) use ($documents_by_member, $document_base_path, $Lang) {
                $member_id = (int)$Member->id();
                $docs = isset($documents_by_member[$member_id]) ? $documents_by_member[$member_id] : [];

                if (!PerchUtil::count($docs)) {
                    return PerchUtil::html($Lang->get('No documents'));
                }

                $items = [];

                foreach ($docs as $Document) {
                    $doc_name = $Document->documentName();
                    $doc_url = $document_base_path.$doc_name;

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

                    $items[] = '<li><a target="_blank" href="'.PerchUtil::html($doc_url).'">'.PerchUtil::html($doc_name).'</a>'.$meta.'</li>';
                }

                return '<ul class="simple-list">'.implode('', $items).'</ul>';
            },
        ]);

        $Listing->add_col([
            'title' => $Lang->get('Latest upload'),
            'value' => function($Member) use ($documents_by_member, $Lang) {
                $member_id = (int)$Member->id();
                $docs = isset($documents_by_member[$member_id]) ? $documents_by_member[$member_id] : [];

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
            'value' => function($Member) use ($user_choices, $Lang) {
                $member_id = (int)$Member->id();
                $details = $Member->to_array();
                $current = isset($details['document_reviewer_id']) ? (int)$details['document_reviewer_id'] : null;

                $options = '<option value="">'.PerchUtil::html($Lang->get('Unassigned')).'</option>';
                foreach ($user_choices as $choice) {
                    $selected = ($current !== null && (int)$choice['id'] === $current) ? ' selected="selected"' : '';
                    $options .= '<option value="'.(int)$choice['id'].'"'.$selected.'>'.PerchUtil::html($choice['label']).'</option>';
                }

                return '<select name="assignment['.$member_id.']">'.$options.'</select>';
            },
        ]);

        echo $Listing->render($members);

        echo $AssignmentForm->submit_field('save_assignments', 'Save assignments');
        echo $AssignmentForm->form_end();
    } else {
        echo $HTML->warning_message($Lang->get('No members currently awaiting approval.'));
    }
