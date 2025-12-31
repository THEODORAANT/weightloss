<?php
$isLoggedIn = perch_member_logged_in();

perch_layout('client/header', [
    'page_title' => perch_page_title(true),
]);
 if(!perch_twillio_customer_verified()){
verify_customer_from_form();
}else{
echo "phone is verified!";
}

 ?>
