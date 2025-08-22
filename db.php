<?php

$host = 'nlclinic.mysql.database.azure.com';

$user = 'nlclinic';
$pass = 'EfJS1HHkCNOlyOeT';
 if($_GET["db"]=="main"){
$db = 'getweightlossmain';
	}else{
	$db = 'getweightloss';
	}
/*$user = 'root';
$pass = '';
$host = "localhost";
	$db =  "weightloss";
*/
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 try{
//$pdo->exec("ALTER TABLE `p4_purchases` ADD `orderID` INT NOT NULL AFTER `id`");
//$pdo->exec("ALTER TABLE `p4_purchases` ADD `orderID` INT NOT NULL AFTER `id`");

 function recordPurchase( $pdo,$orderid,$member_id) {
     // Record purchase
$stmtcount = $pdo->prepare("SELECT * FROM p4_purchases WHERE referred_member_id = :member_id and orderID=:orderID");
$stmtcount->execute([
                ':member_id' => $member_id,
                ':orderID' => $orderid
            ]);
$existpurchase = $stmtcount->fetch();
  if (!count($existpurchase)) {
$stmt = $pdo->prepare("INSERT INTO p4_purchases (member_id, orderID) VALUES (:member_id, :orderID)");
$stmt->execute([
    ':member_id' => $member_id,
    ':orderID' => $orderid
]);
}

     // Check if user was referred

 $stmt = $pdo->query("SELECT * FROM p4_referrals WHERE referred_member_id=".$member_id);
 echo "SELECT * FROM p4_referrals WHERE referred_member_id=".$member_id;
 $referralrow = $stmt->fetchAll();
 print_r( $referralrow);
     if (count($referralrow)) {
         // Increment their purchase count

  $pdo->exec("UPDATE p4_referrals SET purchase_count =purchase_count + 1 WHERE referred_member_id=".$member_id);

echo "UPDATE p4_referrals ";
         // Get the new count
/*
$stmtcount = $pdo->prepare("SELECT purchase_count FROM p4_referrals WHERE referred_member_id = :member_id");
$stmtcount->execute([':member_id' => $member_id]);
$counts = $stmtcount->fetch();

      echo "counts p4_referrals ";
print_r( $counts);
         if (count($counts)== 1) {

       $stmtcount = $pdo->query("SELECT program_type FROM p4_affiliates WHERE referrer_affiliate_id=".$referralrow["referrer_affiliate_id"]);
         $rowtype = $stmtcount->fetch();
               echo " program_type ";
         print_r( $rowtype);
             if ($rowtype["program_type"] == 1) {
                 // Add £10 credit immediately

  $pdo->exec("UPDATE p4_affiliates SET purchase_count =credit = credit + 10  WHERE referrer_affiliate_id=".$referralrow["referrer_affiliate_id"]);


             }else if ($rowtype["program_type"] == 2) {
                             // Add £30 credit
                          $pdo->exec("UPDATE p4_affiliates SET purchase_count =credit = credit + 30  WHERE referrer_affiliate_id=".$referralrow["referrer_affiliate_id"]);


                         }

         }*/

     }
 }
 if($_GET["sync"]){


$stmt = $pdo->query("SELECT memberID, memberEmail, memberProperties FROM p4_members");
$members = $stmt->fetchAll();

foreach ($members as $member) {
    $props = json_decode($member['memberProperties'], true); // Convert JSON to associative array
$member_id=$member['memberID'];
$affID="";
$referrer="";
    echo "<h3>Member: {$member['memberEmail']}</h3>";
    echo "<ul>";
    foreach ($props as $key => $value) {
        echo "<li><strong>$key:</strong> $value</li>";
        if (!empty($props['affID'])) {
            $affID=$props['affID'];
        }
         if (!empty($props['referrer'])) {
                    $referrer=$props['referrer'];
                }


    }
      if (!empty($affID)) {
      $stmt = $pdo->prepare("INSERT INTO p4_affiliates (member_id, program_type, credit, affid)
                                       VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $member_id, 2, 0.00, $affID
                ]);
            }
                // If referrer is provided, insert into p4_referrals
                if (!empty($referrer)) {
                    $stmt = $pdo->prepare("INSERT INTO p4_referrals (referred_member_id, referrer_affiliate_id, purchase_count)
                                           VALUES (?, ?, ?)");
                    $stmt->execute([
                        $member_id,  $referrer, 0
                    ]);
                }
    echo "</ul><hr>";
}


 }

/*$pdo->exec("CREATE TABLE IF NOT EXISTS p4_affiliate_payouts (
           id INT AUTO_INCREMENT PRIMARY KEY,
              member_id INT NOT NULL,
              total_amount DECIMAL(10,2),
              payout_date DATETIME DEFAULT CURRENT_TIMESTAMP,
              method VARCHAR(50), -- e.g., 'PayPal', 'Bank Transfer'
              reference VARCHAR(255)
        ) ");

  $pdo->exec("ALTER TABLE p4_commissions ADD COLUMN paid TINYINT(1) DEFAULT 0 ");*/
//$pdo->exec("INSERT INTO p4_referrals (referred_member_id, referrer_affiliate_id) VALUES (98,'AFFGKV4X');");
    // $pdo->exec("DELETE FROM p4_referrals  ");
    $stmt = $pdo->query("SELECT * FROM p4_affiliate_payouts");
       echo '<stmt>';
             print_r($stmt);
         // Display each row
         while ($row = $stmt->fetch()) {
             echo '<pre>';
             print_r($row);
             echo '</pre>';
         }
$stmt = $pdo->query("SELECT * FROM p4_affiliates");
   echo '<stmt>';
         print_r($stmt);
     // Display each row
     while ($row = $stmt->fetch()) {
         echo '<pre>';
         print_r($row);
         echo '</pre>';
     }

$stmt = $pdo->query("SELECT * FROM p4_purchases");
   echo '<stmt>';
         print_r($stmt);
     // Display each row
     while ($row = $stmt->fetch()) {
         echo '<pre>';
         print_r($row);
         echo '</pre>';
     }
$stmt = $pdo->query("SELECT * FROM p4_referrals");
   echo '<stmt>';
         print_r($stmt);
     // Display each row
     while ($row = $stmt->fetch()) {
         echo '<pre>';
         print_r($row);
         echo '</pre>';
     }
 if($_GET["db"]=="main"){
 //$pdo->exec("UPDATE p4_products_match_pharmacy SET pharmacy_productID='67b4a129a625a662b4f66c34' WHERE productID=164");
/*$pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('161', '67b4a076a625a662b4f66c23', 'Mounjaro-2.5mg');");
$pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('162', '67b4a0baa625a662b4f66c28', 'Mounjaro-5.0mg');");
 $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('163', '67b4a0f2a625a662b4f66c2e', 'Mounjaro-7.5mg');");
 $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('164', '67b4a129a625a662b4f66c34', 'Mounjaro-10mg');");
 $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('165', '67e12e2bae0d068ea42106ac', 'Mounjaro-12.5mg');");
 $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('166', '67e12e5eae0d068ea42106b1', 'Mounjaro-15mg');");

  $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('167', '67e12a75ae0d068ea4210687', 'Wegovy-0.25');");
  $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('168', '67e12a9bae0d068ea421068c', 'Wegovy-0.5');");
  $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('169', '67e12accae0d068ea4210691', 'Wegovy-1.0');");

  $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('170', '67e12b32ae0d068ea4210696', 'Wegovy-1.7');");
  $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('171', '67e12b59ae0d068ea421069b', 'Wegovy-2.4');");*/
}
     // $pdo->exec("ALTER TABLE p4_commissions  MODIFY COLUMN `referrer_id`  VARCHAR(100) DEFAULT '' ");


  //   $pdo->exec("INSERT INTO p4_questionnaire (type, question_slug, member_id, version) VALUES ('re-order', 'dose', '52', 'v1'); INSERT INTO p4_questionnaire (type, question_slug, question_text, answer_text, member_id, version) VALUES ('re-order', 'weight', 'What is your weight?', '100', '52', 'v1'); INSERT INTO p4_questionnaire (type, question_slug, question_text, answer_text, member_id, version) VALUES ('re-order', 'weight2', 'inches', '', '52', 'v1'); INSERT INTO p4_questionnaire (type, question_slug, question_text, answer_text, member_id, version) VALUES ('re-order', 'weightradio-unit', 'weight unit', 'kg', '52', 'v1'); INSERT INTO p4_questionnaire (type, question_slug, member_id, version) VALUES ('re-order', 'nextstep', '52', 'v1'); INSERT INTO p4_questionnaire (type, question_slug, question_text, answer_text, member_id, version) VALUES ('re-order', 'more_side_effects', 'Please tell us as much as you can about your side effects', 'no', '52', 'v1'); INSERT INTO p4_questionnaire (type, question_slug, question_text, answer_text, member_id, version) VALUES ('re-order', 'additional-medication', 'Have you started taking any additional medication?', 'no', '52', 'v1'); INSERT INTO p4_questionnaire (type, question_slug, question_text, answer_text, member_id, version) VALUES ('re-order', 'rate_current_experience', 'Are you happy with your monthly weight loss?', 'no', '52', 'v1'); INSERT INTO p4_questionnaire (type, question_slug, question_text, answer_text, member_id, version) VALUES ('re-order', 'no_happy_reasons', 'Please tell us as much as you can about the reasons you are not happy with your monthly weight loss.', 'uuuu', '52', 'v1'); INSERT INTO p4_questionnaire (type, question_slug, question_text, answer_text, member_id, version) VALUES ('re-order', 'email_address', 'Please enter your  email address', '', '52', 'v1'); ");

 //$pdo->exec("INSERT INTO p4_questionnaire (type, question_slug, question_text, answer_text, uuid, member_id, version) VALUES ('first-order', 'multiple_answers', 'Have client alter answers?', 'Yes-https://getweightloss-dev-d2c5gpf7asdvh3a2.uksouth-01.azurewebsites.net/perch/addons/apps/perch_members/questionnaire_logs/?userId=9ceedf9e-412a-4f66-b3fe-b76474a86469', '9ceedf9e-412a-4f66-b3fe-b76474a86469', '51', 'v1')");
    //  $pdo->exec("ALTER TABLE p4_orders_match_pharmacy  ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP ");


  //$pdo->exec("INSERT INTO p4_shop_orders(orderStatus,orderGateway,orderTotal,currencyID,orderItemsSubtotal,orderItemsTax,orderItemsTotal,orderShippingSubtotal,orderShippingDiscounts,orderShippingTax,orderShippingTaxDiscounts,orderShippingTotal,orderDiscountsTotal,orderTaxDiscountsTotal,orderSubtotal,orderTaxTotal,orderItemsRefunded,orderTaxRefunded,orderShippingRefunded,orderTotalRefunded,orderTaxID,orderShippingWeight,orderCreated,orderPricing,orderDynamicFields,customerID,shippingID,orderShippingTaxRate,orderBillingAddress,orderShippingAddress) VALUES('created','stripe','125.00',47,'125.00','0.00',125,'0.00','0.00','0.00','0.00','0.00','0.00','0.00',125,'0.00',0,0,0,0,NULL,'0.00','2025-05-13 10:36:31','standard','[]',20,NULL,0,219,220)");


/*$stmt = $pdo->query("SELECT * FROM p4_shop_orders order by orderID desc");
   echo '<stmt>';
         print_r($stmt);
     // Display each row
     while ($row = $stmt->fetch()) {
         echo '<pre>';
         print_r($row);
         echo '</pre>';
     }
      $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('1', '67b4a0baa625a662b4f66c28', 'Mounjaro-0.5mg');");
      $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('8', '67e12a9bae0d068ea421068c', 'Wegovy-0.5');");
     // $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('9', '67b4a0baa625a662b4f66c28', 'Mounjaro-0.5mg');");
    $pdo->exec("ALTER TABLE p4_orders_match_pharmacy  MODIFY COLUMN `pharmacy_orderID`  VARCHAR(100) DEFAULT '' ");

 /*$pdo->exec("CREATE TABLE IF NOT EXISTS p4_orders_match_pharmacy (
            id INT AUTO_INCREMENT PRIMARY KEY,
             orderID INT NOT NULL,
             pharmacy_orderID VARCHAR(100) NOT NULL,
              pharmacy_message VARCHAR(100) DEFAULT ''
        ) ");*/

        // $pdo->exec("ALTER TABLE p4_products_match_pharmacy  MODIFY COLUMN `pharmacy_productID`  VARCHAR(100) DEFAULT '' ");

  /*$pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('153', '67b4a076a625a662b4f66c23', 'Mounjaro-2.5mg');");
$pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('154', '67b4a0baa625a662b4f66c28', 'Mounjaro-0.5mg');");
 $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('155', '67b4a0f2a625a662b4f66c2e', 'Mounjaro-7.5mg');");
 $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('156', '67b4a076a625a662b4f66c23', 'Mounjaro-10mg');");
 $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('157', '67e12e2bae0d068ea42106ac', 'Mounjaro-12.5mg');");
 $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('158', '67e12e5eae0d068ea42106b1', 'Mounjaro-15mg');");

  $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('147', '67e12a75ae0d068ea4210687', 'Wegovy-0.25');");
  $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('148', '67e12a9bae0d068ea421068c', 'Wegovy-0.5');");
  $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('149', '67e12accae0d068ea4210691', 'Wegovy-1.0');");

  $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('150', '67e12b32ae0d068ea4210696', 'Wegovy-1.7');");
  $pdo->exec("INSERT INTO p4_products_match_pharmacy (productID, pharmacy_productID, pharmacy_name) VALUES ('151', '67e12b59ae0d068ea421069b', 'Wegovy-2.4');");
 */

 $stmt = $pdo->query("SELECT * FROM p4_orders_match_pharmacy");
   echo '<stmt>';
         print_r($stmt);
     // Display each row
     while ($row = $stmt->fetch()) {
         echo '<pre>';
         print_r($row);
         echo '</pre>';
     }


 $stmt = $pdo->query("SELECT * FROM p4_products_match_pharmacy");
   echo '<stmt>';
         print_r($stmt);
     // Display each row
     while ($row = $stmt->fetch()) {
         echo '<pre>';
         print_r($row);
         echo '</pre>';
     }

     $stmt = $pdo->query("SELECT * FROM p4_questionnaire");
       echo '<stmt>';
             print_r($stmt);
         // Display each row
         while ($row = $stmt->fetch()) {
             echo '<pre>';
             print_r($row);
             echo '</pre>';
         }
          $stmt = $pdo->query("SELECT * FROM p4_commissions");
                echo '<stmt>';
                      print_r($stmt);
                  // Display each row
                  while ($row = $stmt->fetch()) {
                      echo '<pre>';
                      print_r($row);
                      echo '</pre>';
                  }

 //$pdo->exec("UPDATE p4_shop_countries SET  countryActive='0'   ");

      // $pdo->exec("ALTER TABLE p4_questionnaire  ADD COLUMN `type`  SET('first-order','re-order') DEFAULT 'first-order' ");
    }catch (Exception $e) {
               echo $e->getMessage();

    }

 /* $pdo->exec("
        CREATE TABLE IF NOT EXISTS p4_products_match_pharmacy (
            id INT AUTO_INCREMENT PRIMARY KEY,
             productID INT NOT NULL,
             pharmacy_productID INT NOT NULL,
             name VARCHAR(100) DEFAULT ''
        )
    ");
    $pdo->exec("ALTER TABLE p4_questionnaire  MODIFY COLUMN `version`  VARCHAR(100) DEFAULT '' ");
    $pdo->exec("ALTER TABLE p4_questionnaire  MODIFY COLUMN `question_text`  VARCHAR(100) DEFAULT '' ");

    $pdo->exec("ALTER TABLE p4_questionnaire  MODIFY COLUMN `answer`  VARCHAR(100) DEFAULT '' ");

    try{
            $pdo->exec("INSERT INTO p4_questionnaire (question_slug, answer_text, uuid, member_id) VALUES ('age', '18to74', '0b2407ab-9d85-4c27-8039-89010ffed7ca', '2');");
    }catch (Exception $e) {
               echo $e->getMessage();

    }

    $stmt = $pdo->query("SELECT 1 FROM p4_questionnaire LIMIT 1");

    if ($stmt && $stmt->fetch()) {
        echo "Rows exist in the table.";
    } else {
        echo "Table is empty.";
    }
$stmt = $pdo->query("SELECT * FROM p4_members_documents");
  echo '<stmt>';
        print_r($stmt);
    // Display each row
    while ($row = $stmt->fetch()) {
        echo '<pre>';
        print_r($row);
        echo '</pre>';
    }*/
    // Create questionnaire_versions table

    // Create questions table
 /*   $pdo->exec("
        CREATE TABLE IF NOT EXISTS p4_questions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            version_id INT NOT NULL,
            question_text TEXT NOT NULL,
            question_type VARCHAR(50) NOT NULL, -- e.g., 'text', 'multiple_choice'
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (version_id) REFERENCES questionnaire_versions(id) ON DELETE CASCADE
        )
    ");

    // Create answers table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS p4_answers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            question_id INT NOT NULL,
            answer_text TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
        )
    ");*/

    echo "Tables created successfully!";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
