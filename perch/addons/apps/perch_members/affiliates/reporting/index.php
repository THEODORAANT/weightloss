<?php
    # include the API
    include('../../../../../core/inc/api.php');

    $API  = new PerchAPI(1.0, 'perch_members');
    $HTML   = $API->get('HTML');
    $Lang   = $API->get('Lang');
    $Paging = $API->get('Paging');

    # Set the page title
    $Perch->page_title = $Lang->get('Reporting Affiliate');

    # Do anything you want to do before output is started
    include('../../modes/_subnav.php');
        $Members = new PerchMembers_Members($API);
        $message = false;

        $Affiliate = new PerchMembers_Affiliate($API);


        $HTML = $API->get('HTML');

        if (isset($_GET['id']) && $_GET['id']!='') {
            $memberID = (int) $_GET['id'];
            $Member = $Members->find($memberID);
            $details = $Member->to_array();

            $heading1 = 'Reporting Affiliate';

        }
            echo $HTML->title_panel([
                'heading' => $Lang->get($heading1),
            ], $CurrentUser);

              include(PERCH_CORE . '/inc/top.php');
    $commissions=$Affiliate->getMemberCommissions($details["affID"]);


$totalsByTier =$Affiliate->getMemberSumCommissions($details["affID"]);

echo "<h2> Commission Report for Affiliate:".$details["affID"]."</h2>
<table border='1'>
    <tr>
        <th>Referred User</th>
        <th>Tier</th>
          <th>Paid</th>
        <th>Amount</th>
        <th>Date</th>
    </tr>";
  foreach ($commissions as $c):
   echo " <tr>
        <td>".htmlspecialchars($c['member_id'])."</td>
       <td>".$c['tier']."</td>
          <td>".$c['paid']."</td>
        <td>".number_format($c['amount'], 2)."</td>
        <td>".$c['created_at']."</td>
    </tr>";
   endforeach;
echo "</table>";
?>
<canvas id="tierChart" width="400" height="200"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('tierChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [<?php foreach ($totalsByTier as $t) echo "'Tier {$t['tier']}',"; ?>],
            datasets: [{
                label: 'Total Earnings by Tier',
                data: [<?php foreach ($totalsByTier as $t) echo $t['total'] . ','; ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.7)'
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
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
