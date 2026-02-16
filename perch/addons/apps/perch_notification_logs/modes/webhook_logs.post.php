<div class="smartbar">
    <ul>
        <li><a href="<?php echo $HTML->encode($API->app_nav()); ?>"><?php echo $Lang->get('Notification Logs'); ?></a></li>
        <li class="selected"><a href="<?php echo $HTML->encode($API->app_nav().'/webhook_logs/'); ?>"><?php echo $Lang->get('Webhook Logs'); ?></a></li>
    </ul>
</div>

<form method="get" action="" class="searchform" style="margin: 1rem 0;">
    <div>
        <label for="search"><?php echo $Lang->get('Search (email/text)'); ?></label>
        <input type="text" id="search" name="search" value="<?php echo $HTML->encode($search); ?>" placeholder="<?php echo $Lang->get('e.g. email@example.com'); ?>" />
    </div>
    <div>
        <label for="limit"><?php echo $Lang->get('Limit'); ?></label>
        <input type="number" min="1" id="limit" name="limit" value="<?php echo (int)$limit; ?>" />
    </div>
    <div>
        <button class="button"><?php echo $Lang->get('Search'); ?></button>
    </div>
</form>

<?php if ($error): ?>
    <p class="notification notification-warning"><?php echo $HTML->encode($error); ?></p>
<?php elseif (!count($entries)): ?>
    <p><?php echo $Lang->get('No webhook logs found for this search.'); ?></p>
<?php else: ?>
    <p><strong><?php echo $Lang->get('Results'); ?>:</strong> <?php echo count($entries); ?></p>
    <table class="d">
        <thead>
            <tr>
                <th><?php echo $Lang->get('Timestamp'); ?></th>
                <th><?php echo $Lang->get('Raw Data'); ?></th>
                <th><?php echo $Lang->get('Decoded'); ?></th>
                <th><?php echo $Lang->get('Updates'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($entries as $entry): ?>
                <tr>
                    <td><?php echo $HTML->encode((string)($entry['timestamp'] ?? '')); ?></td>
                    <td><pre style="white-space: pre-wrap; max-width: 420px;"><?php echo $HTML->encode((string)($entry['raw_data'] ?? '')); ?></pre></td>
                    <td><pre style="white-space: pre-wrap; max-width: 420px;"><?php echo $HTML->encode((string)($entry['decoded'] ?? '')); ?></pre></td>
                    <td><pre style="white-space: pre-wrap; max-width: 420px;"><?php echo $HTML->encode((string)($entry['updates'] ?? '')); ?></pre></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
