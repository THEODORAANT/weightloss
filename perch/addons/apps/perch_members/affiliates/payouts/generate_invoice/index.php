<?php      include('../../../../../../core/inc/api.php');
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

function generateInvoiceHTML($data) {
    ob_start();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; font-size: 14px; }
            .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; }
            h2 { text-align: center; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; }
            .section-title { font-weight: bold; margin-top: 20px; }
        </style>
    </head>
    <body>
    <div class="invoice-box">
        <h2>Affiliate Commission Invoice</h2>
        <p><strong>Invoice date:</strong> <?= $data['invoice_date'] ?></p>

        <p class="section-title">Affiliate Details</p>
        <p><strong>ID:</strong> <?= $data['affiliate_id'] ?><br>
           <strong>Name:</strong> <?= $data['name'] ?><br>
           <strong>Address:</strong> <?= nl2br($data['address']) ?><br>
           <strong>Email:</strong> <?= $data['email'] ?></p>

        <p><strong>Invoice period:</strong> <?= $data['period_start'] ?> to <?= $data['period_end'] ?></p>

        <p class="section-title">Bank Details</p>
        <p><strong>Bank:</strong> <?= $data['bank_name'] ?><br>
           <strong>Sort Code:</strong> <?= $data['sort_code'] ?><br>
           <strong>Account No:</strong> <?= $data['account_number'] ?></p>

        <p class="section-title">Invoice To</p>
        <p><strong>MI Health Ltd t/a Get Weight Loss</strong><br>
           Longcroft House, 2-8 Victoria Ave,<br>
           London EC2M 4NS<br>
           Email: support@getweightloss.co.uk</p>

        <p class="section-title">Summary of Activity</p>
        <table>
            <thead>
                <tr><th>Referred User</th><th>Orders</th></tr>
            </thead>
            <tbody>
                <?php foreach ($data['activity'] as $entry): ?>
                <tr>
                    <td><?= htmlspecialchars($entry['user']) ?></td>
                    <td><?= $entry['orders'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    </body>
    </html>
    <?php
    return ob_get_clean();
}
    $API  = new PerchAPI(1.0, 'perch_members');

        $Affiliate = new PerchMembers_Affiliate($API);
        $Members = new PerchMembers_Members($API);
  $payout_details=$Affiliate->getAffiliatePayoutDetails($_GET['payout_id']);
  $affdetails=$Affiliate->getAffiliateDetails($payout_details['affiliate_id']);
  $Member = $Members->find($affdetails["member_id"]);
     $details = $Member->to_array();

 $activity= json_decode($payout_details["referral_snapshot"]);
// Sample data
$data = [
    'invoice_date' => date("d/M/Y"),
    'affiliate_id' => $affdetails['affid'],
    'name' => $details["first_name"]." ".$details["last_name"],
    'address' => "",
    'email' => $details["email"],
    'period_start' => '12/06/2025',
    'period_end' => '31/07/2025',
    'bank_name' => 'xxxx',
    'sort_code' => '12-34-56',
    'account_number' => '12345678',
    'activity' => $activity
];

// Generate PDF


$html = generateInvoiceHTML($data);
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("affiliate_invoice.pdf", ["Attachment" => false]); // false = open in browser
