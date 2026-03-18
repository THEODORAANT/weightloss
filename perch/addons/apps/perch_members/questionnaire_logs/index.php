<?php
    include('../../../../core/inc/api.php');

    $API  = new PerchAPI(1.0, 'perch_members');
    $HTML = $API->get('HTML');
    $Lang = $API->get('Lang');

    $userId = isset($_GET['userId']) ? (string)$_GET['userId'] : '';
    $logDir = 'logs';
    if (isset($_GET['type']) && $_GET['type'] === 're-order') {
        $logDir = 'logs/reorder';
    }

    $Questionnaires = new PerchMembers_Questionnaires($API);
    $logResult      = $Questionnaires->displayUserAnswerHistoryUI($userId, $logDir);

    $logEntries   = $logResult['entries'];
    $errorMessage = $logResult['error'];
    $metadata     = $logResult['metadata'];

    $grouped = [];
    if (is_array($logEntries)) {
        foreach ($logEntries as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $questionKey = $entry['question'] ?? '';
            if ($questionKey === '') {
                continue;
            }

            if (!isset($grouped[$questionKey])) {
                $grouped[$questionKey] = [];
            }

            $grouped[$questionKey][] = $entry;
        }
    }

    $questionSummaries = [];
    foreach ($grouped as $questionKey => $entries) {
        if (!is_array($entries) || empty($entries)) {
            continue;
        }

        $latestEntry = end($entries);
        if (!is_array($latestEntry)) {
            continue;
        }

        $normalisedAnswers = [];
        $changed = false;
        $latestAnswerValue = $latestEntry['answer'] ?? '';
        $previousAnswerValue = $latestEntry['previous_answer'] ?? '';

        foreach ($entries as $historyEntry) {
            if (!is_array($historyEntry)) {
                continue;
            }

            $answerValue = $historyEntry['answer'] ?? '';
            if ($answerValue === null) {
                $answerValue = '';
            } elseif (is_array($answerValue)) {
                $answerValue = implode(', ', $answerValue);
            } elseif (is_bool($answerValue)) {
                $answerValue = $answerValue ? $Lang->get('Yes') : $Lang->get('No');
            }

            $normalisedAnswers[(string)$answerValue] = true;

            if (!empty($historyEntry['changed'])) {
                $changed = true;
            }
        }

        if (!$changed && count($normalisedAnswers) > 1) {
            $changed = true;
        }

        if ($latestAnswerValue === null) {
            $latestAnswerValue = '';
        } elseif (is_array($latestAnswerValue)) {
            $latestAnswerValue = implode(', ', $latestAnswerValue);
        } elseif (is_bool($latestAnswerValue)) {
            $latestAnswerValue = $latestAnswerValue ? $Lang->get('Yes') : $Lang->get('No');
        }

        if ($previousAnswerValue === null) {
            $previousAnswerValue = '';
        } elseif (is_array($previousAnswerValue)) {
            $previousAnswerValue = implode(', ', $previousAnswerValue);
        } elseif (is_bool($previousAnswerValue)) {
            $previousAnswerValue = $previousAnswerValue ? $Lang->get('Yes') : $Lang->get('No');
        }

        $questionSummaries[] = [
            'question' => (string)$questionKey,
            'answer' => (string)$latestAnswerValue,
            'previous_answer' => (string)$previousAnswerValue,
            'time' => (string)($latestEntry['time'] ?? ''),
            'changed' => $changed,
        ];
    }

    $Perch->page_title = $Lang->get('History Log');

    include(PERCH_CORE . '/inc/top.php');
?>

<h2>📋 <?php echo PerchUtil::html($Lang->get('Answer History for')); ?> <code><?php echo PerchUtil::html($userId); ?></code></h2>

<?php if (!empty($metadata) && is_array($metadata)): ?>
    <div class="info">📄 <?php echo PerchUtil::html($Lang->get('Log created:')); ?>
        <?php echo PerchUtil::html($metadata['registered'] ?? ''); ?>,
        <?php echo PerchUtil::html($Lang->get('IP:')); ?>
        <?php echo PerchUtil::html($metadata['ip_address'] ?? ''); ?>
    </div>
