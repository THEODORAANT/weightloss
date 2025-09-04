<?php

function perch_shop_create_package($data)
{
    $API      = new PerchAPI(1.0, 'perch_shop');
    $Packages = new PerchShop_Packages($API);
    echo "new package";
    print_r($data);
    return $Packages->create($data);
}

function perch_shop_add_package_item($packageID, $data)
{
    $API   = new PerchAPI(1.0, 'perch_shop');
    $Items = new PerchShop_PackageItems($API);

    echo "perch_shop_add_package_item";
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

    $API      = new PerchAPI(1.0, 'perch_shop');
    $Packages = new PerchShop_Packages($API);
    $Package  = $Packages->find_by_uuid($_SESSION['perch_shop_package_id']);


    if (!$Package) {
        return false;
    }

    $Items    = $Package->get_items();
    $Products = new PerchShop_Products($API);
    $data     = [];

    if (is_array($Items)) {
        foreach ($Items as $Item) {
            $title = '';

            if ($Item->variantID()) {
                $Product = $Products->find($Item->variantID());
                if ($Product) {
                    $title = $Product->title();
                }
            } elseif ($Item->productID()) {
                $Product = $Products->find($Item->productID());
                if ($Product) {
                    $title = $Product->title();
                }
            }

            $data[] = [
                'id'       => $Item->id(),
                'title'    => $title,
                'quantity' => $Item->qty(),
            ];
        }
    }

    $Template = new PerchTemplate('shop/' . $opts['template']);
    $r        = $Template->render(['packageitems' => $data]);

    if ($return) {
        return $r;
    }

    echo $r;
    PerchUtil::flush_output();
    return true;
}
