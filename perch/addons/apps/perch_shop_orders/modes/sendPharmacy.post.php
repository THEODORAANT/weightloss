<?php
    # Side panel
  echo $HTML->side_panel_start();
    echo $HTML->para('Send order to pharmacy.');
    echo $HTML->side_panel_end();

    # Main panel
    echo $HTML->main_panel_start();
    include('_subnav.php');

    echo $HTML->heading1('Send order ‘%s’', $HTML->encode($Order->id()));


 if($message){
 if($success){
  echo $Alert->set('success', PerchLang::get('The order has been successfully send to the pharmacy.<div class="submit-bar"><div class="submit-bar-actions"> <a  name="btn" id="btnSubmit" href="/perch/addons/apps/perch_shop_orders/order/?id='.$Order->id().'" class="button button button-simple">Back</a></div></div>'));
   echo $Alert->output();
 }else{
    echo  $Alert->set('warning', PerchLang::get('Error send on api message %s ','<code>'.$message.'</code>'));
    echo '<br/><div class="submit-bar"><div class="submit-bar-actions">
    <a  name="btn" id="btnSubmit" href="/perch/addons/apps/perch_shop_orders/sendToPharmacy/?id='.$Order->id().'" class="button button button-simple">Try Again</a></div></div>';
                                          echo $Alert->output();
                                          }
 }else{

echo'<form method="post" action="/perch/addons/apps/perch_shop_orders/sendToPharmacy/?id='.$Order->id().'" enctype="multipart/form-data" class="app form-simple">
<div role="alert" class="notification notification-warning"><svg role="img" width="16" height="16" class="icon icon-alert"> <use xlink:href="/perch/core/assets/svg/core.svg#alert"></use> </svg>Are you sure you wish to send this?Is Member;s docs approved?</div>
<input type="hidden" id="orderID" name="orderID" value="'.$Order->id().'" class="">
<div class="submit-bar"><div class="submit-bar-actions"><input type="submit" name="btnSubmit" id="btnSubmit" value="Save" class="button button button-simple"><input type="hidden" name="formaction" value="perch_shop"><input type="hidden" name="token" value="51f3fe1fafc4d507a0f64038be4f42e5"> or <a href="/perch/addons/apps/perch_shop">Cancel</a></div></div></form>';

  }  echo $HTML->main_panel_end();
