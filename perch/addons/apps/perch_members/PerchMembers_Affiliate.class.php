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

$sql = "SELECT * FROM ".PERCH_DB_PREFIX."affiliate_payouts p,".PERCH_DB_PREFIX."affiliate_payout_details d WHERE p.id = d.payout_id and d.payout_id =".$this->db->pdb($payoutID)."";

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

  $purchases = [];
  $member_ids = [];
  if (PerchUtil::count($referrals)) {
      $member_ids = array_filter(array_column($referrals, 'referred_member_id'));
  }
  if (!empty($affrow['member_id'])) {
      $member_ids[] = (int) $affrow['member_id'];
  }
  $member_ids = array_unique(array_map('intval', $member_ids));
  if (!empty($member_ids)) {
      $member_ids = implode(',', $member_ids);
      $sql ="SELECT * FROM ".PERCH_DB_PREFIX."purchases   WHERE member_id IN (".$member_ids.") AND payout_id IS NULL";
      $purchases = $this->db->get_rows($sql);
  }

// Step 2: Insert payout
/*$stmt = $pdo->prepare("INSERT INTO perch_affiliate_payouts (affiliate_id, amount) VALUES (:id, :amount)");
$stmt->execute(['id' => $affiliate_id, 'amount' => $credit]);
$payout_id = $pdo->lastInsertId();*/

   //$sql="INSERT INTO ".PERCH_DB_PREFIX."affiliate_payouts (affiliate_id, total_amount, method, reference) VALUES (".$member_id.", '".$details["referrer"]."',".$tier.", ".$tierAmount.");";
  // $this->db->execute($sql);
    // Step 3: Create payout record
    $payout_id = $this->db->insert(
        PERCH_DB_PREFIX.'affiliate_payouts',
        [
            'affiliate_id'   => $affiliate_id,
            'amount'         => $credit,
            'payout_method'  => $payout_method,
            'payout_details' => $payout_details,
        ]
    );

    if (!$payout_id) {
        return [
            'status' => 'Unable to create payout request.'
        ];
    }

    // Step 4: Update the referral/purchase records to lock them to this payout
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

  $sql="UPDATE ".PERCH_DB_PREFIX."affiliates  SET credit = 0 WHERE id=".$this->db->pdb($affiliate_id);
     $this->db->execute($sql);
      return [
             'status' => "Payout request submitted successfully."

         ];
}

function createStripeCouponAndPromotionCode($couponCode, $amount, $currencyCode, $currencyDecimals)
{
    $Gateway = PerchShop_Gateways::get('stripe');
    $config = PerchShop_Config::get('gateways', 'stripe');

    if (!$Gateway || !is_array($config)) {
        return [
            'ok' => false,
            'status' => 'Stripe gateway is not configured.'
        ];
    }

    $stripeSecretKey = trim((string) $Gateway->get_api_key($config));
    if ($stripeSecretKey === '') {
        return [
            'ok' => false,
            'status' => 'Stripe secret key is missing.'
        ];
    }

    $amountOff = (int) round(((float)$amount) * pow(10, (int)$currencyDecimals));
    if ($amountOff <= 0) {
        return [
            'ok' => false,
            'status' => 'Stripe amount_off must be greater than zero.'
        ];
    }

    $couponFields = [
        'amount_off' => $amountOff,
        'currency' => strtolower((string) $currencyCode),
        'duration' => 'once',
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.stripe.com/v1/coupons',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_USERPWD => $stripeSecretKey . ':',
        CURLOPT_POSTFIELDS => http_build_query($couponFields),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded',
        ],
    ]);

    $couponResponse = curl_exec($ch);
    print_r($couponResponse);
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return [
            'ok' => false,
            'status' => 'Stripe coupon cURL error: ' . $error
        ];
    }
    curl_close($ch);

    $couponData = json_decode($couponResponse, true);
    if (!is_array($couponData) || !isset($couponData['id'])) {
        return [
            'ok' => false,
            'status' => 'Stripe coupon creation failed: ' . $couponResponse
        ];
    }

    $promoFields = [
        'promotion[type]' => 'coupon',
        'promotion[coupon]' => $couponData['id'],
        'code' => $couponCode,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.stripe.com/v1/promotion_codes',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_USERPWD => $stripeSecretKey . ':',
        CURLOPT_POSTFIELDS => http_build_query($promoFields),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded',
        ],
    ]);

    $promoResponse = curl_exec($ch);
    print_r($promoResponse);
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return [
            'ok' => false,
            'status' => 'Stripe promotion code cURL error: ' . $error
        ];
    }
    curl_close($ch);

    $promoData = json_decode($promoResponse, true);
    if (!is_array($promoData) || !isset($promoData['id'])) {
        return [
            'ok' => false,
            'status' => 'Stripe promotion code creation failed: ' . $promoResponse
        ];
    }

    return [
        'ok' => true,
        'coupon_id' => $couponData['id'],
        'promotion_code_id' => $promoData['id'],
        'promotion_code' => (string)($promoData['code'] ?? $couponCode),
    ];
}


