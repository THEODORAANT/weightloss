<?php

class PerchShop_PackageItem extends PerchShop_Base
{
    protected $factory_classname = 'PerchShop_PackageItems';
    protected $table             = 'shop_package_items';
    protected $pk                = 'itemID';
    protected $index_table       = false;

    protected $event_prefix = 'shop.packageitem';

    public function set_status_paid($orderID)
    {
        $result = $this->update([
            'orderID' => $orderID,
            'paymentStatus' => 'paid',
        ]);

        if ($result === false) {
            return false;
        }

        if ((int)$this->month() !== 1) {
            return true;
        }

        $payment_date = $this->resolve_payment_date($orderID);

        $Items = new PerchShop_PackageItems($this->api);
        $package_items = $Items->get_for_package($this->packageID());

        if (!PerchUtil::count($package_items)) {
            return true;
        }

        $all_updated = true;
        $next_billing_date = null;

        foreach ($package_items as $PackageItem) {
            if ((int)$PackageItem->itemID() === (int)$this->itemID()) {
                continue;
            }

            $month_number = (int)$PackageItem->month();
            if ($month_number <= 1) {
                continue;
            }

            $target_date = $payment_date->modify('+' . ($month_number - 1) . ' month');
            $formatted   = $target_date->format('Y-m-d');

            if ($PackageItem->billingDate() !== $formatted) {
                if ($PackageItem->update(['billingDate' => $formatted]) === false) {
                    $all_updated = false;
                }
            }

            $status = strtolower((string)$PackageItem->paymentStatus());
            if ($status !== 'paid') {
                if ($next_billing_date === null || $formatted < $next_billing_date) {
                    $next_billing_date = $formatted;
                }
            }
        }

        if ($next_billing_date !== null) {
            $Packages = new PerchShop_Packages($this->api);
            $Package  = $Packages->find_by_uuid($this->packageID());

            if ($Package) {
                if ($Package->update(['nextBillingDate' => $next_billing_date]) === false) {
                    $all_updated = false;
                }
            }
        }

        return $all_updated;
    }

    private function resolve_payment_date($orderID)
    {
        if ($orderID) {
            $Orders = new PerchShop_Orders($this->api);
            $Order  = $Orders->find((int)$orderID);

            if ($Order) {
                $raw_date = $Order->orderCreated();

                if ($raw_date) {
                    $date = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $raw_date);

                    if ($date instanceof \DateTimeImmutable) {
                        return $date;
                    }

                    try {
                        return new \DateTimeImmutable($raw_date);
                    } catch (\Exception $e) {
                        // fall back to current date below
                    }
                }
            }
        }

        return new \DateTimeImmutable('today');
    }
}

