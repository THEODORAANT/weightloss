<?php
    # include the API
    include('../../../../core/inc/api.php');

    $API  = new PerchAPI(1.0, 'perch_members');
    $HTML   = $API->get('HTML');
    $Lang   = $API->get('Lang');
    $Paging = $API->get('Paging');
$userId=$_GET["userId"];
$logDir = 'logs';
if(isset($_GET["type"]) && $_GET["type"]=="re-order"){
 $logDir = 'logs/reorder';
}
    $Questionnaires = new PerchMembers_Questionnaires($API);
$logEntries= $Questionnaires->displayUserAnswerHistoryUI($userId,$logDir) ;
// Group entries by question
$grouped = [];

foreach ($logEntries as $entry) {
    $q = $entry['question'];
    $grouped[$q][] = $entry;
}
    # Set the page title
    $Perch->page_title = $Lang->get('History Log');
    echo '<h2>📋 Answer History for <code>';
    echo $userId;
    echo'</code></h2>

    <input type="text" id="searchInput" placeholder="Search questions or answers..." style="width: 100%; padding: 8px; margin-bottom: 12px;">

    <table border="1" width="100%" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th onclick="sortTable(0)">Question 🔼</th>
                <th onclick="sortTable(1)">Answer 🔽</th>
                <th onclick="sortTable(2)">Time 🕒</th>
            </tr>
        </thead>
        <tbody id="historyTable">
';

    foreach ($logEntries as $entry) {
        $q = $entry['question'];
        $a = $entry['answer'];
        $t = $entry['time'];
        $style = (count($grouped[$q]) >= 2) ? " style='background-color: #ffe6e6;'" : "";

$badge = (count($grouped[$q]) >= 2)
    ? "<span style='background: red; color: white; padding: 2px 6px; border-radius: 6px; font-size: 0.8em;'>Changed</span>"
    : "";
        echo "<tr{$style}>";

        echo "<td>{$q} {$badge}</td><td>{$a}</td><td>{$t}</td></tr>";
    }

    echo '</tbody>
    </table>

    <script>
    const searchInput = document.getElementById("searchInput");
    searchInput.addEventListener("keyup", function () {
        const filter = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll("#historyTable tr");

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });

    function sortTable(colIndex) {
        const table = document.getElementById("historyTable");
        const rows = Array.from(table.rows);

        const sorted = rows.sort((a, b) => {
            const valA = a.cells[colIndex].innerText.toLowerCase();
            const valB = b.cells[colIndex].innerText.toLowerCase();
            return valA.localeCompare(valB);
        });

        rows.forEach(row => table.appendChild(row));
    }
    </script>';

    # Bottom layout
    include(PERCH_CORE . '/inc/btm.php');
