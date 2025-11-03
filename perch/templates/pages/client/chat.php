<?php
if (!perch_member_logged_in()) {
    PerchUtil::redirect('/client');
}

perch_layout('client/header', [
    'page_title' => 'Chat',
]);

$Session = PerchMembers_Session::fetch();
$memberID = (int)$Session->get('memberID');
$memberEmail = perch_member_get('email');

$ChatRepo = new PerchMembers_ChatRepository();

if (!$ChatRepo->tables_ready()) {
    echo '<div class="container mt-5"><div class="alert alert-warning">'
        . 'Chat is not available yet. Please run the SQL in <code>sql/create_chat_tables.sql</code> to create the chat tables.'
        . '</div></div>';
    perch_layout('getStarted/footer');
    return;
}

$thread = $ChatRepo->get_or_create_thread_for_member($memberID);

$respond_with_json = function (array $payload) {
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
};

if (isset($_GET['fetch']) && $_GET['fetch'] === 'messages') {
    $after = isset($_GET['after']) ? (int)$_GET['after'] : null;
    $messages = $thread ? $ChatRepo->get_messages($thread['id'], ['after_id' => $after]) : [];
    if ($thread) {
        $ChatRepo->mark_thread_read_by_member($thread['id']);
    }
    $respond_with_json(['messages' => format_messages($messages, $memberID)]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = trim((string)($_POST['message'] ?? ''));
    if ($body !== '') {
        $messageID = $ChatRepo->add_member_message($memberID, $body);
        if ($messageID && $thread) {
            $messages = $ChatRepo->get_messages($thread['id'], ['after_id' => $messageID - 1]);
            $ChatRepo->mark_thread_read_by_member($thread['id']);
            if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest') {
                $respond_with_json(['messages' => format_messages($messages, $memberID)]);
            }
        }
    }

    if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest') {
        $respond_with_json(['messages' => []]);
    }

    PerchUtil::redirect('/client/chat');
}

$messages = $thread ? $ChatRepo->get_messages($thread['id']) : [];
if ($thread) {
    $ChatRepo->mark_thread_read_by_member($thread['id']);
}

function format_messages(array $messages, int $memberID)
{
    $result = [];
    foreach ($messages as $message) {
        $result[] = [
            'id' => (int)$message['id'],
            'body' => nl2br(htmlspecialchars($message['body'], ENT_QUOTES, 'UTF-8')),
            'sender' => $message['sender_type'] === 'member' ? 'You' : 'Support',
            'is_member' => $message['sender_type'] === 'member',
            'timestamp' => date('d M Y H:i', strtotime($message['created_at'])),
        ];
    }
    return $result;
}
?>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex flex-column flex-md-row align-items-md-center justify-content-between">
          <div>
            <h5 class="mb-1">Chat with Support</h5>
            <small><?php echo htmlspecialchars($memberEmail, ENT_QUOTES, 'UTF-8'); ?></small>
          </div>
          <div class="status-badge mt-2 mt-md-0">
            <span class="badge <?php echo ($thread && $thread['status'] === 'closed') ? 'bg-secondary' : 'bg-success'; ?>">
              <?php echo ($thread && $thread['status'] === 'closed') ? 'Closed' : 'Open'; ?>
            </span>
          </div>
        </div>
        <div class="card-body chat-body" id="chatMessages" data-last-id="<?php echo $messages ? (int)end($messages)['id'] : 0; ?>">
          <?php if (!$messages): ?>
            <div class="text-center text-muted py-5" id="chatPlaceholder">
              <p class="mb-1">You haven&apos;t started a conversation yet.</p>
              <p class="mb-0">Send us a message below and someone from the team will reply shortly.</p>
            </div>
          <?php else: ?>
            <?php foreach ($messages as $message): ?>
              <div class="chat-message <?php echo $message['sender_type'] === 'member' ? 'chat-message-member' : 'chat-message-staff'; ?>" data-message-id="<?php echo (int)$message['id']; ?>">
                <div class="chat-message-meta">
                  <strong><?php echo $message['sender_type'] === 'member' ? 'You' : 'Support'; ?></strong>
                  <span class="text-muted"><?php echo date('d M Y H:i', strtotime($message['created_at'])); ?></span>
                </div>
                <div class="chat-message-body"><?php echo nl2br(htmlspecialchars($message['body'], ENT_QUOTES, 'UTF-8')); ?></div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <div class="card-footer">
          <?php if ($thread && $thread['status'] === 'closed'): ?>
            <div class="alert alert-info mb-0">This conversation has been closed. Start a new message to reopen it.</div>
          <?php endif; ?>
          <form id="chatForm" method="post" class="mt-3">
            <div class="mb-3">
              <label for="chatMessage" class="form-label">Message</label>
              <textarea class="form-control" id="chatMessage" name="message" rows="3" placeholder="Type your message"></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted">Support replies will appear instantly.</small>
              <button type="submit" class="btn btn-primary">Send</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.chat-body {
  max-height: 60vh;
  overflow-y: auto;
  background: #f9fafb;
}
.chat-message {
  background: #ffffff;
  border-radius: 12px;
  padding: 12px 16px;
  margin-bottom: 12px;
  position: relative;
  box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
}
.chat-message-member {
  border-left: 4px solid #3328bf;
}
.chat-message-staff {
  border-left: 4px solid #28a745;
}
.chat-message-meta {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 6px;
  font-size: 0.85rem;
}
.chat-message-body {
  white-space: pre-wrap;
  font-size: 0.95rem;
}
</style>

<script>
(function() {
  const form = document.getElementById('chatForm');
  const textarea = document.getElementById('chatMessage');
  const messagesWrap = document.getElementById('chatMessages');
  let lastId = parseInt(messagesWrap.dataset.lastId || '0', 10);

  function appendMessages(list) {
    if (!Array.isArray(list) || !list.length) return;
    const placeholder = document.getElementById('chatPlaceholder');
    if (placeholder) {
      placeholder.remove();
    }

    list.forEach(msg => {
      const container = document.createElement('div');
      container.className = 'chat-message ' + (msg.is_member ? 'chat-message-member' : 'chat-message-staff');
      container.dataset.messageId = msg.id;
      container.innerHTML = `
        <div class="chat-message-meta">
          <strong>${msg.sender}</strong>
          <span class="text-muted">${msg.timestamp}</span>
        </div>
        <div class="chat-message-body">${msg.body}</div>
      `;
      messagesWrap.appendChild(container);
      lastId = Math.max(lastId, msg.id);
    });

    messagesWrap.scrollTop = messagesWrap.scrollHeight;
    messagesWrap.dataset.lastId = String(lastId);
  }

  function fetchMessages() {
    fetch(window.location.pathname + '?fetch=messages&after=' + lastId, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(response => response.json())
      .then(data => {
        if (data && Array.isArray(data.messages)) {
          appendMessages(data.messages);
        }
      })
      .catch(() => {});
  }

  if (form) {
    form.addEventListener('submit', function(evt) {
      evt.preventDefault();
      const value = textarea.value.trim();
      if (!value) return;

      const formData = new FormData();
      formData.append('message', value);

      fetch(window.location.pathname, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data && Array.isArray(data.messages)) {
            appendMessages(data.messages);
            textarea.value = '';
          }
        })
        .catch(() => form.submit());
    });
  }

  fetchMessages();
  setInterval(fetchMessages, 5000);
})();
</script>

<?php perch_layout('getStarted/footer'); ?>
