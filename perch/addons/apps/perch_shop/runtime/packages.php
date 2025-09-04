<?php

function perch_shop_create_package($data)
{
    $API      = new PerchAPI(1.0, 'perch_shop');
    $Packages = new PerchShop_Packages($API);
    return $Packages->create($data);
}

function perch_shop_add_package_item($packageID, $data)
{
    $API   = new PerchAPI(1.0, 'perch_shop');
    $Items = new PerchShop_PackageItems($API);

    $data['packageID'] = $packageID;

    return  $Items->create($data);
}
function perch_shop_package_update_item($itemID, $qty)
{
    $API   = new PerchAPI(1.0, 'perch_shop');
    $Items = new PerchShop_PackageItems($API);
    $Item  = $Items->find((int)$itemID);
    if ($Item) {
        return $Item->update(['qty' => (int)$qty]);
    }
    return false;
}

function perch_shop_package_remove_item($itemID)
{
    $API   = new PerchAPI(1.0, 'perch_shop');
    $Items = new PerchShop_PackageItems($API);
    $Item  = $Items->find((int)$itemID);
    if ($Item) {
        return $Item->delete();
    }
    return false;
}

function perch_shop_update_package_status($status)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['perch_shop_package_id'])) {
        return false;
    }

    $API      = new PerchAPI(1.0, 'perch_shop');
    $Packages = new PerchShop_Packages($API);
    $Package  = $Packages->find_by_uuid($_SESSION['perch_shop_package_id']);

    if ($Package) {
        $Package->update(['status' => $status]);
    }

    return $Package;
}

function perch_shop_package_contents($opts = [], $return = false)
{
    $opts = PerchUtil::extend([
        'template'      => 'shop/products/package-summary/summary.html',
        'skip-template' => false,
    ], $opts);

    if ($opts['skip-template']) {
        $return = true;
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }


    if (!isset($_SESSION['perch_shop_package_id'])) {
        return false;
    }
$ShopRuntime = PerchShop_Runtime::fetch();
			$r = $ShopRuntime->get_package_items($opts);

if ($return) return $r;
		echo $r;
		PerchUtil::flush_output();
}

function perch_shop_future_packages($opts = [], $return = false)
{
    $opts = PerchUtil::extend([
        'template'      => 'packages/future.html',
        'skip-template' => false,
    ], $opts);

    if ($opts['skip-template']) {
        $return = true;
    }

    if (!perch_member_logged_in()) {
        if ($return) {
            return $opts['skip-template'] ? [] : '';
        }
        echo '';
        PerchUtil::flush_output();
        return true;
    }

    $Runtime    = PerchShop_Runtime::fetch();
    $customerID = $Runtime->get_customer_id();

    $API      = new PerchAPI(1.0, 'perch_shop');
    $Packages = new PerchShop_Packages($API);
    $packages = $Packages->get_for_customer($customerID);

    $data  = [];
    $today = time();

    if (PerchUtil::count($packages)) {
        foreach ($packages as $Package) {
            $date   = $Package->packageDate();
            $status = $Package->packageStatus();

            if ($status === 'pending' && $date) {
                $ts = strtotime($date);
                if ($ts >= $today) {
                    $data[] = [
                        'uuid'        => $Package->uuid(),
                        'packageDate' => $date,
                        'due'         => ($ts <= $today ? 1 : 0),
                    ];
                }
            }
        }
    }

    $Template = new PerchTemplate('shop/' . $opts['template']);
    $r        = $Template->render(['packages' => $data]);

    if ($return) {
        return $r;
    }

    echo $r;
    PerchUtil::flush_output();
    return true;
}

function perch_shop_future_packages($opts = [], $return = false)
{
    $opts = PerchUtil::extend([
        'template'      => 'packages/future.html',
        'skip-template' => false,
    ], $opts);

    if ($opts['skip-template']) {
        $return = true;
    }

    if (!perch_member_logged_in()) {
        if ($return) {
            return $opts['skip-template'] ? [] : '';
        }
        echo '';
        PerchUtil::flush_output();
        return true;
    }

    $Runtime    = PerchShop_Runtime::fetch();
    $customerID = $Runtime->get_customer_id();

    $API      = new PerchAPI(1.0, 'perch_shop');
    $Packages = new PerchShop_Packages($API);
    $packages = $Packages->get_for_customer($customerID);

    $data  = [];
    $today = time();

    if (PerchUtil::count($packages)) {
        foreach ($packages as $Package) {
            $date   = $Package->packageDate();
            $status = $Package->packageStatus();

            if ($status === 'pending' && $date) {
                $ts = strtotime($date);
                if ($ts >= $today) {
                    $data[] = [
                        'uuid'        => $Package->uuid(),
                        'packageDate' => $date,
                        'due'         => ($ts <= $today ? 1 : 0),
                    ];
                }
            }
        }
    }

    $Template = new PerchTemplate('shop/' . $opts['template']);
    $r        = $Template->render(['packages' => $data]);

    if ($return) {
        return $r;
    }

    echo $r;
    PerchUtil::flush_output();
    return true;
}
