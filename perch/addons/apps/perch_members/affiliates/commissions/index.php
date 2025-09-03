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
$unpaid = $Affiliate->getUnpaidCommissions();
//print_r($unpaid);
$payoutHistory = $Affiliate->getPayoutHistory();
    $Members = new PerchMembers_Members($API);

            $heading1 = 'Affiliate Payouts';


            echo $HTML->title_panel([
                'heading' => $Lang->get($heading1),
            ], $CurrentUser);

              include(PERCH_CORE . '/inc/top.php');
              // Handle payout submission
// Handle payout submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payouts'])) {
    foreach ($_POST['payouts'] as $memberId => $data) {
        $amount = floatval($data['amount']);
        $method = htmlspecialchars($data['method']);
        $reference = htmlspecialchars($data['reference']);

        $success = true;

        if ($method === 'paypal') {
            $result = sendPayPalPayout($reference, $amount);
            if (strpos($result, 'batch_header') === false) {
                echo "<p>PayPal payout failed for Member $memberId: $result</p>";
                $success = false;
            }
        } elseif ($method === 'stripe') {
            $result = sendStripePayout($reference, $amount);
            if (strpos($result, 'id') === false) {
                echo "<p>Stripe payout failed for Member $memberId: $result</p>";
                $success = false;
            }
        }

        if ($success) {
            $Affiliate->logPayout($memberId, $amount, $method, $reference);
            $Affiliate->markCommissionsPaid($memberId);
        }
    }
    echo "<p>Payouts processed.</p>";
}

              // Simulated Stripe Payout without SDK (raw HTTP)
 function sendStripePayout($accountId, $amount) {
                 	$Gateway = PerchShop_Gateways::get("stripe");
                 		$result  = $Gateway->sendStripePayout($accountId, $amount);
                 		return $result ;
              }
function sendPayPalPayout($email, $amount) {
    $accessToken = 'YOUR_PAYPAL_ACCESS_TOKEN';
    $headers = [
        "Content-Type: application/json",
        "Authorization: Bearer $accessToken"
    ];
    $data = [
        "sender_batch_header" => [
            "sender_batch_id" => uniqid(),
            "email_subject" => "Affiliate Payout"
        ],
        "items" => [[
            "recipient_type" => "EMAIL",
            "amount" => ["value" => number_format($amount, 2, '.', ''), "currency" => "GBP"],
            "receiver" => $email,
            "note" => "Thank you for your contribution!",
            "sender_item_id" => uniqid()
        ]]
    ];

    $ch = curl_init('https://api-m.sandbox.paypal.com/v1/payments/payouts');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return "Error: " . curl_error($ch);
    }
    curl_close($ch);
    return $response;
}
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


?>
<form method="POST">
        <table>
            <tr>
                <th>Member ID</th>
                 <th>Affiliate ID</th>
                <th>Total Unpaid</th>
                <th>Method</th>
                <th>Reference</th>
            </tr>
            <?php foreach ($unpaid as $row):
            $affid="";
            if(isset($row['member_id'])){
               $Member = $Members->find($row['member_id']);

if($Member){
  $details = $Member->to_array();
                       $affid=$details['affID'];
}

                     }
             ?>
                <tr>
                    <td><?= $row['member_id'] ?></td>
                       <td><?=$affid?></td>
                    <td>£<?= number_format($row['total'], 2) ?></td>
                    <td><input type="text" name="payouts[<?= $row['member_id'] ?>][method]" required></td>
                    <td><input type="text" name="payouts[<?= $row['member_id'] ?>][reference]" required></td>
                    <input type="hidden" name="payouts[<?= $row['member_id'] ?>][amount]" value="<?= $row['total'] ?>">
                </tr>

            <?php endforeach;
                 if(!count($unpaid)){
                             echo "<tr> <th  colspan='5'><p>No Pending Payouts !</p></th></tr>";
                          } ?>


        </table>
        <br>
        <button class="button button button-simple" id="btnSubmit" type="submit">Process Payouts</button>
    </form>
 <h2>Payout History</h2>

    <a class="button button-icon icon-left" href="?export=csv"><div><svg role="img" width="10" height="10" class="icon icon-o-cloud-download"> <use xlink:href="/perch/core/assets/svg/ext.svg#o-cloud-download"></use> </svg><span>Export CSV</span></div></a>
    <table>
        <tr>
            <th>Member ID</th>
            <th>Total Amount</th>
            <th>Payout Date</th>
            <th>Method</th>
            <th>Reference</th>
        </tr>
        <?php foreach ($payoutHistory as $p): ?>
            <tr>
                <td><?= $p['member_id'] ?></td>
                <td>£<?= number_format($p['total_amount'], 2) ?></td>
                <td><?= $p['payout_date'] ?></td>
                <td><?= htmlspecialchars($p['method']) ?></td>
                <td><?= htmlspecialchars($p['reference']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
              <?php

               /*  include('../modes/affiliates.reporting.pre.php');
                  # Top layout
                  include(PERCH_CORE . '/inc/top.php');


                  # Display your page
                  include('../modes/affiliates.reporting.post.php');*/

                  # Bottom layout
                  include(PERCH_CORE . '/inc/btm.php');
              ?>
