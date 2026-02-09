<?php
    if (is_object($Order)) {
        $invoice = $Order->orderInvoiceNumber();
        if ($invoice == '') $invoice = $Order->id();
        $title = $Lang->get('Editing Order ‘%s’', $HTML->encode($invoice));
    }else{
        $title = $Lang->get('Creating a New Order');
    }

    echo $HTML->title_panel([
        'heading' => $title,
    ], $CurrentUser);

    include('_orders_smartbar.php');

   // if ($message) echo $message;

    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div class="template-help">' . $template_help_html . '</div>';
    }

    echo $HTML->heading2('Order');

    echo $Form->form_start('edit');

    echo $Form->fields_from_template($Template, $details);
    echo $Form->select_field('customer', 'Customer', $customer_opts, $details['customerID'] ?? '');
    echo '<div class="field-wrap"> <a  class="button button-icon button-simple" href="'.$API->app_path('perch_shop_orders').'/customers/edit/">Add new customer</a></div>';
         echo '<script>
                       var sel = document.querySelector("select[name=customer]");

                       var txt = document.querySelector("input[name=perch_customerID]");


                       if (sel && txt) {
                          // txt.value = sel.value;
                           sel.addEventListener("change", function() {
                               txt.value = this.value;
                           });
                       }
                     </script>';

        if (PerchUtil::count($order_items)) {
            echo $HTML->heading2('Order items');
            echo '<table class="d">';
            echo '<thead><tr>';
            echo '<th>'.$Lang->get('Item').'</th>';
            echo '<th>'.$Lang->get('SKU').'</th>';
            echo '<th>'.$Lang->get('Product').'</th>';
            echo '<th>'.$Lang->get('Qty').'</th>';
            echo '<th>'.$Lang->get('Unit Price').'</th>';
            echo '<th>'.$Lang->get('Tax').'</th>';
            echo '</tr></thead><tbody>';

            foreach ($order_items as $Item) {
                $item_id = (int)$Item->itemID();
                $current_product_id = (int)$Item->productID();
                $item_label = $Item->title();
                if ($Item->is_variant()) {
                    $item_label .= ' - '.$Item->productVariantDesc();
                }

                $options_html = '';
                foreach ($product_options as $product_id => $label) {
                    $selected = ((int)$product_id === $current_product_id) ? ' selected' : '';
                    $options_html .= '<option value="'.$HTML->encode($product_id).'"'.$selected.'>'.$HTML->encode($label).'</option>';
                }

                echo '<tr>';
                echo '<td>'.$HTML->encode($item_label).'</td>';
                echo '<td>'.$HTML->encode($Item->sku()).'</td>';
                echo '<td><select name="order_items['.$item_id.'][product_id]">'.$options_html.'</select></td>';
                echo '<td><input type="number" name="order_items['.$item_id.'][qty]" min="1" value="'.$HTML->encode($Item->itemQty()).'" class="input-text"></td>';
                echo '<td>'.$HTML->encode($Item->itemPrice()).'</td>';
                echo '<td>'.$HTML->encode($Item->itemTax()).'</td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
        }
        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    echo $Form->form_end();
