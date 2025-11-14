<?php

class PerchMembers_DocumentReminderService
{
    /**
     * @var PerchAPI
     */
    protected $api;

    /**
     * @var string|null
     */
    protected $logFilePath;

    /**
     * @var array<string,array<string,mixed>>
     */
    protected $options = [
        'all_approved' => [
            'label'        => 'All approved',
            'description'  => 'Stop all automated document reminder emails.',
            'send_email'   => false,
        ],
        'all_required' => [
            'label'        => 'All required',
            'description'  => 'Request every outstanding document until they are uploaded.',
            'template'     => 'document_reminder_all_required.html',
            'subject'      => 'Reminder: we still need your documents',
            'send_email'   => true,
            'send_daily'   => true,
        ],
        'proof_id_video' => [
            'label'        => 'Proof of ID / Video',
            'description'  => 'Ask for both proof of ID and video evidence.',
            'template'     => 'document_reminder_proof_id_video.html',
            'subject'      => 'Reminder: ID and video still required',
            'send_email'   => true,
            'send_daily'   => true,
        ],
        'proof_id_previous' => [
            'label'        => 'Proof of ID / Previous prescription',
            'description'  => 'Ask for proof of ID and the previous prescription.',
            'template'     => 'document_reminder_proof_id_previous.html',
            'subject'      => 'Reminder: ID and previous prescription required',
            'send_email'   => true,
            'send_daily'   => true,
        ],
        'video_previous' => [
            'label'        => 'Video / Previous prescription',
            'description'  => 'Ask for a video and the previous prescription.',
            'template'     => 'document_reminder_video_previous.html',
            'subject'      => 'Reminder: video and previous prescription required',
            'send_email'   => true,
            'send_daily'   => true,
        ],
        'id' => [
            'label'        => 'Proof of ID',
            'description'  => 'Request only proof of ID.',
            'template'     => 'document_reminder_id.html',
            'subject'      => 'Reminder: proof of ID required',
            'send_email'   => true,
            'send_daily'   => true,
        ],
        'video' => [
            'label'        => 'Video',
            'description'  => 'Request only a video submission.',
            'template'     => 'document_reminder_video.html',
            'subject'      => 'Reminder: video submission required',
            'send_email'   => true,
            'send_daily'   => true,
        ],
        'previous' => [
            'label'        => 'Previous prescription',
            'description'  => 'Request only the previous prescription document.',
            'template'     => 'document_reminder_previous.html',
            'subject'      => 'Reminder: previous prescription required',
            'send_email'   => true,
            'send_daily'   => true,
        ],
        'thirty_day' => [
            'label'        => '30 day – one off email',
            'description'  => 'Send a single reminder 30 days after the request.',
            'template'     => 'document_reminder_thirty_day.html',
            'subject'      => 'Important: 30-day document reminder',
            'send_email'   => true,
            'send_once'    => true,
        ],
    ];

    public function __construct(PerchAPI $api)
    {
        $this->api = $api;

        $rootDir = realpath(__DIR__ . '/../../../../');
        if ($rootDir === false) {
            $rootDir = dirname(dirname(dirname(dirname(__DIR__))));
        }

        $logDir = $rootDir . DIRECTORY_SEPARATOR . 'logs';

        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }

        $this->logFilePath = PerchUtil::file_path($logDir . DIRECTORY_SEPARATOR . 'document_reminders.log');
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public function get_options()
    {
        return $this->options;
    }

    /**
     * @param string $status
     * @return string
     */
    public function sanitize_status($status)
    {
        $status = strtolower(trim((string) $status));
        $status = str_replace([' ', '–', '-', '/', '\\'], ['_', '_', '_', '_', '_'], $status);

        if ($status === '' || !array_key_exists($status, $this->options)) {
            return 'all_approved';
        }

        return $status;
    }

    /**
     * @param string $status
     * @return array<string,mixed>|null
     */
    public function get_option($status)
    {
        $status = $this->sanitize_status($status);

        return $this->options[$status] ?? null;
    }

    /**
     * @param PerchMembers_Member $Member
     * @return array{status:string,last_status:string,last_sent_at:?string}
     */
    public function get_member_status_data(PerchMembers_Member $Member)
    {
        $properties = $this->decode_properties($Member->memberProperties());

        $status = isset($properties['document_reminder_status'])
            ? (string) $properties['document_reminder_status']
            : 'all_approved';

        if (!array_key_exists($status, $this->options)) {
            $status = 'all_approved';
        }

        return [
            'status'      => $status,
            'last_status' => isset($properties['document_reminder_last_status'])
                ? (string) $properties['document_reminder_last_status']
                : '',
            'last_sent_at' => isset($properties['document_reminder_last_sent_at'])
                ? (string) $properties['document_reminder_last_sent_at']
                : null,
        ];
    }

    /**
     * @param PerchMembers_Member $Member
     * @param string $status
     * @return string Normalised status value that was saved.
     */
    public function update_member_status(PerchMembers_Member $Member, $status)
    {
        $status = $this->sanitize_status($status);

        $properties = $this->decode_properties($Member->memberProperties());
        $previousStatus = isset($properties['document_reminder_status'])
            ? (string) $properties['document_reminder_status']
            : null;

        $properties['document_reminder_status'] = $status;

        if ($previousStatus !== $status) {
            unset($properties['document_reminder_last_status']);
            unset($properties['document_reminder_last_sent_at']);
        }

        $Member->update([
            'memberProperties' => PerchUtil::json_safe_encode($properties),
        ]);

        return $status;
    }

