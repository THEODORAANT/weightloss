<?php if (!empty($logs)): ?>
    <?php foreach ($logs as $filename => $entries): ?>
        <h2><?php echo $HTML->encode($filename); ?></h2>
        <table class="d">
            <thead>
                <tr>
                    <th><?php echo $Lang->get('Item ID'); ?></th>
                    <th><?php echo $Lang->get('Customer ID'); ?></th>
                    <th><?php echo $Lang->get('Billing Date'); ?></th>
                    <th><?php echo $Lang->get('Logged At'); ?></th>
                    <th><?php echo $Lang->get('Status'); ?></th>
                    <th><?php echo $Lang->get('Send Notification Page'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($entries as $entry): ?>
                <?php $link = $entry['link'] ?? null; ?>
                <tr>
                    <td>
                        <?php if ($link): ?>
                            <a href="<?php echo $HTML->encode($link); ?>"><?php echo $HTML->encode($entry['itemID'] ?? ''); ?></a>
                        <?php else: ?>
                            <?php echo $HTML->encode($entry['itemID'] ?? ''); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($link): ?>
                            <a href="<?php echo $HTML->encode($link); ?>"><?php echo $HTML->encode($entry['customerID'] ?? ''); ?></a>
                        <?php else: ?>
                            <?php echo $HTML->encode($entry['customerID'] ?? ''); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($link): ?>
                            <a href="<?php echo $HTML->encode($link); ?>"><?php echo $HTML->encode($entry['billingDate'] ?? ''); ?></a>
                        <?php else: ?>
                            <?php echo $HTML->encode($entry['billingDate'] ?? ''); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($link): ?>
                            <a href="<?php echo $HTML->encode($link); ?>"><?php echo $HTML->encode($entry['loggedAt'] ?? ''); ?></a>
                        <?php else: ?>
                            <?php echo $HTML->encode($entry['loggedAt'] ?? ''); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($link): ?>
                            <a href="<?php echo $HTML->encode($link); ?>"><?php echo $HTML->encode($entry['status'] ?? ''); ?></a>
                        <?php else: ?>
                            <?php echo $HTML->encode($entry['status'] ?? ''); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($link): ?>
                            <a class="button button-simple" href="<?php echo $HTML->encode($link); ?>"><?php echo $Lang->get('Open'); ?></a>
                        <?php else: ?>
                            &ndash;
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
<?php else: ?>
    <p><?php echo $Lang->get('No notification logs found.'); ?></p>
<?php endif; ?>
