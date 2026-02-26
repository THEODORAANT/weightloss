<?php
# Side panel
echo $HTML->side_panel_start();
echo $HTML->para('Create order to pharmacy.');
echo $HTML->side_panel_end();

# Main panel
echo $HTML->main_panel_start();
include('_subnav.php');

echo $HTML->heading1('Create order to pharmacy ‘%s’', $HTML->encode($Order->id()));

if ($success) {
    echo $Alert->set('success', PerchLang::get('The order has been successfully sent to the pharmacy.<div class="submit-bar"><div class="submit-bar-actions"> <a name="btn" id="btnSubmit" href="/perch/addons/apps/perch_shop_orders/order/?id='.$Order->id().'" class="button button button-simple">Back</a></div></div>'));
} else {
    echo $Alert->set('warning', PerchLang::get('Create order to pharmacy failed: %s', '<code>'.$HTML->encode($message).'</code>'));
    if ($script_output !== '') {
        echo '<pre>'.$HTML->encode($script_output).'</pre>';
    }
    echo '<br/><div class="submit-bar"><div class="submit-bar-actions"><a name="btn" id="btnSubmit" href="/perch/addons/apps/perch_shop_orders/order/?id='.$Order->id().'" class="button button button-simple">Back</a></div></div>';
}

echo $Alert->output();

echo $HTML->main_panel_end();
