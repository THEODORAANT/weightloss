<?php
class PerchMembers_ChatRepository
{
    private $db;
    private $threads_table;
    private $messages_table;
    private $tables_ready = null;
    private $closures_table;
    private $closures_table_ready = null;
    private $API;
    private $Members;

    public function __construct($API = null, $db = null)
    {
        $this->db = $db ?: PerchDB::fetch();
        $this->threads_table = PERCH_DB_PREFIX . 'chat_threads';
        $this->messages_table = PERCH_DB_PREFIX . 'chat_messages';
        $this->closures_table = PERCH_DB_PREFIX . 'chat_thread_closures';
        $this->API = $API instanceof PerchAPI ? $API : new PerchAPI(1.0, 'perch_members');
    }

    public function tables_ready()
    {
        if ($this->tables_ready !== null) {
            return $this->tables_ready;
        }

        $threads = $this->db->get_value('SHOW TABLES LIKE ' . $this->db->pdb($this->threads_table));
        $messages = $this->db->get_value('SHOW TABLES LIKE ' . $this->db->pdb($this->messages_table));

        $this->tables_ready = ($threads !== false && $threads !== null) && ($messages !== false && $messages !== null);
        return $this->tables_ready;
    }

    private function closures_table_ready()
    {
        if ($this->closures_table_ready !== null) {
            return $this->closures_table_ready;
        }

        $table = $this->db->get_value('SHOW TABLES LIKE ' . $this->db->pdb($this->closures_table));
        $this->closures_table_ready = ($table !== false && $table !== null);

        return $this->closures_table_ready;
    }

