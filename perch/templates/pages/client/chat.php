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
        $created_at = strtotime($message['created_at']);
        $result[] = [
            'id' => (int)$message['id'],
            'body' => nl2br(htmlspecialchars($message['body'], ENT_QUOTES, 'UTF-8')),
            'sender' => $message['sender_type'] === 'member' ? 'You' : 'Support',
            'is_member' => $message['sender_type'] === 'member',
            'timestamp' => date('d M Y H:i', $created_at),
            'timestamp_iso' => date('c', $created_at),
        ];
    }
    return $result;
}
?>

<section class="client-section py-5">
  <div class="container all_content">
    <div class="page-heading text-center mb-5">
      <h1 class="fw-bolder mb-3">Chat with our care team</h1>
      <p class="lead mb-0">Have a question about your treatment or account? Send us a message and our clinicians will get back to you soon.</p>
    </div>

    <div class="row g-4 justify-content-center">
      <div class="col-xl-8 col-lg-9">
        <div class="chat-panel">
          <div class="chat-panel__header">
            <div class="chat-panel__title">
              <h2>Support inbox</h2>
              <p><?php echo htmlspecialchars($memberEmail, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <div class="chat-panel__status <?php echo ($thread && $thread['status'] === 'closed') ? 'is-closed' : 'is-open'; ?>">
              <span class="chat-panel__status-dot" aria-hidden="true"></span>
              <?php echo ($thread && $thread['status'] === 'closed') ? 'Closed' : 'Active'; ?>
            </div>
          </div>

          <div class="chat-panel__body" id="chatMessages" data-last-id="<?php echo $messages ? (int)end($messages)['id'] : 0; ?>">
            <?php if (!$messages): ?>
              <div class="chat-empty" id="chatPlaceholder">
                <h3>Start a new conversation</h3>
                <p>Share as much detail as you can – this helps our team reply quickly with the best guidance.</p>
              </div>
            <?php else: ?>
              <?php foreach ($messages as $message): ?>
                <div class="chat-message <?php echo $message['sender_type'] === 'member' ? 'chat-message--member' : 'chat-message--staff'; ?>" data-message-id="<?php echo (int)$message['id']; ?>">
                  <div class="chat-message__meta">
                    <strong><?php echo $message['sender_type'] === 'member' ? 'You' : 'Support'; ?></strong>
                    <?php $messageTime = strtotime($message['created_at']); ?>
                    <time datetime="<?php echo date('c', $messageTime); ?>"><?php echo date('d M Y H:i', $messageTime); ?></time>
                  </div>
                  <div class="chat-message__body"><?php echo nl2br(htmlspecialchars($message['body'], ENT_QUOTES, 'UTF-8')); ?></div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <div class="chat-panel__footer">
            <?php if ($thread && $thread['status'] === 'closed'): ?>
              <div class="chat-panel__alert">This conversation has been closed. Send another message to reopen it.</div>
            <?php endif; ?>

            <form id="chatForm" method="post" class="chat-form">
              <label class="chat-form__label" for="chatMessage">Message</label>
              <textarea class="chat-form__input" id="chatMessage" name="message" rows="3" placeholder="Type your message here"></textarea>
              <div class="chat-form__actions">
                <small>Support replies will appear instantly.</small>
                <button type="submit" class="chat-form__submit">Send message</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-xl-4 col-lg-9">
        <aside class="support-card">
          <h3>Helpful tips</h3>
          <ul>
            <li>Reply directly in this chat to keep your conversation in one place.</li>
            <li>Attach clinical details or order numbers if you&apos;re following up on documents.</li>
            <li>We aim to respond within a few working hours. Urgent medical issues? Contact emergency services.</li>
          </ul>
          <div class="support-card__footer">
            <span class="support-card__icon" aria-hidden="true"><i class="fa-solid fa-clock"></i></span>
            <div>
              <strong>Support hours</strong>
              <p class="mb-0">Mon–Fri, 9am–6pm</p>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>
</section>

<style>
.client-section {
  background: linear-gradient(180deg, #f8f9ff 0%, #ffffff 100%);
}

.chat-panel {
  background: #ffffff;
  border-radius: 24px;
  box-shadow: 0 24px 48px rgba(15, 23, 42, 0.08);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  min-height: 520px;
}

.chat-panel__header {
  padding: 28px 32px 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 24px;
  border-bottom: 1px solid rgba(15, 23, 42, 0.08);
}

.chat-panel__title h2 {
  font-size: 1.5rem;
  margin-bottom: 6px;
  color: #111827;
}

.chat-panel__title p {
  margin: 0;
  color: #6b7280;
  font-size: 0.95rem;
}

.chat-panel__status {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  font-weight: 600;
  padding: 8px 14px;
  border-radius: 999px;
  letter-spacing: 0.02em;
}

.chat-panel__status.is-open {
  color: #166534;
  background: rgba(22, 101, 52, 0.12);
}

.chat-panel__status.is-closed {
  color: #52525b;
  background: rgba(79, 70, 229, 0.12);
}

.chat-panel__status-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: currentColor;
}

.chat-panel__body {
  padding: 32px;
  background: linear-gradient(180deg, rgba(248, 250, 255, 0.9) 0%, rgba(255, 255, 255, 0.9) 100%);
  flex: 1;
  overflow-y: auto;
}

.chat-empty {
  text-align: center;
  max-width: 420px;
  margin: 60px auto;
  color: #4b5563;
}

.chat-empty h3 {
  font-size: 1.35rem;
  margin-bottom: 12px;
  color: #1f2937;
}

.chat-message {
  background: #ffffff;
  border-radius: 16px;
  padding: 16px 18px;
  margin-bottom: 14px;
  box-shadow: 0 12px 30px rgba(71, 85, 105, 0.12);
  border: 1px solid rgba(148, 163, 184, 0.18);
}

.chat-message--member {
  border-left: 5px solid #4338ca;
}

.chat-message--staff {
  border-left: 5px solid #0ea5e9;
}

.chat-message__meta {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: 18px;
  font-size: 0.85rem;
  color: #64748b;
  margin-bottom: 8px;
}

.chat-message__body {
  white-space: pre-wrap;
  font-size: 0.98rem;
  color: #1f2937;
  line-height: 1.55;
}

.chat-panel__footer {
  padding: 28px 32px 32px;
  border-top: 1px solid rgba(15, 23, 42, 0.08);
  background: #ffffff;
}

.chat-panel__alert {
  background: rgba(79, 70, 229, 0.08);
  color: #4338ca;
  padding: 12px 16px;
  border-radius: 14px;
  font-size: 0.95rem;
  margin-bottom: 16px;
}

.chat-form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.chat-form__label {
  font-weight: 600;
  color: #1f2937;
}

.chat-form__input {
  border: 1px solid rgba(148, 163, 184, 0.6);
  border-radius: 14px;
  padding: 14px 16px;
  font-size: 1rem;
  resize: vertical;
  min-height: 120px;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.chat-form__input:focus {
  border-color: #4338ca;
  outline: none;
  box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
}

.chat-form__actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  color: #6b7280;
  font-size: 0.9rem;
}

.chat-form__submit {
  background: linear-gradient(90deg, #4338ca 0%, #6366f1 100%);
  color: #ffffff;
  border: none;
  border-radius: 999px;
  padding: 10px 22px;
  font-weight: 600;
  letter-spacing: 0.01em;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.chat-form__submit:hover {
  transform: translateY(-1px);
  box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
}

.support-card {
  background: #ffffff;
  border-radius: 24px;
  padding: 28px;
  box-shadow: 0 18px 36px rgba(15, 23, 42, 0.08);
  border: 1px solid rgba(148, 163, 184, 0.18);
}

.support-card h3 {
  font-size: 1.25rem;
  margin-bottom: 14px;
  color: #111827;
}

.support-card ul {
  padding-left: 20px;
  color: #4b5563;
  line-height: 1.6;
  margin-bottom: 22px;
}

.support-card__footer {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px;
  border-radius: 18px;
  background: rgba(99, 102, 241, 0.08);
  color: #312e81;
}

.support-card__icon {
  width: 46px;
  height: 46px;
  border-radius: 14px;
  background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
  display: inline-flex;
  justify-content: center;
  align-items: center;
  color: #ffffff;
  font-size: 1.2rem;
}

@media (max-width: 992px) {
  .chat-panel__header,
  .chat-panel__body,
  .chat-panel__footer {
    padding: 24px;
  }
}

@media (max-width: 576px) {
  .chat-form__actions {
    flex-direction: column;
    align-items: flex-start;
  }

  .chat-form__submit {
    width: 100%;
    text-align: center;
  }
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
      const roleClass = msg.is_member ? 'chat-message--member' : 'chat-message--staff';
      const timeAttr = msg.timestamp_iso ? ` datetime="${msg.timestamp_iso}"` : '';
      container.className = 'chat-message ' + roleClass;
      container.dataset.messageId = msg.id;
      container.innerHTML = `
        <div class="chat-message__meta">
          <strong>${msg.sender}</strong>
          <time${timeAttr}>${msg.timestamp}</time>
        </div>
        <div class="chat-message__body">${msg.body}</div>
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
