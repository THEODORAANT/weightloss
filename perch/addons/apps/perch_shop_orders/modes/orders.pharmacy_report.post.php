<?php
    $refresh_url = $API->app_path() . '/pharmacy-report/';

    echo $HTML->title_panel([
        'heading' => $Lang->get('Pharmacy orders awaiting pharmacy action'),
    ], $CurrentUser);
?>

<div class="inner">
    <p><?php echo $Lang->get('Orders sent to the pharmacy before 14:00 and pending for more than 28 hours (excluding refunded orders).'); ?></p>

    <div class="submit-bar">
        <div class="submit-bar-actions">
            <button class="button" type="button" id="copy-to-clipboard"><?php echo $Lang->get('Copy list to clipboard'); ?></button>
            <a class="button button-simple" href="<?php echo $refresh_url; ?>"><?php echo $Lang->get('Refresh list'); ?></a>
        </div>
    </div>

    <?php if (PerchUtil::count($pharmacy_pending_orders)) : ?>
        <table class="d">
            <thead>
                <tr>
                    <th><?php echo $Lang->get('Client'); ?></th>
                    <th><?php echo $Lang->get('Email'); ?></th>
                    <th><?php echo $Lang->get('Pharmacy Order ID'); ?></th>
                    <th><?php echo $Lang->get('Pushed at'); ?></th>
                    <th><?php echo $Lang->get('Pharmacy status'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pharmacy_pending_orders as $order) : ?>
                    <?php
                        $full_name   = trim($order['customerFirstName'] . ' ' . $order['customerLastName']);
                        $copy_line   = trim($full_name . ' <' . $order['customerEmail'] . '>');
                        $status_text = $order['pharmacy_status'] !== '' ? $order['pharmacy_status'] : $Lang->get('Pending');
                    ?>
                    <tr data-copy-line="<?php echo htmlspecialchars($copy_line, ENT_QUOTES, 'UTF-8'); ?>">
                        <td><?php echo $HTML->encode($full_name); ?></td>
                        <td><?php echo $HTML->encode($order['customerEmail']); ?></td>
                        <td><?php echo $HTML->encode($order['pharmacy_orderID']); ?></td>
                        <td><?php echo $HTML->encode($order['created_at']); ?></td>
                        <td><?php echo $HTML->encode($status_text); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p><?php echo $Lang->get('No pharmacy-bound orders meet the pending criteria.'); ?></p>
    <?php endif; ?>
</div>

<script>
    (function() {
        var copyButton = document.getElementById('copy-to-clipboard');
        if (!copyButton) return;

        copyButton.addEventListener('click', function() {
            var rows = document.querySelectorAll('[data-copy-line]');
            var lines = Array.prototype.map.call(rows, function(row) {
                return row.getAttribute('data-copy-line');
            }).join('\n');

            navigator.clipboard.writeText(lines).then(function() {
                copyButton.classList.add('button-success');
                copyButton.textContent = '<?php echo $Lang->get('Copied'); ?>';
                window.setTimeout(function() {
                    copyButton.classList.remove('button-success');
                    copyButton.textContent = '<?php echo $Lang->get('Copy list to clipboard'); ?>';
                }, 2000);
            }).catch(function() {
                alert('<?php echo $Lang->get('Unable to copy to clipboard in this browser.'); ?>');
            });
        });
    })();
</script>
