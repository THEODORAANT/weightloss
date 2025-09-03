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

        $Affiliates = new PerchMembers_Affiliates($API);

        $HTML = $API->get('HTML');

        if (isset($_GET['id']) && $_GET['id']!='') {
            $affiliateID = (int) $_GET['id'];
            $Affiliate = $Affiliates->find($affiliateID);

            $details = $Affiliate->to_array();

            $heading1 = 'Reporting Affiliate';

        }
          /*  echo $HTML->title_panel([
                'heading' => $Lang->get($heading1),
            ], $CurrentUser);*/

              include(PERCH_CORE . '/inc/top.php');
    $referrals=$Affiliate->getAffReferrals($details["affid"]);
     $payouts = $Affiliate->getMemberPayouts($details["affid"]);
 $affdetails=$Affiliate->getAffiliateDetails($affiliateID,$details["affid"]);
 if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $Affiliate->assignProgramType($_POST['affiliateID'],$_POST['program_type']);
          	$Alert->set('success', PerchLang::get('Successfully updated'));
          	             PerchUtil::redirect(PERCH_LOGINPATH.'/addons/apps/perch_members/affiliates/affiliate/?id='.$_POST['affiliateID']);


 }
echo "<h2> Referrals for Affiliate:".$details["affid"]."</h2>
<table border='1'>";?>
       <tr><th >  <strong>Available Credit:</strong></th><th> £<?= number_format($affdetails['credit'], 2) ?></th></tr>
<?php echo " <tr><th >Program Type</th>
        <th >
<form method='POST'>
  <input type='hidden' name='affiliateID' value='".$affiliateID."' required>
  <input type='text' name='program_type' value='".$affdetails["program_type"]."' required></td>
<br/><span><b>Program Type 1: 10 credit</b></span><br/>
<span><b>Program Type 2: 30 credit</b></span>
  </th> </tr>
   <tr><th colspan='2'> <button class='button button button-simple' id='btnSubmit' type='submit'>Save</button>
    </th> </tr>
    </form>
  </table>
 <h2> Payouts</h2>

  <table border='1'>
   <tr><th>Date</th><th>Amount</th><th>Status</th></tr>";
    foreach ($payouts as $p):?>
   <tr>
                                 <td><?= date("Y-m-d", strtotime($p['requested_at'])) ?></td>
                                 <td>£<?= number_format($p['amount'], 2) ?></td>
                                 <td><?= ucfirst($p['status']) ?></td>
                             </tr>
<?php     endforeach;
  echo "</table> <h2> Referrals</h2>
<table border='1'>
    <tr>
        <th>Referred User</th>
    <th>Orders </th>
    </tr>";
  foreach ($referrals as $r):
   $Member = $Members->find($r['referred_member_id']);

  if($Member){
    $member_details = $Member->to_array();
    $name= $member_details["first_name"]." ".$member_details["last_name"];

  }
   echo " <tr> <td>".$r['referred_member_id']."-" .$name."</td><td>".$r['purchase_count']."</td> </tr>";
   endforeach;
echo "</table>";
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
