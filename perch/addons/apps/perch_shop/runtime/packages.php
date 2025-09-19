<?php

function perch_shop_create_package($data)
{
    $API      = new PerchAPI(1.0, 'perch_shop');
    $Packages = new PerchShop_Packages($API);
if (perch_member_logged_in()) {
			$member_id = perch_member_get('memberID');
			//get customer id from shop table to combine the two addons
			$Customers = new PerchShop_Customers($API);
            $Customer = $Customers->find_from_logged_in_member();
            $data["customerID"]=$Customer->id();
}
    return $Packages->create($data);
}

function perch_shop_add_package_item($packageID, $data)
{
    $API   = new PerchAPI(1.0, 'perch_shop');
    $Items = new PerchShop_PackageItems($API);

    //echo "perch_shop_add_package_item";
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
function perch_shop_package_remove($packageID)
{
    $API   = new PerchAPI(1.0, 'perch_shop');
      $Packages = new PerchShop_Packages($API);
      $Package  = $Packages->find_by_uuid($packageID);

    $Items = new PerchShop_PackageItems($API);
    $Itemasll  = $Items->get_for_package($packageID);
    					foreach($Itemasll as $Item){


    if ($Item) {
        return $Item->delete();
    }
    }
     $Package->delete();
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
 /*   if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }


    if (!isset($_SESSION['perch_shop_package_id']) && !isset($opts["itemID"])) {
        return false;
    }*/
$ShopRuntime = PerchShop_Runtime::fetch();
  if(isset($opts["itemID"])){
    $r = $ShopRuntime->get_package_item($opts);
  }else{
			$r = $ShopRuntime->get_package_items($opts);
}
if ($return) return $r;
		echo $r;
		PerchUtil::flush_output();
}
function perch_shop_future_packages($opts = [], $return = false)
{
    $opts = PerchUtil::extend([
        'template'      => 'shop/orders/packages/future.html',
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
        return false;
    }


  $ShopRuntime = PerchShop_Runtime::fetch();
  			$r = $ShopRuntime->get_package_future_items($opts);
  			if(!$r) $r='<div class="plan">No Future Payments</div>';

  if ($return) return $r;
  		echo $r;
  		PerchUtil::flush_output();
}
