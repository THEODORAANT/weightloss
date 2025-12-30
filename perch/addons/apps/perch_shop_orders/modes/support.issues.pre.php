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
    $current_issue = null;
    $form_defaults = [];

    $Issues = new SupportIssueLogRepository();
    if (!$Issues->table_ready()) {
        $Issues->ensure_table_exists();
    }

    $editing_id = PerchUtil::get('id');
    if ($editing_id) {
        $current_issue = $Issues->find((int)$editing_id);
        if ($current_issue) {
            $form_defaults = $current_issue;
        } else {
            $message = $HTML->failure_message($Lang->get('Issue not found.'));
        }
    }

    $parse_date_time = function ($date_string, $is_end = false) {
        $date_string = trim((string)$date_string);
        if ($date_string === '') {
            return null;
        }

        $formats = [
            'd/m/Y H:i',
            'd/m/y H:i',
            'd/m/Y',
            'd/m/y',
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'Y-m-d',
        ];

        foreach ($formats as $format) {
            $dt = DateTime::createFromFormat($format, $date_string);
            if ($dt instanceof DateTime) {
                if (strpos($format, 'H:i') === false) {
                    $dt->setTime($is_end ? 23 : 0, $is_end ? 59 : 0, $is_end ? 59 : 0);
                }
                return $dt->format('Y-m-d H:i:s');
            }
        }

        return null;
    };

    $requested_export = PerchUtil::get('export') === '1';
    $export_issue_type = PerchUtil::get('export_issue_type', 'complaint');
    $export_from = $parse_date_time(PerchUtil::get('export_from'));
    $export_to = $parse_date_time(PerchUtil::get('export_to'), true);

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

    if ($requested_export) {
        $export_filters = $filters;
        if ($export_issue_type) {
            $export_filters['issueType'] = $export_issue_type;
        }

        if (!$export_from || !$export_to) {
            $message = $HTML->failure_message($Lang->get('Please provide valid from/to date-times.'));
        } else {
            $export_rows = $Issues->list($export_filters, 0, $export_from, $export_to);
            $filename = sprintf('support-issues-%s-to-%s.csv', str_replace('-', '', $export_from), str_replace('-', '', $export_to));
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Issue type', 'Summary', 'Status', 'Event date', 'Created at', 'Member ID', 'Order ID', 'Order number', 'Tracking', 'Details', 'Resolution', 'Logged by']);
            foreach ($export_rows as $row) {
                fputcsv($out, [
                    $row['id'],
                    $row['issueType'],
                    $row['summary'],
                    $row['status'],
                    $row['eventDate'],
                    $row['createdAt'],
                    $row['memberID'],
                    $row['orderID'],
                    $row['orderNumber'],
                    $row['trackingNumber'],
                    $row['details'],
                    $row['resolution'],
                    $row['loggedByName'],
                ]);
            }
            fclose($out);
            exit;
        }
    }

    $submitted = $Form->submitted();

    if ($submitted) {
        $postvars = [
            'issue_id',
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
        $issue_id = isset($data['issue_id']) ? (int)$data['issue_id'] : null;

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
            $Issues->save($data, $issue_id);
            $message = $HTML->success_message($issue_id ? $Lang->get('Support issue updated.') : $Lang->get('Support issue logged.'));

            if ($issue_id) {
                $current_issue = $Issues->find($issue_id);
                $form_defaults = $current_issue ?: [];
            } else {
                $current_issue = null;
                $form_defaults = [];
            }
        }
    }

    if (!empty($form_defaults)) {
        $Form->set_defaults($form_defaults);
    }

    $issues = $Issues->list($filters, 200);
