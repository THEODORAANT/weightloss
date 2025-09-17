<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get('Viewing package'),

    ], $CurrentUser);

   // include('_order_smartbar.php');


    if ($message) echo $message;



    $form_action = $HTML->encode($Form->action());
    $csrf_token  = $HTML->encode(PerchSession::get('csrf_token'));

    if (!isset($status_choices) || !is_array($status_choices)) {
        $status_choices = ['confirmed', 'completed', 'cancelled'];
    }

    $current_status = trim((string)$Package->status());



$output= $HTML->heading2('Package');


    $output.= '<div class="inner">';
    $output.=  '<table class="d factsheet">';



    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Package ID').'</th>';
        $output.=  '<td>'.$HTML->encode($Package->packageID()).'</td>';
    $output.=  '</tr>';

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Created').'</th>';
        $output.=  '<td>'.$HTML->encode(PerchShop_Date::format($Package->created(), PERCH_DATE_SHORT.' '.PERCH_TIME_SHORT)).'</td>';
    $output.=  '</tr>';

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('PaymentStatus').'</th>';
        $output.=  '<td>'.$HTML->encode(ucfirst($Package->paymentStatus())).'</td>';
    $output.=  '</tr>';

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Status').'</th>';
        $output.=  '<td>';
            $output.=  '<form method="post" action="'.$form_action.'" class="inline-package-status">';
            $output.=  '<input type="hidden" name="formaction" value="update_status">';
            $output.=  '<input type="hidden" name="token" value="'.$csrf_token.'">';
            $output.=  '<select name="status">';
            foreach ($status_choices as $status_option) {
                $label = $Lang->get(ucfirst($status_option));
                $selected = ($status_option === $current_status) ? ' selected="selected"' : '';
                $output.=  '<option value="'.$HTML->encode($status_option).'"'.$selected.'>'.$HTML->encode($label).'</option>';
            }
            $output.=  '</select>';
            $output.=  '<button type="submit" class="button button-simple button-small">'.$HTML->encode($Lang->get('Save')).'</button>';
            $output.=  '</form>';
        $output.=  '</td>';
    $output.=  '</tr>';

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Billing Type').'</th>';
        $output.=  '<td>'.$HTML->encode($Package->billing_type()).'</td>';
    $output.=  '</tr>';

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Months').'</th>';
        $output.=  '<td>'.$HTML->encode($Package->months()).'</td>';
    $output.=  '</tr>';




    $output.=  '</table>';

    $output.=  '</div>';

