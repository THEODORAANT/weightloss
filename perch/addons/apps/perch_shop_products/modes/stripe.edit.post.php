<?php
    echo $HTML->title_panel([
        'heading' => $Lang->get('Stripe product and price IDs'),
    ], $CurrentUser);

    echo $Form->form_start('stripe-edit');

    echo $message;

    echo $Form->select_field('productID', $Lang->get('Product'), $product_options, $selected_product_id);

    $fields_hidden = $selected_product ? '' : ' style="display:none;"';

    echo '<div id="stripe-fields"' . $fields_hidden . '>';
    echo $Form->text_field('stripe_product_id_test', $Lang->get('Stripe product ID (Test mode)'), $stripe_product_id_test, 'l');
    echo $Form->text_field('stripe_product_id', $Lang->get('Stripe product ID'), $stripe_product_id, 'l');
    echo $Form->text_field('stripe_price_id_test', $Lang->get('Stripe price ID (Test mode)'), $stripe_price_id_test, 'l');
    echo $Form->text_field('stripe_price_id', $Lang->get('Stripe price ID'), $stripe_price_id, 'l');
    echo $Form->submit_field('btnSubmit', $Lang->get('Save'), $API->app_path());
    echo '</div>';

    echo $Form->form_end();

    ?>
    <script>
    (function () {
        var productSelect = document.getElementById('productID');
        var fieldsWrap = document.getElementById('stripe-fields');

        if (!productSelect || !fieldsWrap) {
            return;
        }

        function toggleFields() {
            fieldsWrap.style.display = productSelect.value ? '' : 'none';
        }

        productSelect.addEventListener('change', function () {
            toggleFields();
            var baseUrl = window.location.pathname;

            if (productSelect.value) {
                window.location.href = baseUrl + '?id=' + encodeURIComponent(productSelect.value);
                return;
            }

            window.location.href = baseUrl;
        });

        toggleFields();
    }());
    </script>
<?php
