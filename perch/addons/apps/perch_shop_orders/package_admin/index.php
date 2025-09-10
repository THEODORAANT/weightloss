<?php
include(__DIR__.'/../../../../core/inc/api.php');

$API = new PerchAPI(1.0, 'perch_shop');
$DB  = PerchDB::fetch();

$table = PERCH_DB_PREFIX . 'shop_packages';

// Handle new package submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerID      = (int)($_POST['customerID'] ?? 0);
    $billingType     = $_POST['billing_type'] ?? 'monthly';
    $nextBillingDate = $_POST['nextBillingDate'] ?? date('Y-m-d');

    $sql = 'INSERT INTO ' . $table .
           ' (customerID, billing_type, status, paymentStatus, nextBillingDate)' .
           ' VALUES (' . $DB->pdb($customerID) . ', ' . $DB->pdb($billingType) . ', ' .
           $DB->pdb('active') . ', ' . $DB->pdb('pending') . ', ' . $DB->pdb($nextBillingDate) . ')';
    $DB->execute($sql);
}

// Fetch all packages
$sql       = 'SELECT packageID, customerID, billing_type, status, paymentStatus, nextBillingDate FROM ' . $table . ' ORDER BY packageID DESC';
$packages  = $DB->get_rows($sql);

// Fetch pending packages
$sqlPending      = 'SELECT packageID, customerID, billing_type, status, paymentStatus, nextBillingDate FROM ' . $table .
                   ' WHERE paymentStatus=' . $DB->pdb('pending') . ' ORDER BY nextBillingDate ASC';
$pendingPackages = $DB->get_rows($sqlPending);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Packages</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        .pending { background: #ffe8e8; }
        form div { margin-bottom: 8px; }
    </style>
</head>
<body>
<h1>Admin Package Module</h1>

<h2>Add Package</h2>
<form method="post">
    <div>
        <label>Customer ID <input type="number" name="customerID" required></label>
    </div>
    <div>
        <label>Billing Type
            <select name="billing_type">
                <option value="monthly">Monthly</option>
                <option value="annual">Annual</option>
            </select>
        </label>
    </div>
    <div>
        <label>Next Billing Date <input type="date" name="nextBillingDate" required></label>
    </div>
    <button type="submit">Add Package</button>
</form>

<h2>All Packages</h2>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>Billing Type</th>
        <th>Status</th>
        <th>Payment Status</th>
        <th>Next Billing Date</th>
    </tr>
    </thead>
    <tbody>
    <?php if (PerchUtil::count($packages)) : ?>
        <?php foreach ($packages as $pkg): ?>
            <tr class="<?= $pkg['paymentStatus'] === 'pending' ? 'pending' : '' ?>">
                <td><?= (int)$pkg['packageID'] ?></td>
                <td><?= (int)$pkg['customerID'] ?></td>
                <td><?= htmlspecialchars($pkg['billing_type']) ?></td>
                <td><?= htmlspecialchars($pkg['status']) ?></td>
                <td><?= htmlspecialchars($pkg['paymentStatus']) ?></td>
                <td><?= htmlspecialchars($pkg['nextBillingDate']) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="6">No packages found</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<h2>Pending Packages</h2>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>Billing Type</th>
        <th>Status</th>
        <th>Payment Status</th>
        <th>Next Billing Date</th>
    </tr>
    </thead>
    <tbody>
    <?php if (PerchUtil::count($pendingPackages)) : ?>
        <?php foreach ($pendingPackages as $pkg): ?>
            <tr>
                <td><?= (int)$pkg['packageID'] ?></td>
                <td><?= (int)$pkg['customerID'] ?></td>
                <td><?= htmlspecialchars($pkg['billing_type']) ?></td>
                <td><?= htmlspecialchars($pkg['status']) ?></td>
                <td><?= htmlspecialchars($pkg['paymentStatus']) ?></td>
                <td><?= htmlspecialchars($pkg['nextBillingDate']) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="6">No pending packages</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</body>
</html>
