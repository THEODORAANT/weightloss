<?php
//require_once __DIR__ . '/runtime.php';
	include(__DIR__.'/../../../../core/inc/api.php');

$API = new PerchAPI(1.0, 'perch_shop');
$DB  = PerchDB::fetch();

$table = PERCH_DB_PREFIX . 'shop_packages';
$sql   = 'SELECT packageID, customerID, status, nextBillingDate FROM ' . $table . ' where billing_type="monthly" and nextBillingDate>=NOW()  ORDER BY nextBillingDate ASC';

$packages = $DB->get_rows($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Package Future Payments</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <h1>Package Future Payments</h1>
    <table>
        <thead>
            <tr>
                <th>Package ID</th>
                <th>Customer ID</th>
                <th>Status</th>
                <th>Next Billing Date</th>
            </tr>
        </thead>
        <tbody>
        <?php if (PerchUtil::count($packages)) : ?>
            <?php foreach ($packages as $pkg): ?>
                <tr>
                    <td><?= (int)$pkg['packageID'] ?></td>
                    <td><?= (int)$pkg['customerID'] ?></td>
                    <td><?= htmlspecialchars($pkg['status']) ?></td>
                    <td><?= htmlspecialchars($pkg['nextBillingDate']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">No packages found</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