function getUnusedCoupons($affID) {
 $affID = trim((string)$affID);
 if ($affID === '') {
     return [];
 }

 $sql = "SELECT p.promoID, p.promoTitle, p.promoFrom, p.promoTo, p.promoActive, p.promoDynamicFields, p.promoCreated, COUNT(o.orderID) AS use_count
"
      . "FROM ".PERCH_DB_PREFIX."shop_promotions p
"
      . "LEFT JOIN ".PERCH_DB_PREFIX."shop_order_promotions op ON op.promoID = p.promoID
"
      . "LEFT JOIN ".PERCH_DB_PREFIX."shop_orders o ON o.orderID = op.orderID AND o.orderStatus = ".$this->db->pdb('paid')."
"
      . "WHERE p.promoTitle LIKE ".$this->db->pdb('Affiliate Credit %('.$affID.')')."
"
      . "GROUP BY p.promoID, p.promoTitle, p.promoFrom, p.promoTo, p.promoActive, p.promoDynamicFields, p.promoCreated
"
      . "HAVING use_count = 0
"
      . "ORDER BY p.promoCreated DESC";

 $rows = $this->db->get_rows($sql);
 if (!PerchUtil::count($rows)) {
     return [];
 }

 $output = [];
 foreach ($rows as $row) {
     $dynamic = json_decode((string)($row['promoDynamicFields'] ?? ''), true);
     if (!is_array($dynamic)) {
         $dynamic = [];
     }

     $amount = null;
     if (isset($dynamic['amount']) && is_array($dynamic['amount'])) {
         $amountValues = array_values($dynamic['amount']);
         if (isset($amountValues[0])) {
             $amount = (float)$amountValues[0];
         }
     }

     $discountCode = isset($dynamic['discount_code']) ? trim((string)$dynamic['discount_code']) : '';
     $promoTo = isset($row['promoTo']) ? trim((string)$row['promoTo']) : '';
     $isExpired = false;
     if ($promoTo !== '' && strtotime($promoTo) !== false) {
         $isExpired = strtotime($promoTo) < time();
     }

     $status = 'Active';
     if (!(int)$row['promoActive']) {
         $status = 'Inactive';
     }
     if ($isExpired) {
         $status = 'Expired';
     }

     $output[] = [
         'promo_id' => (int)($row['promoID'] ?? 0),
         'title' => (string)($row['promoTitle'] ?? ''),
         'code' => $discountCode,
         'amount' => $amount,
         'valid_from' => (string)($row['promoFrom'] ?? ''),
         'valid_to' => $promoTo,
         'status' => $status,
         'uses' => 0,
     ];
 }

 return $output;
}

