<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get('Viewing package'),

    ], $CurrentUser);

   // include('_order_smartbar.php');




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
                $output.=  '<td>'.$Item->itemID().'</td>';
                $output.=  '<td>'.($Item->productID() ? $Item->productVariantDesc() : '').'</td>';
                 $output.=  '<td>'.$Item->month().'</td>';
                $output.=  '<td>'.$Item->qty().'</td>';
                $output.=  '<td>'.$Item->paymentStatus().'</td>';
                $output.=  '<td>'.$Item->billingDate().'</td>';
                $output.=  '<td>'.$Item->orderID().'</td>';
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