    /**
     * @param array{status:string,last_status:string,last_sent_at:?string} $statusData
     * @param array<string,mixed> $option
     * @return bool
     */
    public function should_send_reminder(array $statusData, array $option)
    {
        if (empty($option['send_email'])) {
            return false;
        }

        $status = $statusData['status'];
        $lastStatus = $statusData['last_status'];
        $lastSentAt = $statusData['last_sent_at'];

        if (!empty($option['send_once'])) {
            return $lastStatus !== $status;
        }

        if ($lastStatus !== $status) {
            return true;
        }

        if (!$lastSentAt) {
            return true;
        }

        $lastSentDate = substr($lastSentAt, 0, 10);
        $today = date('Y-m-d');

        return $lastSentDate !== $today;
    }

    /**
     * @param PerchMembers_Member $Member
     * @param string $status
     * @param array<string,mixed> $option
     * @return bool
     */
    public function send_reminder_email(PerchMembers_Member $Member, $status, array $option)
    {
        $status = $this->sanitize_status($status);

        if (empty($option['send_email']) || !isset($option['template'])) {
            return false;
        }

        $memberEmail = trim((string) $Member->memberEmail());
        if ($memberEmail === '' || !PerchUtil::is_valid_email($memberEmail)) {
            return false;
        }

        $memberData = $Member->to_array();

        $loginPage = '';
        $Settings = $this->api->get('Settings');
        if ($Settings) {
            $loginSetting = $Settings->get('perch_members_login_page');
            if ($loginSetting) {
                $loginPage = str_replace('{returnURL}', '', (string) $loginSetting->val());
            }
        }

        $emailData = array_merge($memberData, [
            'document_reminder_label'       => $option['label'],
            'document_reminder_description' => $option['description'],
            'document_reminder_status'      => $status,
            'login_page'                    => $loginPage,
        ]);

        $Email = $this->api->get('Email');
        $Email->set_template('members/emails/' . $option['template']);
        $Email->set_bulk($emailData);
        $Email->subject($option['subject']);
        $Email->senderName(PERCH_EMAIL_FROM_NAME);
        $Email->senderEmail(PERCH_EMAIL_FROM);
        $Email->recipientEmail($memberEmail);

        return $Email->send();
    }

    /**
     * @param PerchMembers_Member $Member
     * @param string $status
     * @return void
     */
    public function mark_sent(PerchMembers_Member $Member, $status)
    {
        $status = $this->sanitize_status($status);
        $properties = $this->decode_properties($Member->memberProperties());

        $properties['document_reminder_last_status'] = $status;
        $properties['document_reminder_last_sent_at'] = date('Y-m-d H:i:s');

        $Member->update([
            'memberProperties' => PerchUtil::json_safe_encode($properties),
        ]);
    }

    /**
     * @param bool $dryRun
     * @param int|null $memberID
     * @param callable|null $logger Receives a single string argument per message.
     * @return int Number of reminders sent.
     */
    public function process_due_reminders($dryRun = false, $memberID = null, $logger = null)
    {
        $Members = new PerchMembers_Members($this->api);
        $DB = PerchDB::fetch();

        $sql = 'SELECT memberID FROM ' . PERCH_DB_PREFIX . 'members WHERE memberProperties LIKE ' . $DB->pdb('%"document_reminder_status"%');

        if ($memberID) {
            $sql .= ' AND memberID = ' . (int) $memberID;
        }

        $rows = $DB->get_rows($sql);
        if (!PerchUtil::count($rows)) {
            return 0;
        }

        $sent = 0;

        foreach ($rows as $row) {
            $id = isset($row['memberID']) ? (int) $row['memberID'] : 0;
            if ($id <= 0) {
                continue;
            }

            $Member = $Members->find($id);
            if (!$Member instanceof PerchMembers_Member) {
                continue;
            }

            $statusData = $this->get_member_status_data($Member);
            $status = $statusData['status'];
            $option = $this->get_option($status);

            if (!$option || empty($option['send_email'])) {
                continue;
            }

            if (!$this->should_send_reminder($statusData, $option)) {
                continue;
            }

            if ($dryRun) {
                if (is_callable($logger)) {
                    call_user_func($logger, 'Would send "' . $option['label'] . '" reminder to member #' . $Member->id() . ' (' . $Member->memberEmail() . ')');
                }
                continue;
            }

            $sentSuccessfully = $this->send_reminder_email($Member, $status, $option);

            if ($sentSuccessfully) {
                $this->mark_sent($Member, $status);
                $sent++;

                if (is_callable($logger)) {
                    call_user_func($logger, 'Sent "' . $option['label'] . '" reminder to member #' . $Member->id() . ' (' . $Member->memberEmail() . ')');
                }

                $this->log_message('Sent "' . $option['label'] . '" reminder to member #' . $Member->id() . ' (' . $Member->memberEmail() . ')');
            } else {
                if (is_callable($logger)) {
                    call_user_func($logger, 'Failed to send "' . $option['label'] . '" reminder to member #' . $Member->id() . ' (' . $Member->memberEmail() . ')');
                }

                $this->log_message('Failed to send "' . $option['label'] . '" reminder to member #' . $Member->id() . ' (' . $Member->memberEmail() . ')');
            }
        }

        return $sent;
    }

    /**
     * @param string $json
     * @return array<string,mixed>
     */
    protected function decode_properties($json)
    {
        if ($json === '' || $json === null) {
            return [];
        }

        $decoded = PerchUtil::json_safe_decode($json, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param string $message
     * @return void
     */
    protected function log_message($message)
    {
        if (!$this->logFilePath) {
            return;
        }

        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;

        @file_put_contents($this->logFilePath, $line, FILE_APPEND | LOCK_EX);
    }
}
