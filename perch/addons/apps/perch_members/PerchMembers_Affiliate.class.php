<?php

class PerchMembers_Affiliate extends PerchAPI_Base
{
    protected $table  = 'affiliates';
    protected $pk     = 'id';

// Fetch unpaid commissions
function getUnpaidCommissions() {
 $sql = 'SELECT member_id, SUM(amount) as total FROM '.PERCH_DB_PREFIX.'commissions WHERE paid = 0 GROUP BY member_id';

		return $this->db->get_rows($sql);
}
function registerAffiliate($member_id,$affID) {
    // Auto assign to Type 2
  $sqlraff ="SELECT * FROM ".PERCH_DB_PREFIX."affiliates WHERE affID=".$this->db->pdb($affID);
    $affiliate =$this->db->get_row($sqlraff);



     if (!PerchUtil::count($affiliate)) {
     $sql1="INSERT INTO ".PERCH_DB_PREFIX."affiliates (member_id,affID) VALUES (".$member_id.",'".$affID."');";

    $this->db->execute($sql1);
    }
}
function getAffiliates() {
     $sql ="SELECT * FROM ".PERCH_DB_PREFIX."affiliates ORDER BY id DESC";
  	return $this->db->get_rows($sql);
}
function getAffiliateDetails($affiliate_id,$slug="") {
if($slug){
  $sql ="SELECT * FROM ".PERCH_DB_PREFIX."affiliates where  affID=".$this->db->pdb($slug);
  	return $this->db->get_row($sql);
}else{
    $sql ="SELECT * FROM ".PERCH_DB_PREFIX."affiliates where  id=".$this->db->pdb($affiliate_id);
  	return $this->db->get_row($sql);
}

}
function assignProgramType($affiliate_id,$program_type) {
 $sql="UPDATE ".PERCH_DB_PREFIX."affiliates  SET program_type =".$this->db->pdb($program_type)." WHERE id=".$this->db->pdb($affiliate_id);
     $this->db->execute($sql);

}
function registerReferral($referred_member_id, $referrer_affiliate_id) {

    $sql="INSERT INTO ".PERCH_DB_PREFIX."referrals (referred_member_id, referrer_affiliate_id) VALUES (".$referred_member_id.",'".$referrer_affiliate_id."');";

       $this->db->execute($sql);
}
function getAffReferrals($affiliate_id) {
     $sql ="SELECT * FROM ".PERCH_DB_PREFIX."referrals where referrer_affiliate_id=".$this->db->pdb($affiliate_id);

  	return $this->db->get_rows($sql);
}
function getMemberPayouts($affID){
 $sqlaff ="SELECT * FROM ".PERCH_DB_PREFIX."affiliates WHERE affID=".$this->db->pdb($affID);

    $affrow =$this->db->get_row($sqlaff);
$sql = "SELECT * FROM ".PERCH_DB_PREFIX."affiliate_payouts WHERE affiliate_id =".$this->db->pdb($affrow['id'])." ORDER BY requested_at DESC";

  	return $this->db->get_rows($sql);

}

function getAffiliatePayoutDetails($payoutID){

$sql = "SELECT * FROM ".PERCH_DB_PREFIX."affiliate_payouts p,".PERCH_DB_PREFIX."affiliate_payout_details d WHERE p.id = d.payout_id and payout_id =".$this->db->pdb($payoutID)."";

  	return $this->db->get_rows($sql);

}

function requestPayout($affID, $payout_method = 'manual', $payout_details = '') {


    // Get available credit
 $sqlaff ="SELECT * FROM ".PERCH_DB_PREFIX."affiliates WHERE affID=".$this->db->pdb($affID);
   // echo "sqlref"; echo $sqlaff ;
    $affrow =$this->db->get_row($sqlaff);
    $affiliate_id=$affrow["id"];
    $credit=$affrow["credit"];
/* $sqlaff ="SELECT credit FROM ".PERCH_DB_PREFIX."affiliates WHERE id=".$this->db->pdb($affiliate_id);
    echo "sqlref"; echo $sqlaff ;
    $affrow =$this->db->get_row($sqlaff);*/
  $sql ="SELECT * FROM ".PERCH_DB_PREFIX."referrals   WHERE referrer_affiliate_id =".$affiliate_id." AND payout_id IS NULL";
$referrals = $this->db->get_rows($sql);

  $sql ="SELECT * FROM ".PERCH_DB_PREFIX."purchases   WHERE member_id =".$memberId." AND payout_id IS NULL";
$purchases = $this->db->get_rows($sql);

// Step 2: Insert payout
/*$stmt = $pdo->prepare("INSERT INTO perch_affiliate_payouts (affiliate_id, amount) VALUES (:id, :amount)");
$stmt->execute(['id' => $affiliate_id, 'amount' => $credit]);
$payout_id = $pdo->lastInsertId();*/

   //$sql="INSERT INTO ".PERCH_DB_PREFIX."affiliate_payouts (affiliate_id, total_amount, method, reference) VALUES (".$member_id.", '".$details["referrer"]."',".$tier.", ".$tierAmount.");";
  // $this->db->execute($sql);
// Step 3: Update the referral/purchase records to lock them to this payout
if ($referrals) {
    $ids = implode(",", array_column($referrals, 'id'));
     $sql="UPDATE ".PERCH_DB_PREFIX."referrals SET payout_id = $payout_id WHERE id IN ($ids)";
     $this->db->execute($sql);
     $sql = "INSERT INTO ".PERCH_DB_PREFIX."affiliate_payout_details
             (payout_id, referral_snapshot, purchase_snapshot)
             VALUES (?, ?, ?)";

     $this->db->execute($sql, [
         $payout_id,
         json_encode($referrals),
         json_encode($purchases)
     ]);

}

if ($purchases) {
    $ids = implode(",", array_column($purchases, 'id'));
    $sql="UPDATE ".PERCH_DB_PREFIX."purchases SET payout_id = $payout_id WHERE id IN ($ids)";
      $this->db->execute($sql);
}

// Step 4: Save snapshot as JSON

$sql="INSERT INTO ".PERCH_DB_PREFIX."affiliate_payouts (affiliate_id, amount, payout_method, payout_details) VALUES (".$affiliate_id.",'".$credit."','".$payout_method."','".$payout_details."');";

       $this->db->execute($sql);
    // Insert payout request



  $sql="UPDATE ".PERCH_DB_PREFIX."affiliates  SET credit = 0 WHERE id=".$this->db->pdb($affiliate_id);
     $this->db->execute($sql);
      return [
             'status' => "Payout request submitted successfully."

         ];
}

// Mark commissions as paid
function markCommissionsPaid($memberId) {

    $sql="UPDATE ".PERCH_DB_PREFIX."commissions  SET paid = 1 WHERE member_id=".$this->db->pdb($memberId);
     $this->db->execute($sql);
}
// Fetch payout history
function getPayoutHistory() {
     $sql ="SELECT * FROM ".PERCH_DB_PREFIX."affiliate_payouts ORDER BY requested_at DESC";
  	return $this->db->get_rows($sql);
}


// Log payout
function logPayout($memberId, $amount, $method, $reference) {
// Step 1: Get only NEW referrals/purchases

  $sql ="SELECT * FROM ".PERCH_DB_PREFIX."referrals   WHERE referrer_affiliate_id =".$affiliate_id." AND payout_id IS NULL";
$referrals = $this->db->get_rows($sql);

  $sql ="SELECT * FROM ".PERCH_DB_PREFIX."purchases   WHERE member_id =".$memberId." AND payout_id IS NULL";
$purchases = $this->db->get_rows($sql);

// Step 2: Insert payout
/*$stmt = $pdo->prepare("INSERT INTO perch_affiliate_payouts (affiliate_id, amount) VALUES (:id, :amount)");
$stmt->execute(['id' => $affiliate_id, 'amount' => $credit]);
$payout_id = $pdo->lastInsertId();*/

   $sql="INSERT INTO ".PERCH_DB_PREFIX."affiliate_payouts (affiliate_id, total_amount, method, reference) VALUES (".$member_id.", '".$details["referrer"]."',".$tier.", ".$tierAmount.");";
   $this->db->execute($sql);
// Step 3: Update the referral/purchase records to lock them to this payout
if ($referrals) {
    $ids = implode(",", array_column($referrals, 'id'));
     $sql="UPDATE ".PERCH_DB_PREFIX."referrals SET payout_id = $payout_id WHERE id IN ($ids)";
     $this->db->execute($sql);
     $sql = "INSERT INTO ".PERCH_DB_PREFIX."affiliate_payout_details
                  (payout_id, referral_snapshot, purchase_snapshot)
                  VALUES (?, ?, ?)";

          $this->db->execute($sql, [
              $payout_id,
              json_encode($referrals),
              json_encode($purchases)
          ]);
}

if ($purchases) {
    $ids = implode(",", array_column($purchases, 'id'));
    $sql="UPDATE ".PERCH_DB_PREFIX."purchases SET payout_id = $payout_id WHERE id IN ($ids)";
      $this->db->execute($sql);
}

// Step 4: Save snapshot as JSON

// Step 5: Reset credit
$sql="UPDATE ".PERCH_DB_PREFIX."affiliates SET credit = 0 WHERE id = ".$affiliate_id;
 $this->db->execute($sql);
}
  public function to_array()
    {
        $details = $this->details;



        return $details;
    }


function recordPurchase($member_id,$orderID,$isreorder) {
try{

    $sql ="SELECT * FROM ".PERCH_DB_PREFIX."purchases WHERE orderID=".$this->db->pdb($orderID);

    $purchaserow =$this->db->get_row($sql);

      if (!PerchUtil::count($purchaserow)) {
    // Record purchase
   $sql="INSERT INTO ".PERCH_DB_PREFIX."purchases (member_id,orderID) VALUES (".$member_id.",".$orderID.");";

   $this->db->execute($sql);
}

    // Check if user was referred
    $sqlref ="SELECT * FROM ".PERCH_DB_PREFIX."referrals WHERE referred_member_id=".$this->db->pdb($member_id);
    $referralrow =$this->db->get_row($sqlref);


    if (PerchUtil::count($referralrow)) {
        // Increment their purchase count
           $sqlup="UPDATE ".PERCH_DB_PREFIX."referrals  SET purchase_count =purchase_count + 1 WHERE referred_member_id=".$this->db->pdb($member_id);
             $this->db->execute($sqlup);


        // Get the new count

 $sqlcount ="SELECT purchase_count FROM ".PERCH_DB_PREFIX."referrals WHERE referred_member_id=".$this->db->pdb($member_id);
    $counts =$this->db->get_rows($sqlcount);
       // if (PerchUtil::count($counts)== 1) {


    $sql ="SELECT program_type FROM ".PERCH_DB_PREFIX."affiliates WHERE affid=".$this->db->pdb($referralrow["referrer_affiliate_id"]);


    $rowtype =$this->db->get_row($sql);
    //print_r($rowtype);
            if (isset($rowtype["program_type"]) && $rowtype["program_type"] == 1) {
                // Add £10 credit immediately
                  $sqlupdate="UPDATE ".PERCH_DB_PREFIX."affiliates  SET credit = credit + 10 WHERE affid=".$this->db->pdb($referralrow["referrer_affiliate_id"]);
                   $this->db->execute($sqlupdate);


            }else if (isset($rowtype["program_type"]) && $rowtype["program_type"] == 2) {
            if(!$isreorder){


                            // Add £30 credit
                                $sql="UPDATE ".PERCH_DB_PREFIX."affiliates  SET credit = credit + 30 WHERE affid=".$this->db->pdb($referralrow["referrer_affiliate_id"]);
                                               $this->db->execute($sql);
                                               }

                        }

     //   }
        /*elseif (PerchUtil::count($counts) == 2) {


    $sqltype ="SELECT program_type FROM ".PERCH_DB_PREFIX."affiliates WHERE referrer_affiliate_id=".$this->db->pdb($referralrow["referrer_affiliate_id"]);
    $rowtype =$this->db->get_row($sqltype);
            if ($rowtype["program_type"] == 2) {
                // Add £30 credit
                    $sql="UPDATE ".PERCH_DB_PREFIX."affiliates  SET credit = credit + 30 WHERE referrer_affiliate_id=".$this->db->pdb($referralrow["referrer_affiliate_id"]);
                                   $this->db->execute($sql);

            }
        }*/
    }
    }catch (Exception $e) {
                    echo $e->getMessage();

         }
}

public function addCommission($member_id, $amount) {

        $tier = 1;

        $Members = new PerchMembers_Members();
        if (is_object($Members)) $Member = $Members->find($member_id);
        $details = $Member->to_array();
      //  print_r($details);
        while ($tier <= 1 && $details["referrer"]) {

            $commissionAmount = $amount * (0.10 / $tier); // Example: tier 1 = 10%, tier 2 = 5%, tier 3 = 3.33%
            if($tier==1){
              $tierAmount =10;
            }
            if($tier==2){
              $tierAmount =3;
            }
              if($tier==3){
                $tierAmount =1;

               }
                     $sql1="INSERT INTO ".PERCH_DB_PREFIX."commissions (member_id, referrer_id, tier, amount) VALUES (".$member_id.", '".$details["referrer"]."',".$tier.", ".$tierAmount.");";

                           $this->db->execute($sql1);

            $tier++;
           }
        }

          public function getMemberSumCommissions($affID) {
        $sql = 'SELECT tier, SUM(amount) as total FROM  '.PERCH_DB_PREFIX.'commissions  WHERE referrer_id ='.$this->db->pdb($affID) .'GROUP BY tier';

		return $this->db->get_rows($sql);

        }

        public function getMemberCommissions($affID) {

            $sql = 'SELECT * FROM '.PERCH_DB_PREFIX.'commissions WHERE referrer_id ='.$this->db->pdb($affID);

		return $this->db->get_rows($sql);

        }
function listPendingPayouts() {

      $sql = "SELECT ap.*, a.member_id FROM  ".PERCH_DB_PREFIX."affiliate_payouts ap JOIN  ".PERCH_DB_PREFIX."affiliates a ON a.id = ap.affiliate_id WHERE status = 'pending'";
    	return $this->db->get_rows($sql);
}

function markPayoutStatus($payout_id, $status) {

     $sql = "UPDATE  ".PERCH_DB_PREFIX."affiliate_payouts SET status = ".$this->db->pdb($status).", processed_at = NOW() WHERE id =".$this->db->pdb($payout_id);

     $this->db->execute($sql);
    return [
        'status' => $status,
        'id' => $payout_id
    ];
}

    }


    ?>
