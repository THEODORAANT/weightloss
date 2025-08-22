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

$stmt = $pdo->query("SELECT m.memberID FROM p4_shop_orders o inner join p4_shop_customers m on o.customerID=m.customerID where o.status=order by o.orderID desc");
   echo '<stmt>';
         print_r($stmt);
     // Display each row
     while ($row = $stmt->fetch()) {
         echo '<pre>';
         print_r($row);
         $stmt = $pdo->prepare("INSERT INTO p4_questionnaire_member_status (memberID,questionnaire_id, `status`) VALUES (:member_id, :questionnaire_id,:status)");
       $stmt->execute([
             ':member_id' => $row['memberID'],
             ':questionnaire_id'=>"v1",
             ':status' => 'completed'
         ]);
         echo '</pre>';
     }

     } catch (PDOException $e) {
         echo "Database error: " . $e->getMessage();
     }
