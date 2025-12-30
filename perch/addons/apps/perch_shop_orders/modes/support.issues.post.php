<?php

    $issue_type_lookup = [];
    foreach ($issue_types as $type) {
        $issue_type_lookup[$type['value']] = $type['label'];
    }

    $status_lookup = [];
    foreach ($status_options as $status) {
        $status_lookup[$status['value']] = $status['label'];
    }

    $panel_heading = $Lang->get('Support issues log');
    if (!empty($current_issue)) {
        $panel_heading = $Lang->get('Edit support issue #%s', (int)$current_issue['id']);
    }

    echo $HTML->title_panel([
        'heading' => $panel_heading,
    ], $CurrentUser);

    include('_subnav.php');

    if ($message) {
        echo $message;
    }

?>

<h2 id="support-issue-form">
    <?php echo !empty($current_issue) ? $Lang->get('Edit issue') : $Lang->get('Log a new issue'); ?>
</h2>

<?php
    echo $Form->form_start('support-issue');

        if (!empty($current_issue)) {
            echo '<input type="hidden" name="issue_id" value="' . $HTML->encode($current_issue['id']) . '">';
        }

        echo $Form->select_field('issueType', $Lang->get('Issue type'), $issue_types, $Form->get_value('issueType', 'complaint'));
        echo $Form->text_field('summary', $Lang->get('Summary'), $Form->get_value('summary', ''));
        echo $Form->textarea_field('details', $Lang->get('Details'), $Form->get_value('details', ''), 'input-simple');
        echo $Form->text_field('memberID', $Lang->get('Member ID'), $Form->get_value('memberID', ''));
        echo $Form->text_field('orderID', $Lang->get('Order ID'), $Form->get_value('orderID', ''));
        echo $Form->text_field('orderNumber', $Lang->get('Order number'), $Form->get_value('orderNumber', ''));
        echo $Form->text_field('trackingNumber', $Lang->get('Tracking number'), $Form->get_value('trackingNumber', ''));
        echo $Form->date_field('eventDate', $Lang->get('Event date'), $Form->get_value('eventDate', ''));
        echo $Form->select_field('status', $Lang->get('Status'), $status_options, $Form->get_value('status', 'open'));
        echo $Form->textarea_field('resolution', $Lang->get('Resolution / follow-up'), $Form->get_value('resolution', ''), 'input-simple');

        echo $Form->submit_field('btnSubmit', !empty($current_issue) ? $Lang->get('Update issue') : $Lang->get('Save issue'), $API->app_path());

    echo $Form->form_end();
?>

<h2><?php echo $Lang->get('Export issues'); ?></h2>

