<?php
$log_dir = realpath(__DIR__.'/../../../../../logs/notifications');
//$log_dir  = __DIR__ . '/logs/notifications';
//$log_dir  = __DIR__ . '/logs/notifications';
$logs = [];

$send_page_base = rtrim($API->app_path('perch_shop_orders'), '/') . '/package_admin/';
$package_items_factory = null;
$packages_factory = null;

if (class_exists('PerchShop_PackageItems', true) && class_exists('PerchShop_Packages', true)) {
    $shop_api = new PerchAPI(1.0, 'perch_shop');
    $package_items_factory = new PerchShop_PackageItems($shop_api);
    $packages_factory = new PerchShop_Packages($shop_api);
}

$build_link = function(array $entry) use ($send_page_base, $package_items_factory, $packages_factory) {
    $query = [];

    if (!empty($entry['itemID'])) {
        $item_id = (int)$entry['itemID'];
        if ($item_id > 0) {
            $query['itemID'] = $item_id;

            if ($package_items_factory && $packages_factory) {
                $PackageItem = $package_items_factory->find($item_id);
                if ($PackageItem) {
                    $package_uuid = $PackageItem->packageID();
                    if ($package_uuid) {
                        $query['packageUUID'] = $package_uuid;

                        $Package = $packages_factory->find_by_uuid($package_uuid);
                        if ($Package) {
                            $query['packageID'] = $Package->id();
                        }
                    }
                }
            }
        }
    }

    if (!empty($entry['customerID'])) {
        $query['customerID'] = $entry['customerID'];
    }

    if (!empty($entry['billingDate'])) {
        $query['billingDate'] = $entry['billingDate'];
    }

    if (empty($query)) {
        return null;
    }

    return $send_page_base . '?' . http_build_query($query);
};

if ($log_dir && is_dir($log_dir)) {
    $files = glob($log_dir . '/send_payment_notification*.log');
    sort($files);
    foreach ($files as $file) {
        $entries = [];
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $parts = explode('|', $line);
            if (count($parts) >= 5) {
                $entry = [
                    'itemID' => $parts[0],
                    'customerID' => $parts[1],
                    'billingDate' => $parts[2],
                    'loggedAt' => $parts[3],
                    'status' => $parts[4] ?? ''
                ];
                $entry['link'] = $build_link($entry);
                $entries[] = $entry;
            } elseif (count($parts) >= 3) {
                $entry = [
                    'itemID' => '',
                    'customerID' => $parts[0],
                    'billingDate' => $parts[1],
                    'loggedAt' => $parts[2],
                    'status' => $parts[3] ?? ''
                ];
                $entry['link'] = $build_link($entry);
                $entries[] = $entry;
            }
        }
        $logs[basename($file)] = $entries;
    }
}
