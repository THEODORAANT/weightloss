<?php  # include the API
          include('../../../../../core/inc/api.php');
           if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if($_POST['action']=="export"){

              PerchUtil::redirect(PERCH_LOGINPATH.'/addons/apps/perch_members/affiliates/payouts/generate_invoice?payout_id='.$_POST['payout_id']);

      }else{
         $Affiliate->markPayoutStatus($_POST['payout_id'], $_POST['action']);
                PerchUtil::redirect(PERCH_LOGINPATH.'/addons/apps/perch_members/affiliates/payouts/');
      }

      }


    $API  = new PerchAPI(1.0, 'perch_members');
    $HTML   = $API->get('HTML');
    $Lang   = $API->get('Lang');
    $Paging = $API->get('Paging');

    # Set the page title
    $Perch->page_title = $Lang->get('Affiliate');

    # Do anything you want to do before output is started
    include('../../modes/_subnav.php');
        $message = false;

        $Affiliate = new PerchMembers_Affiliate($API);
        $Members = new PerchMembers_Members($API);


        $HTML = $API->get('HTML');

            $heading1 = 'Affiliate Payouts';


         /*   echo $HTML->title_panel([
                'heading' => $Lang->get($heading1),
            ], $CurrentUser);*/

              include(PERCH_CORE . '/inc/top.php');

$payouts = $Affiliate->listPendingPayouts();
//print_r($payouts);
    echo $HTML->title_panel([
        'heading' => $Lang->get('Pending Payouts'),
        'button'  => [
            'text' => $Lang->get('History'),
            'link' => $API->app_nav().'/affiliates/payouts/history/',
            'icon' => 'core/plus',
            ],
    ], $CurrentUser);
foreach ($payouts as $payout) {
    $affdetails=$Affiliate->getAffiliateDetails($payout['affiliate_id']);

    echo "<hr><div>";
        echo "<b>Payout ID:</b> {$payout['id']}<br>";

    echo "<b>Affiliate :</b> {$affdetails['affid']} | Amount: £{$payout['amount']} | Requested: {$payout['requested_at']}<br>";
      echo "<b>Program Type :</b> {$affdetails['program_type']}<br>";
      echo "<b>Credit  :</b> {$affdetails['credit']}<br>";
    echo "<b>Status:</b> {$payout['status']}<br>";

    echo "<b>Payout Method: {$payout['payout_method']}<br>";
    echo "<form method='post'>
    <button class='button button button-simple' name='action' value='export'>Export Invoice</button>
        <input type='hidden' name='payout_id' value='{$payout['id']}'>
        <button class='button button button-simple' name='action' value='approve'>Approve</button>
        <button class='button button button-simple' name='action' value='reject'>Reject</button>
        <button class='button button button-simple' name='action' value='paid'>Mark as Paid</button>
    </form>";
    echo "</div>";
}

// Handling form submission



?>

    <?php
 /*  include('../modes/affiliates.reporting.pre.php');
    # Top layout
    include(PERCH_CORE . '/inc/top.php');


    # Display your page
    include('../modes/affiliates.reporting.post.php');*/

    # Bottom layout
    include(PERCH_CORE . '/inc/btm.php');
?>