$output.=  $HTML->heading2('Customer');

    $output.=  '<div class="inner"> <table class="d factsheet">';

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Customer ID').'</th>';
        $output.=  '<td><a href="'.$API->app_path('perch_shop_orders').'/customers/edit/?id='.$HTML->encode($Package->customerID()).'">'.$HTML->encode($Package->customerID()).'</a></td>';
    $output.=  '</tr>';


    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('First name').'</th>';
        $output.=  '<td>'.$HTML->encode($Customer->customerFirstName()).'</td>';
    $output.=  '</tr>';

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Last name').'</th>';
        $output.=  '<td>'.$HTML->encode($Customer->customerLastName()).'</td>';
    $output.=  '</tr>';

    $output.=  '<tr>';
        $output.=  '<th>'.$Lang->get('Email').'</th>';
        $output.=  '<td>'.$HTML->encode($Customer->customerEmail()).'</td>';
    $output.=  '</tr>';



           $output.=  '<tr style="background-color:#dca5ff">';

            $output.=  '<th style="background-color:#dca5ff" class="text-left">'.$Lang->get('Check Documents and Questionnaire: ').'</th>';
            $output.=  '<td style="background-color:#dca5ff" class="text-left"><a target="_blank" href="/perch/addons/apps/perch_members/edit/?id='.$HTML->encode($Customer->memberID()).'" >View</a></td>';
        $output.=  '</tr>';
    $output.=  '</table>';


    if (PerchUtil::count($items)) {


        //$output.=  $HTML->heading2('Order items');

        $output.=  '<table class="">';

        $output.=  '<thead>';
        $output.=  '<tr>';
                $output.=  '<th>'.$Lang->get('itemID').'</th>';
                $output.=  '<th>'.$Lang->get('productID').'</th>';
                $output.=  '<th>'.$Lang->get('month').'</th>';
                $output.=  '<th>'.$Lang->get('Qty').'</th>';
                $output.=  '<th>'.$Lang->get('paymentStatus').'</th>';
                $output.=  '<th>'.$Lang->get('billingDate').'</th>';
                $output.=  '<th>'.$Lang->get('orderID').'</th>';
        $output.=  '</tr>';
        $output.=  '</thead>';

        foreach($items as $Item) {
            #PerchUtil::debug($Item);
            $output.=  '<tr>';
                $output.=  '<td>'.$HTML->encode($Item->itemID()).'</td>';
                $product_desc = $Item->productID() ? $Item->productVariantDesc() : '';
                $output.=  '<td>'.$HTML->encode($product_desc).'</td>';
                $output.=  '<td>'.$HTML->encode($Item->month()).'</td>';
                $output.=  '<td>'.$HTML->encode($Item->qty()).'</td>';
                $output.=  '<td>'.$HTML->encode($Item->paymentStatus()).'</td>';
                $output.=  '<td>';

                if ((int)$Item->month() === 1) {
                    $billing_value = $Item->billingDate() ? $HTML->encode($Item->billingDate()) : '';
                     $output.=  $HTML->encode($Item->billingDate());
                      $billing_value = date($billing_value);
                    $output.=  '<form method="post" action="'.$form_action.'" class="inline-billing-date">';
                    $output.=  '<input type="hidden" name="formaction" value="update_billing_date">';
                    $output.=  '<input type="hidden" name="token" value="'.$csrf_token.'">';
                    $output.=  '<input type="hidden" name="itemID" value="'.$HTML->encode($Item->itemID()).'">';
                    $output.=  '<input type="date" name="billingDate" value="'.$billing_value.'" required />';
                    $output.=  '<button type="submit" class="button button-simple button-small">'.$HTML->encode($Lang->get('Save')).'</button>';
                    $output.=  '</form>';
                } else {
                    $output.=  $HTML->encode($Item->billingDate());
                }

                $output.=  '</td>';
                $output.=  '<td>'.$HTML->encode($Item->orderID()).'</td>';
            $output.=  '</tr>';
        }




        $output.=  '</table>';

    }

    $output.=  '</div>';


    $output.=  '</div>';







    // $properties = PerchUtil::json_safe_decode($Order->orderDynamicFields(), true);
    // if (PerchUtil::count($properties)) {

    //     $output2.=  $HTML->heading2('Additional information');


    //     $output2.=  '<div class="inner"><table class="d factsheet">';

    //     foreach($properties as $key => $val) {
    //         $output2.=  '<tr>';
    //             $output2.=  '<th>'.$HTML->encode($key).'</th>';
    //             $output2.=  '<td>'.$HTML->encode($val).'</td>';
    //         $output2.=  '</tr>';
    //     }

    //     $output2.=  '</table>';


    // }


    echo $output;


    function _if($val, $HTML)
    {
        if (isset($val) && $val) {
            return $HTML->encode($val).'<br>';
        }
    }
?>



<script type="text/javascript">
function exportInPDF(){


        var data = '';
        $.ajax({
            type: 'POST',
            url: 'export_pdf/loadpdf.php',
            data: {'output2':'<?=json_encode($output2)?>'},
            xhrFields: {
                responseType: 'blob'
            },
            success: function(response){
                var blob = new Blob([response]);
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = "<?=$HTML->encode($Order->orderInvoiceNumber())?>.pdf";
                link.click();
            },
            error: function(blob){
                console.log(blob);
            }
        });
}
</script>
