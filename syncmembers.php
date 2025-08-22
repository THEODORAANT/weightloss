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



    // Step 1: Get height answers from questionnaire
    $stmt = $pdo->prepare("
     SELECT q.*,m.memberProperties
             FROM p4_questionnaire q,p4_members m
             WHERE q.member_id=m.memberID and q.question_slug LIKE '%height%'
              order by m.memberID

    ");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $selectStmt = $pdo->prepare("SELECT memberProperties FROM p4_members WHERE memberID = :memberID");

    $heightByMember = [];
    $updateStr="";
    $count=0;
    foreach ($rows as $row) {
        $memberId = (int)$row['member_id'];
        $height = $row['answer'] ?: $row['answer_text'];
        $height = trim($height);
        if(array_key_exists($memberId, $heightByMember)){
        $count++;
        }else{
         $count=0;
        }

        // Basic numeric validation
        if (isset($height)) {


           //  $selectStmt->execute(['memberID' => $memberId]);
                 //   $memberrow = $selectStmt->fetch(PDO::FETCH_ASSOC);
                             if( $count==0){
                                                          //if (!is_array($props)) $props = [];

                                 $props = json_decode($row['memberProperties'], true);
                                 $newprops =$props;
                             }


            $newprops[ $row["question_slug"]] = $height;
            echo "count:"; echo $count;
           $heightByMember[$memberId] = $newprops;
             // $updateStr .= "UPDATE p4_members SET memberProperties =".json_encode($props)."    WHERE memberID = ". $memberId." ;";

        }
    }
     $updateStmt = $pdo->prepare("
            UPDATE p4_members
            SET memberProperties = :props
            WHERE memberID = :memberID
        ");
       foreach ($heightByMember as $memberId => $props) {
      $updateStr .= "UPDATE p4_members SET memberProperties =".json_encode($props)."    WHERE memberID = ". $memberId." ;";
   $updateStmt->execute([
            'props' => json_encode($props),
            'memberID' => $memberId
        ]);
       }

    // Step 2: Update memberProperties with height
  /*     $selectStmt = $pdo->prepare("SELECT memberProperties FROM p4_members WHERE memberID = :memberID");
    $updateStmt = $pdo->prepare("
        UPDATE p4_members
        SET memberProperties = :props
        WHERE memberID = :memberID
    ");

 $updatedCount = 0;
$updateStr="";
    foreach ($heightByMember as $memberId => $height) {
        $selectStmt->execute(['memberID' => $memberId]);
        $row = $selectStmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) continue;

        $props = json_decode($row['memberProperties'], true);
        if (!is_array($props)) $props = [];

        $props['height'] = $height;

       /* $updateStmt->execute([
            'props' => json_encode($props),
            'memberID' => $memberId
        ]);*/
    /*     $updateStr .= "
                                UPDATE p4_members
                                SET memberProperties =".json_encode($props)."
                                WHERE memberID = ". $memberId." ;";

        $updatedCount++;
    }*/

    //echo "Updated height for $updatedCount members.\n".$updateStr;
echo "\n".$updateStr;
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

?>