<form class="filter-bar" method="get" action="<?php echo $HTML->encode($Form->action()); ?>">
    <input type="hidden" name="export" value="1">
    <div class="field-group">
        <label><?php echo $Lang->get('Issue type'); ?></label>
        <select name="export_issue_type">
            <option value=""><?php echo $Lang->get('All'); ?></option>
            <?php foreach ($issue_types as $type): ?>
                <option value="<?php echo $HTML->encode($type['value']); ?>" <?php echo (PerchUtil::get('export_issue_type', 'complaint') === $type['value']) ? 'selected' : ''; ?>>
                    <?php echo $HTML->encode($type['label']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="field-group">
        <label><?php echo $Lang->get('From (dd/mm/yy)'); ?></label>
        <input type="text" name="export_from" value="<?php echo $HTML->encode(PerchUtil::get('export_from', '')); ?>" placeholder="dd/mm/yy">
    </div>
    <div class="field-group">
        <label><?php echo $Lang->get('To (dd/mm/yy)'); ?></label>
        <input type="text" name="export_to" value="<?php echo $HTML->encode(PerchUtil::get('export_to', '')); ?>" placeholder="dd/mm/yy">
    </div>
    <div class="field-group buttons">
        <button type="submit" class="button"><?php echo $Lang->get('Download CSV'); ?></button>
    </div>
</form>

<h2><?php echo $Lang->get('Recent entries'); ?></h2>

<form class="filter-bar" method="get" action="<?php echo $HTML->encode($Form->action()); ?>">
    <div class="field-group">
        <label><?php echo $Lang->get('Issue type'); ?></label>
        <select name="filter_issue_type">
            <option value=""><?php echo $Lang->get('All'); ?></option>
            <?php foreach ($issue_types as $type): ?>
                <option value="<?php echo $HTML->encode($type['value']); ?>" <?php echo (PerchUtil::get('filter_issue_type') === $type['value']) ? 'selected' : ''; ?>>
                    <?php echo $HTML->encode($type['label']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="field-group">
        <label><?php echo $Lang->get('Status'); ?></label>
        <select name="filter_status">
            <option value=""><?php echo $Lang->get('All'); ?></option>
            <?php foreach ($status_options as $status): ?>
                <option value="<?php echo $HTML->encode($status['value']); ?>" <?php echo (PerchUtil::get('filter_status') === $status['value']) ? 'selected' : ''; ?>>
                    <?php echo $HTML->encode($status['label']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="field-group">
        <label><?php echo $Lang->get('Member ID'); ?></label>
        <input type="text" name="filter_member" value="<?php echo $HTML->encode(PerchUtil::get('filter_member', '')); ?>">
    </div>

    <div class="field-group">
        <label><?php echo $Lang->get('Order ID'); ?></label>
        <input type="text" name="filter_order" value="<?php echo $HTML->encode(PerchUtil::get('filter_order', '')); ?>">
    </div>

    <div class="field-group buttons">
        <button type="submit" class="button"><?php echo $Lang->get('Apply filters'); ?></button>
        <a class="button button-simple" href="<?php echo $HTML->encode($Form->action()); ?>"><?php echo $Lang->get('Clear'); ?></a>
    </div>
</form>

<?php if (PerchUtil::count($issues)): ?>
    <table class="d">
        <thead>
            <tr>
                <th><?php echo $Lang->get('Date'); ?></th>
                <th><?php echo $Lang->get('Issue type'); ?></th>
                <th><?php echo $Lang->get('Summary'); ?></th>
                <th><?php echo $Lang->get('Member'); ?></th>
                <th><?php echo $Lang->get('Order'); ?></th>
                <th><?php echo $Lang->get('Tracking'); ?></th>
                <th><?php echo $Lang->get('Status'); ?></th>
                <th><?php echo $Lang->get('Logged by'); ?></th>
                <th><?php echo $Lang->get('Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($issues as $issue): ?>
            <tr>
                <td>
                    <?php
                        $date_text = $issue['eventDate'] ?: substr($issue['createdAt'], 0, 10);
                        echo $HTML->encode($date_text);
                    ?>
                </td>
                <td><?php echo $HTML->encode($issue_type_lookup[$issue['issueType']] ?? $issue['issueType']); ?></td>
                <td>
                    <strong><?php echo $HTML->encode($issue['summary']); ?></strong><br>
                    <?php if (!empty($issue['details'])): ?>
                        <small><?php echo nl2br($HTML->encode($issue['details'])); ?></small>
                    <?php endif; ?>
                    <?php if (!empty($issue['resolution'])): ?>
                        <div class="resolution"><em><?php echo nl2br($HTML->encode($issue['resolution'])); ?></em></div>
                    <?php endif; ?>
                </td>
                <td><?php echo $issue['memberID'] ? $HTML->encode($issue['memberID']) : '&ndash;'; ?></td>
                <td>
                    <?php
                        if ($issue['orderID']) {
                            echo $HTML->encode($issue['orderID']);
                        } elseif (!empty($issue['orderNumber'])) {
                            echo $HTML->encode($issue['orderNumber']);
                        } else {
                            echo '&ndash;';
                        }
                    ?>
                </td>
                <td><?php echo $issue['trackingNumber'] ? $HTML->encode($issue['trackingNumber']) : '&ndash;'; ?></td>
                <td><?php echo $HTML->encode($status_lookup[$issue['status']] ?? $issue['status']); ?></td>
                <td>
                    <?php
                        $author = $issue['loggedByName'] ?: '';
                        if ($issue['loggedBy']) {
                            $author = trim($author . ' (ID ' . (int)$issue['loggedBy'] . ')');
                        }
                        echo $author ? $HTML->encode($author) : '&ndash;';
                    ?>
                </td>
                <td>
                    <a class="button button-small" href="<?php echo $HTML->encode($Form->action()); ?>?id=<?php echo (int)$issue['id']; ?>#support-issue-form">
                        <?php echo $Lang->get('Edit'); ?>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p><?php echo $Lang->get('No issues logged yet.'); ?></p>
<?php endif; ?>