function convertCreditToCoupon($affID) {
 $sqlaff = "SELECT * FROM ".PERCH_DB_PREFIX."affiliates WHERE affID=".$this->db->pdb($affID);
 $affrow = $this->db->get_row($sqlaff);

 if (!PerchUtil::count($affrow)) {
     return [
         'ok' => false,
         'status' => 'Affiliate account not found.'
     ];
 }

 $affiliate_id = (int)$affrow['id'];
 $credit = isset($affrow['credit']) ? (float)$affrow['credit'] : 0;

 if ($credit <= 0) {
     return [
         'ok' => false,
         'status' => 'No available credit to convert right now.'
     ];
 }

 $shopAPI = new PerchAPI(1.0, 'perch_shop');
 $Currencies = new PerchShop_Currencies($shopAPI);
 $defaultCurrency = $Currencies->get_default();

 if (!is_object($defaultCurrency)) {
     return [
         'ok' => false,
         'status' => 'Unable to resolve the default shop currency.'
     ];
 }

 $currencyID = (int)$defaultCurrency->id();
 $currencyCode = (string)$defaultCurrency->currencyCode();
 $currencyDecimals = (int)$defaultCurrency->currencyDecimals();
 $amount = round($credit, 2);
 $couponCode = strtoupper('AFF'.preg_replace('/[^A-Za-z0-9]/', '', (string)$affID).substr(md5(uniqid((string)$affiliate_id, true)), 0, 6));
 $now = date('Y-m-d H:i:s');
 $validTo = date('Y-m-d H:i:s', strtotime('+90 days'));

 $stripeResult = $this->createStripeCouponAndPromotionCode($couponCode, $amount, $currencyCode, $currencyDecimals);
 if (empty($stripeResult['ok'])) {
     return [
         'ok' => false,
         'status' => (string)($stripeResult['status'] ?? 'Unable to create Stripe coupon.')
     ];
 }
 /*
{ "id": "ksdxkleW", "object": "coupon", "amount_off": 9000, "created": 1774972210, "currency": "gbp",
"duration": "once", "duration_in_months": null, "livemode": false, "max_redemptions": null, "metadata": {},
"name": null, "percent_off": null, "redeem_by": null, "times_redeemed": 0, "valid": true }
{ "id": "promo_1TH4YRCeux1vWiSRFCMU4bto", "object": "promotion_code", "active": true,
 "code": "AFFAFF02VKF122131", "coupon": { "id": "ksdxkleW", "object": "coupon", "amount_off": 9000,
 "created": 1774972210, "currency": "gbp", "duration": "once", "duration_in_months": null, "livemode": false,
  "max_redemptions": null, "metadata": {}, "name": null, "percent_off": null, "redeem_by": null,
  "times_redeemed": 0, "valid": true }, "created": 1774972211, "customer": null, "customer_account": null,
   "expires_at": null, "livemode": false, "max_redemptions": null, "metadata": {},
   "restrictions": { "first_time_transaction": false, "minimum_amount": null, "minimum_amount_currency": null },
   "times_redeemed": 0 }*/

 $promoOrderSql = "SELECT MAX(promoOrder) FROM ".PERCH_DB_PREFIX."shop_promotions";
 $promoOrder = (int)$this->db->get_value($promoOrderSql);
 $promoOrder = $promoOrder > 0 ? ($promoOrder + 1) : 1;

 $dynamicFields = [
     'title' => 'Affiliate Credit £'.number_format($amount, 2).' ('.$affID.')',
     'description' => 'Auto-generated from affiliate credit conversion.',
     'from' => $now,
     'to' => $validTo,
     'active' => '1',
     'status' => '1',
     'action' => 'discount_by_fixed',
     'discount_code' => $couponCode,
     'amount' => [
         (string)$currencyID => $amount
     ],
     'max_uses' => 1,
     'customer_uses' => 1,
     'terminating' => 1,
     'persistent' => 1,
     'priority' => 999,
     'apply_to_shipping' => 0,
 ];

 $promoID = $this->db->insert(PERCH_DB_PREFIX.'shop_promotions', [
     'promoTitle' => $dynamicFields['title'],
     'promoDynamicFields' => PerchUtil::json_safe_encode($dynamicFields),
     'promoFrom' => $now,
     'promoTo' => $validTo,
     'promoActive' => 1,
     'promoOrder' => $promoOrder,
     'promoCreated' => $now,
     'promoUpdated' => $now,
 ]);

 if (!$promoID) {
     return [
         'ok' => false,
         'status' => 'Could not create the coupon. Please try again.'
     ];
 }

 $sql = "UPDATE ".PERCH_DB_PREFIX."affiliates SET credit = 0 WHERE id=".$this->db->pdb($affiliate_id);
 $this->db->execute($sql);

 return [
     'ok' => true,
     'status' => 'Credit converted successfully. Use this coupon on checkout.',
     'coupon_code' => $couponCode,
     'amount' => $amount
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
                $sqlupdate = "UPDATE " . PERCH_DB_PREFIX . "affiliates  SET credit = credit + 10 WHERE affid=" . $this->db->pdb($referralrow["referrer_affiliate_id"]);
                $this->db->execute($sqlupdate);

            } else if (isset($rowtype["program_type"]) && $rowtype["program_type"] == 2) {
                if (!$isreorder) {
                    $firstOrderPayout = 5;

                    if ($referralrow['referrer_affiliate_id'] === 'AFFEX3Y4') {
                        $firstOrderPayout = 7.50;
                    } else if ($referralrow['referrer_affiliate_id'] === 'AFFKKPJK') {
                        $firstOrderPayout = 5;
                    } else if ($referralrow['referrer_affiliate_id'] === 'AFFZQOVY') {
                        $firstOrderPayout = $this->order_has_prepaid_or_preorder($orderID) ? 10 : 5;
                    }

                    $sql = "UPDATE " . PERCH_DB_PREFIX . "affiliates  SET credit = credit + " . $this->db->pdb($firstOrderPayout) . " WHERE affid=" . $this->db->pdb($referralrow["referrer_affiliate_id"]);
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

        private function order_has_prepaid_or_preorder($orderID)
        {
            $OrderItems = new PerchShop_OrderItems($this->api);
            $items = $OrderItems->get_for_admin($orderID);

            if (!PerchUtil::count($items)) {
                return false;
            }

            $keywords = [
         'Mounjaro Prepaid',
                'Mounjaro monthly',
            ];

            foreach ($items as $Item) {
                $title = strtolower((string) $Item->title());

                foreach ($keywords as $keyword) {
                    if (strpos($title, $keyword) !== false) {
                        return true;
                    }
                }
            }

            return false;
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
