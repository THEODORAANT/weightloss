<?php
    # include the API
    include('../../../../../../core/inc/api.php');

    $API  = new PerchAPI(1.0, 'perch_members');
    $HTML   = $API->get('HTML');
    $Lang   = $API->get('Lang');
    $Paging = $API->get('Paging');

    # Set the page title
    $Perch->page_title = $Lang->get('Affiliate');

    # Do anything you want to do before output is started
    include('../../../modes/_subnav.php');
        $message = false;

        $Affiliate = new PerchMembers_Affiliate($API);
        $Members = new PerchMembers_Members($API);


        $HTML = $API->get('HTML');

            $heading1 = 'Affiliate Payouts';


         /*   echo $HTML->title_panel([
                'heading' => $Lang->get($heading1),
            ], $CurrentUser);*/

              include(PERCH_CORE . '/inc/top.php');


$payoutHistory = $Affiliate->getPayoutHistory();
//print_r($payouts);
    echo $HTML->title_panel([
        'heading' => $Lang->get('History Payouts'),

    ], $CurrentUser);


// Export CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=payouts.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Member ID', 'Total Amount', 'Payout Date', 'Method', 'Reference']);
    $payouts = getPayoutHistory($pdo);

    foreach ($payouts as $row) {
        fputcsv($output, [$row['member_id'], $row['total_amount'], $row['payout_date'], $row['method'], $row['reference']]);
    }
    fclose($output);
    exit;
}

//print_r($payouts);
?>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

 <a class="button button-icon icon-left" href="?export=csv"><div><svg role="img" width="10" height="10" class="icon icon-o-cloud-download"> <use xlink:href="/perch/core/assets/svg/ext.svg#o-cloud-download"></use> </svg><span>Export CSV</span></div></a>
    <table id="myTable" class="display">
        <tr>
            <th>Affiliate ID</th>
            <th>Total Amount</th>
                <th>Status</th>
            <th>Payout Date</th>
            <th>Method</th>
            <th>Reference</th>
        </tr>
        <?php foreach ($payoutHistory as $p): ?>
            <tr>
                <td><?= $p['affiliate_id'] ?></td>
                <td>£<?= number_format($p['amount'], 2) ?></td>
                 <td><?= $p['status'] ?></td>
                <td><?= $p['processed_at'] ?></td>
                <td><?= htmlspecialchars($p['payout_method']) ?></td>
                <td></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <script>
      $(document).ready(function() {
        $('#myTable').DataTable();
      });
    </script>

    <?php
 /*  include('../modes/affiliates.reporting.pre.php');
    # Top layout
    include(PERCH_CORE . '/inc/top.php');


    # Display your page
    include('../modes/affiliates.reporting.post.php');*/

    # Bottom layout
    include(PERCH_CORE . '/inc/btm.php');
?>
