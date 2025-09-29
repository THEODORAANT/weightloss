<?php
	$Paging = $API->get('Paging');
	$Paging->set_per_page(24);
$sort="^orderCreated";
	$Packages   = new PerchShop_Packages($API);
	$PackageItems = new PerchShop_PackageItems($API);

    $Template   = $API->get('Template');
    $Template->set('shop/orders/filter.html', 'shop');

    $Form = $API->get('Form');
    $Form->handle_empty_block_generation($Template);

     $details = array();
            if ($Form->submitted()) {

                   $post = $_POST;

                   $data = $Form->get_posted_content($Template, $Orders, false, false);
                   $filerdata= json_encode($data);
                 // print_r( $filerdata);

                    $details=$data["orderDynamicFields"];
                $details =json_decode($details, TRUE);
 $packages = $Packages->get_by_properties($details, $Paging);


                     }else{
                     	$packages   = $Packages->get_admin_listing(['pending','paid'], $Paging);

                     }
