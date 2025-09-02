  <?php //echo "REQUEST_METHOD".$_SERVER['REQUEST_METHOD'];
  $body = file_get_contents('php://input');
  $data = json_decode($body, true); // true returns associative array

  // Accessing fields
//  echo $data['m'];

   $result=false;
    if(isset($data["m"])){
       $result= perch_shop_add_to_cart($data["m"]);

        }
 echo json_encode(['result' => $result]);
        ?>