    public function get_or_create_thread_for_member($memberID)
    {
        if (!$this->tables_ready()) {
            return null;
        }

        $memberID = (int)$memberID;
        if ($memberID < 1) {
            return null;
        }

        $thread = $this->db->get_row(
            'SELECT * FROM ' . $this->threads_table . ' WHERE memberID = ' . $this->db->pdb($memberID) . ' LIMIT 1'
        );

        if ($thread) {
            return $thread;
        }

        $now = date('Y-m-d H:i:s');
        $this->db->insert($this->threads_table, [
            'memberID' => $memberID,
            'status' => 'open',
            'last_message_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $id = (int)$this->db->insert_id();
        if (!$id) {
            return null;
        }

        return $this->db->get_row(
            'SELECT * FROM ' . $this->threads_table . ' WHERE id = ' . $this->db->pdb($id) . ' LIMIT 1'
        );
    }

    public function get_thread($threadID)
    {
        if (!$this->tables_ready()) {
            return null;
        }

        $threadID = (int)$threadID;
        if ($threadID < 1) {
            return null;
        }

        return $this->db->get_row(
            'SELECT * FROM ' . $this->threads_table . ' WHERE id = ' . $this->db->pdb($threadID) . ' LIMIT 1'
        );
    }

    public function list_threads($opts = [])
    {
        if (!$this->tables_ready()) {
            return [];
        }

        $where = [];
        if (!empty($opts['status'])) {
            $where[] = 'status = ' . $this->db->pdb($opts['status']);
        }

        if (!empty($opts['memberID'])) {
            $where[] = 'memberID = ' . $this->db->pdb((int)$opts['memberID']);
        }

        $sql = 'SELECT * FROM ' . $this->threads_table;
        if (count($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $order = ' ORDER BY COALESCE(last_message_at, created_at) DESC';
        if (!empty($opts['order'])) {
            $order = ' ORDER BY ' . $opts['order'];
        }
        $sql .= $order;

        if (!empty($opts['limit'])) {
            $sql .= ' LIMIT ' . (int)$opts['limit'];
        }

        $rows = $this->db->get_rows($sql);
        if (!$rows) {
            return [];
        }

        $threads = [];
        foreach ($rows as $row) {
            $threads[] = $this->enrich_thread($row);
        }
        return $threads;
    }

    public function get_messages($threadID, $opts = [])
    {
        if (!$this->tables_ready()) {
            return [];
        }

        $threadID = (int)$threadID;
        if ($threadID < 1) {
            return [];
        }

        $where = 'threadID = ' . $this->db->pdb($threadID);
        if (!empty($opts['after_id'])) {
            $where .= ' AND id > ' . $this->db->pdb((int)$opts['after_id']);
        }

        $sql = 'SELECT * FROM ' . $this->messages_table . ' WHERE ' . $where . ' ORDER BY id ASC';
        if (!empty($opts['limit'])) {
            $sql .= ' LIMIT ' . (int)$opts['limit'];
        }

        return $this->db->get_rows($sql) ?: [];
    }

    public function add_member_message($memberID, $body)
    {
        if (!$this->tables_ready()) {
            return null;
        }

        $memberID = (int)$memberID;
        if ($memberID < 1) {
            return null;
        }

        $body = trim((string)$body);
        if ($body === '') {
            return null;
        }

        $thread = $this->get_or_create_thread_for_member($memberID);
        if (!$thread) {
            return null;
        }

        $now = date('Y-m-d H:i:s');
        $this->db->insert($this->messages_table, [
            'threadID' => $thread['id'],
            'sender_type' => 'member',
            'sender_memberID' => $memberID,
            'body' => $body,
            'created_at' => $now,
        ]);

        $this->touch_thread($thread['id'], 'member', [
            'last_member_activity' => $now,
            'last_member_read_at' => $now,
            'status' => 'open',
        ]);

        return $this->db->insert_id();
    }

    public function add_staff_message($threadID, $staffUserID, $body)
    {
        if (!$this->tables_ready()) {
            return null;
        }

        $threadID = (int)$threadID;
        if ($threadID < 1) {
            return null;
        }

        $body = trim((string)$body);
        if ($body === '') {
            return null;
        }

        $thread = $this->get_thread($threadID);
        if (!$thread) {
            return null;
        }

        $now = date('Y-m-d H:i:s');
        $data = [
            'threadID' => $threadID,
            'sender_type' => 'staff',
            'sender_staffID' => (int)$staffUserID ?: null,
            'body' => $body,
            'created_at' => $now,
        ];
        $this->db->insert($this->messages_table, $data);

        $this->touch_thread($threadID, 'staff', [
            'last_staff_activity' => $now,
            'last_staff_read_at' => $now,
        ]);

        return $this->db->insert_id();
    }

    public function mark_thread_read_by_member($threadID)
    {
        if (!$this->tables_ready()) {
            return false;
        }

        $threadID = (int)$threadID;
        if ($threadID < 1) {
            return false;
        }

        return $this->db->update($this->threads_table, [
            'last_member_read_at' => date('Y-m-d H:i:s'),
        ], 'id', $threadID);
    }

    public function mark_thread_read_by_staff($threadID)
    {
        if (!$this->tables_ready()) {
            return false;
        }

        $threadID = (int)$threadID;
        if ($threadID < 1) {
            return false;
        }

        return $this->db->update($this->threads_table, [
            'last_staff_read_at' => date('Y-m-d H:i:s'),
        ], 'id', $threadID);
    }

    public function member_has_unread($memberID)
    {
        if (!$this->tables_ready()) {
            return false;
        }

        $thread = $this->get_or_create_thread_for_member($memberID);
        if (!$thread) {
            return false;
        }

        if ($thread['last_message_from'] !== 'staff') {
            return false;
        }

        if (empty($thread['last_message_at'])) {
            return false;
        }

        if (empty($thread['last_member_read_at'])) {
            return true;
        }

        return strtotime($thread['last_member_read_at']) < strtotime($thread['last_message_at']);
    }

    public function staff_has_unread($threadID)
    {
        if (!$this->tables_ready()) {
            return false;
        }

        $thread = $this->get_thread($threadID);
        if (!$thread) {
            return false;
        }

        if ($thread['last_message_from'] !== 'member') {
            return false;
        }

        if (empty($thread['last_message_at'])) {
            return false;
        }

        if (empty($thread['last_staff_read_at'])) {
            return true;
        }

        return strtotime($thread['last_staff_read_at']) < strtotime($thread['last_message_at']);
    }

    public function count_unread_for_staff()
    {
        if (!$this->tables_ready()) {
            return 0;
        }

        $sql = 'SELECT COUNT(*) FROM ' . $this->threads_table
             . " WHERE last_message_from = 'member'"
             . " AND status != 'closed'"
             . ' AND (last_staff_read_at IS NULL OR last_staff_read_at < last_message_at)';

        return (int)$this->db->get_count($sql);
    }

    public function set_thread_status($threadID, $status)
    {
        if (!$this->tables_ready()) {
            return false;
        }

        $status = $status === 'closed' ? 'closed' : 'open';
        $result = $this->db->update($this->threads_table, [
            'status' => $status,
        ], 'id', (int)$threadID);

        if ($result && $status === 'closed') {
            $this->record_thread_closure($threadID);
        }

        return $result;
    }

    public function get_last_message($threadID)
    {
        if (!$this->tables_ready()) {
            return null;
        }

        return $this->db->get_row(
            'SELECT * FROM ' . $this->messages_table . ' WHERE threadID = ' . $this->db->pdb((int)$threadID) . ' ORDER BY id DESC LIMIT 1'
        );
    }

    public function get_last_closed_message_id($threadID)
    {
        if (!$this->tables_ready()) {
            return 0;
        }

        if (!$this->closures_table_ready()) {
            return 0;
        }

        $row = $this->db->get_row(
            'SELECT last_message_id FROM ' . $this->closures_table
            . ' WHERE threadID = ' . $this->db->pdb((int)$threadID)
            . ' ORDER BY id DESC LIMIT 1'
        );

        if (!$row || !isset($row['last_message_id'])) {
            return 0;
        }

        return (int)$row['last_message_id'];
    }

    private function touch_thread($threadID, $last_from, $extra = [])
    {
        $update = array_merge([
            'last_message_at' => date('Y-m-d H:i:s'),
            'last_message_from' => $last_from,
            'updated_at' => date('Y-m-d H:i:s'),
        ], $extra);

        $this->db->update($this->threads_table, $update, 'id', (int)$threadID);
    }

    private function record_thread_closure($threadID)
    {
        if (!$this->closures_table_ready()) {
            return;
        }

        $threadID = (int)$threadID;
        if ($threadID < 1) {
            return;
        }

        $last_message = $this->get_last_message($threadID);
        $last_message_id = $last_message ? (int)$last_message['id'] : 0;

        $this->db->insert($this->closures_table, [
            'threadID' => $threadID,
            'last_message_id' => $last_message_id > 0 ? $last_message_id : null,
            'closed_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function enrich_thread($thread)
    {
        if (!$thread) {
            return null;
        }

        $member = $this->get_member_factory()->find((int)$thread['memberID']);
        $last_message = $this->get_last_message($thread['id']);

        return [
            'thread' => $thread,
            'member' => $member ? $member->to_array() : null,
            'last_message' => $last_message,
            'staff_has_unread' => $this->staff_has_unread($thread['id']),
        ];
    }

    private function get_member_factory()
    {
        if (!$this->Members) {
            $this->Members = new PerchMembers_Members($this->API);
        }

        return $this->Members;
    }
}
