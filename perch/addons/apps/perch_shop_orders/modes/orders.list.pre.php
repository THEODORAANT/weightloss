<?php
	$Paging = $API->get('Paging');
	$Paging->set_per_page(24);
$sort="^orderCreated";
        $Orders   = new PerchShop_Orders($API);
        $OrderItems = new PerchShop_OrderItems($API);
        $Customers = new PerchShop_Customers($API);
        $Statuses = new PerchShop_OrderStatuses($API);
        $Tags = new PerchMembers_Tags($API);
 $Documents = new PerchMembers_Documents($API);
    $Users = new PerchUsers();
    $Template   = $API->get('Template');
    $Template->set('shop/orders/filter.html', 'shop');

    $Form = $API->get('Form');
    $Form->handle_empty_block_generation($Template);

     $details = array();
    $user_labels_by_id = [];

    $all_users = $Users->all();
    if (PerchUtil::count($all_users)) {
        foreach ($all_users as $User) {
            if (!$User->userEnabled()) {
                continue;
            }

            $name_parts = [];
            if ($User->userGivenName()) {
                $name_parts[] = $User->userGivenName();
            }
            if ($User->userFamilyName()) {
                $name_parts[] = $User->userFamilyName();
            }

            $label = trim(implode(' ', $name_parts));
            if ($label === '') {
                $label = $User->userUsername();
            }

            if ($User->userEmail()) {
                $label .= ' ('.$User->userEmail().')';
            }

            $user_labels_by_id[(int)$User->id()] = $label;
        }
    }
    if (!isset($default_statuses) || !is_array($default_statuses) || !count($default_statuses)) {
        $default_statuses = $Statuses->get_status_and_above('paid');
    }
            if ($Form->submitted()) {

                   $post = $_POST;

                   $data = $Form->get_posted_content($Template, $Orders, false, false);
                   $filerdata= json_encode($data);
                 // print_r( $filerdata);

                    $details=$data["orderDynamicFields"];
                $details =json_decode($details, TRUE);
                if (!is_array($details)) {
                    $details = [];
                }
$orders = $Orders->get_by_properties($details, $Paging, $default_statuses);


                     }else{
                        $orders   = $Orders->get_admin_listing($default_statuses, $Paging);

                     }
