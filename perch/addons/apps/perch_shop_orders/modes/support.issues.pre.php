<?php
    include_once(__DIR__ . '/../SupportIssueLogRepository.php');

    $Form = $API->get('Form');
    $HTML = $API->get('HTML');

    $issue_types = [
        ['value' => 'complaint', 'label' => $Lang->get('Complaint')],
        ['value' => 'royal_mail_issue', 'label' => $Lang->get('Royal Mail issue / lost parcel')],
    ];

    $status_options = [
        ['value' => 'open', 'label' => $Lang->get('Open')],
        ['value' => 'in_progress', 'label' => $Lang->get('In progress')],
        ['value' => 'resolved', 'label' => $Lang->get('Resolved')],
    ];

    $message = '';

    $Issues = new SupportIssueLogRepository();
    if (!$Issues->table_ready()) {
        $Issues->ensure_table_exists();
    }

    $submitted = $Form->submitted();

    if ($submitted) {
        $postvars = [
            'issueType',
            'memberID',
            'orderID',
            'orderNumber',
            'trackingNumber',
            'summary',
            'details',
            'status',
            'resolution',
            'eventDate',
        ];

        $data = $Form->receive($postvars);

        $data['issueType'] = $data['issueType'] ?? 'complaint';
        $data['status'] = $data['status'] ?? 'open';

        $data['memberID'] = isset($data['memberID']) && $data['memberID'] !== '' ? (int)$data['memberID'] : null;
        $data['orderID'] = isset($data['orderID']) && $data['orderID'] !== '' ? (int)$data['orderID'] : null;
        $data['orderNumber'] = $data['orderNumber'] ?: null;
        $data['trackingNumber'] = $data['trackingNumber'] ?: null;
        $data['summary'] = trim($data['summary'] ?? '');
        $data['details'] = trim($data['details'] ?? '');
        $data['resolution'] = trim($data['resolution'] ?? '');

        $data['loggedBy'] = (int)$CurrentUser->id();
        $name_parts = [];
        if ($CurrentUser->userGivenName()) {
            $name_parts[] = $CurrentUser->userGivenName();
        }
        if ($CurrentUser->userFamilyName()) {
            $name_parts[] = $CurrentUser->userFamilyName();
        }
        $data['loggedByName'] = trim(implode(' ', $name_parts)) ?: $CurrentUser->userUsername();

        if ($data['summary'] === '') {
            $message = $HTML->failure_message($Lang->get('Please enter a brief summary.'));
        } else {
            $Issues->save($data);
            $message = $HTML->success_message($Lang->get('Support issue logged.'));

            $Form = $API->get('Form');
        }
    }

    $filters = [];

    if (PerchUtil::get('filter_issue_type')) {
        $filters['issueType'] = PerchUtil::get('filter_issue_type');
    }

    if (PerchUtil::get('filter_status')) {
        $filters['status'] = PerchUtil::get('filter_status');
    }

    if (PerchUtil::get('filter_member')) {
        $filters['memberID'] = (int)PerchUtil::get('filter_member');
    }

    if (PerchUtil::get('filter_order')) {
        $filters['orderID'] = (int)PerchUtil::get('filter_order');
    }

    $issues = $Issues->list($filters, 200);
