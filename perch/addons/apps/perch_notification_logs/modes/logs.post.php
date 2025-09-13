<?php if (!empty($logs)): ?>
    <?php foreach ($logs as $filename => $entries): ?>
        <h2><?php echo $HTML->encode($filename); ?></h2>
        <table class="d">
            <thead>
                <tr>
                    <th><?php echo $Lang->get('Customer ID'); ?></th>
                    <th><?php echo $Lang->get('Billing Date'); ?></th>
                    <th><?php echo $Lang->get('Logged At'); ?></th>
                    <th><?php echo $Lang->get('Status'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($entries as $entry): ?>
                <tr>
                    <td><?php echo $HTML->encode($entry['customerID']); ?></td>
                    <td><?php echo $HTML->encode($entry['billingDate']); ?></td>
                    <td><?php echo $HTML->encode($entry['loggedAt']); ?></td>
                    <td><?php echo $HTML->encode($entry['status']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
<?php else: ?>
    <p><?php echo $Lang->get('No notification logs found.'); ?></p>
<?php endif; ?>
