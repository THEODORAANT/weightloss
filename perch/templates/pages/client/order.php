
<?php
    perch_layout('client/header', [
        'page_title' => perch_page_title(true),
    ]);


if (perch_member_logged_in()) {
?>

  <section class="shippin_section">
    <div class="container all_content mt-4">
        <h2 class="text-center fw-bolder">Order Details</h2>

        <div class="plans mt-4">

          <?php
          if(isset($_GET['id'])){
          $order_id=$_GET['id'];

            perch_shop_order($order_id);
perch_shop_order_items($order_id);
          }


            ?>

        </div>
    </div>
        <div class="container all_content mt-4">
            <h2 class="text-center fw-bolder">Track Order</h2>

            <div class="plans mt-4">
             <?php
                      if(isset($_GET['id'])){
                      $order_id=$_GET['id'];

           $r= perch_shop_track_order($order_id);
if($r){

 if(!isset($r["status"])){
 $r["status"]="-";
 }
 if(!isset($r["dispatchDate"])){
 $r["dispatchDate"]="-";
 }
  if(!isset($r["trackingNo"])){
  $r["trackingNo"]="-";
  }
echo '<div class="plan">
             <div>
           <h5>Status </h5>
                      <p> '. $r["status"].'</p>
                                 </div>
      <div>
            <h5>Dispatch Date</h5>
                <p>'. $r["dispatchDate"].'</p>
                           </div>
     <div>
        <h5>>Tracking No</h5><p> '. $r["trackingNo"].'</p>
                                                                                                                                                                                               </div>
        </div></div>';
                  }else{
                  echo '<div class="plan">
                               <div>
                             <h5>No Tracking info yet! </h5>

                                          </div>           </div>';

                  }    }  ?>


        </div></div>



</section>


  <?php
}
?>


    <?php
  perch_layout('getStarted/footer');?>
