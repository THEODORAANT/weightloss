<?php
    include('../../../../core/inc/api.php');

    $API  = new PerchAPI(1.0, 'perch_members');
    $HTML = $API->get('HTML');
    $Lang = $API->get('Lang');
    $DB   = PerchDB::fetch();

    $Perch->page_title = $Lang->get('App registrations report');

    include('../modes/_subnav.php');

    $members_table = PERCH_DB_PREFIX . 'members';
    $sql = sprintf(
        'SELECT memberID, memberCreated, memberProperties'
        . ' FROM %s'
        . ' WHERE memberProperties LIKE %s'
        . ' ORDER BY memberCreated DESC',
        $members_table,
        $DB->pdb('%device:app%')
    );

    $rows = $DB->get_rows($sql) ?: [];

    $registrations = [];
    foreach ($rows as $row) {
        $properties = PerchUtil::json_safe_decode($row['memberProperties'], true);
        if (!is_array($properties)) {
            $properties = [];
        }

        $sname = '';
        if (isset($properties['sname'])) {
            $sname = trim((string)$properties['sname']);
        } elseif (isset($properties['last_name'])) {
            $sname = trim((string)$properties['last_name']);
        } elseif (isset($properties['surname'])) {
            $sname = trim((string)$properties['surname']);
        }

        $registrations[] = [
            'memberID'      => (int)$row['memberID'],
            'memberCreated' => $row['memberCreated'],
            'sname'         => $sname,
        ];
    }

    include(PERCH_CORE . '/inc/top.php');
?>

<div class="inner">
    <h1><?php echo $HTML->encode($Lang->get('App registrations')); ?></h1>
    <p><?php echo $HTML->encode($Lang->get('Showing members where memberProperties contains "device:app".')); ?></p>

    <table class="d listing" role="table">
        <thead>
        <tr>
            <th><?php echo $HTML->encode($Lang->get('Member ID')); ?></th>
            <th><?php echo $HTML->encode($Lang->get('SName')); ?></th>
            <th><?php echo $HTML->encode($Lang->get('Registered')); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php if (!count($registrations)) { ?>
            <tr>
                <td colspan="3"><?php echo $HTML->encode($Lang->get('No app registrations found.')); ?></td>
            </tr>
        <?php } else {
            foreach ($registrations as $registration) { ?>
                <tr>
                    <td><?php echo $HTML->encode((string)$registration['memberID']); ?></td>
                    <td><?php echo $HTML->encode($registration['sname']); ?></td>
                    <td><?php echo $HTML->encode((string)$registration['memberCreated']); ?></td>
                </tr>
            <?php }
        } ?>
        </tbody>
    </table>
</div>

<?php include(PERCH_CORE . '/inc/btm.php');