<?php endif; ?>

<?php if ($errorMessage): ?>
    <div class="notification notification-warning">⚠️ <?php echo PerchUtil::html($errorMessage); ?></div>
<?php elseif (empty($questionSummaries)): ?>
    <p><?php echo PerchUtil::html($Lang->get('No answers have been recorded for this user yet.')); ?></p>
<?php else: ?>
    <input
        type="text"
        id="searchInput"
        placeholder="<?php echo PerchUtil::html($Lang->get('Search questions or answers…')); ?>"
        style="width: 100%; padding: 8px; margin-bottom: 12px;"
    >

    <table class="listing" width="100%" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th onclick="sortTable(0)"><?php echo PerchUtil::html($Lang->get('Question')); ?> 🔼</th>
                <th onclick="sortTable(1)"><?php echo PerchUtil::html($Lang->get('Answer')); ?> 🔽</th>
                <th onclick="sortTable(2)"><?php echo PerchUtil::html($Lang->get('Time')); ?> 🕒</th>
            </tr>
        </thead>
        <tbody id="historyTable">
        <?php foreach ($questionSummaries as $entry):
            if (!is_array($entry)) {
                continue;
            }

            $question       = (string)($entry['question'] ?? '');
            $answerValue    = $entry['answer'] ?? '';
            $timeValue      = (string)($entry['time'] ?? '');
            $rowHighlight   = (!empty($entry['changed']))
                ? ' style="background-color: #ffe6e6;"'
                : '';
            $badgeHtml      = (!empty($entry['changed']))
                ? " <span class=\"badge\">" . PerchUtil::html($Lang->get('Changed')) . '</span>'
                : '';

            if ($answerValue === null) {
                $answerValue = '';
            } elseif (is_array($answerValue)) {
                $answerValue = implode(', ', $answerValue);
            } elseif (is_bool($answerValue)) {
                $answerValue = $answerValue ? $Lang->get('Yes') : $Lang->get('No');
            }

            $previousValue = $entry['previous_answer'] ?? '';
            if (is_array($previousValue)) {
                $previousValue = implode(', ', $previousValue);
            }

            $questionCell = PerchUtil::html($question) . $badgeHtml;
            $answerCell   = PerchUtil::html((string)$answerValue);
            $timeCell     = PerchUtil::html($timeValue !== '' ? $timeValue : '-');

            $previousInfo = '';
            if ($previousValue !== '' && $previousValue !== null && $previousValue !== $answerValue) {
                $previousInfo = '<br><small>'
                    . PerchUtil::html($Lang->get('Previous:'))
                    . ' '
                    . PerchUtil::html((string)$previousValue)
                    . '</small>';
            }
        ?>
            <tr<?php echo $rowHighlight; ?>>
                <td><?php echo $questionCell; ?></td>
                <td><?php echo $answerCell, $previousInfo; ?></td>
                <td><?php echo $timeCell; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <style>
        .badge {
            background: #d9534f;
            color: #fff;
            padding: 2px 6px;
            border-radius: 6px;
            font-size: 0.8em;
            margin-left: 4px;
        }

        .notification {
            padding: 10px;
            margin-bottom: 12px;
            border-radius: 4px;
        }

        .notification-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
        }
    </style>

    <script>
        const searchInput = document.getElementById('searchInput');
        const historyTable = document.getElementById('historyTable');

        if (searchInput && historyTable) {
            searchInput.addEventListener('keyup', () => {
                const filter = searchInput.value.toLowerCase();
                historyTable.querySelectorAll('tr').forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                });
            });
        }

        function sortTable(colIndex) {
            if (!historyTable) return;

            const rows = Array.from(historyTable.rows);
            rows.sort((a, b) => {
                const valA = a.cells[colIndex].innerText.toLowerCase();
                const valB = b.cells[colIndex].innerText.toLowerCase();
                return valA.localeCompare(valB);
            });

            rows.forEach(row => historyTable.appendChild(row));
        }
    </script>
<?php endif; ?>

<?php include(PERCH_CORE . '/inc/btm.php'); ?>
