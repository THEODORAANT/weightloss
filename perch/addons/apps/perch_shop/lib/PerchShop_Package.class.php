<?php

class PerchShop_Package extends PerchShop_Base
{
    protected $factory_classname = 'PerchShop_Packages';
    protected $table             = 'shop_packages';
    protected $pk                = 'packageID';
    protected $index_table       = false;

    protected $event_prefix = 'shop.package';

    public function get_items()
    {
        $Items = new PerchShop_PackageItems($this->api);
        return $Items->get_for_package($this->uuid());
    }

    public function set_orderID($orderID){
        $this->update([
                                'orderID' => $orderID,
                            ]);
    }
    function nextMonthlyPayment(\DateTimeInterface $lastPayment): \DateTimeImmutable
    {
        // Clone the date to avoid modifying the original instance
        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $lastPayment->format('Y-m-d'));

        // Add one month; PHP takes care of month-end edge cases
        return $date->modify('+1 month');
    }
      public function update_next_BillingDate(){
          $lastBilling = new DateTimeImmutable($this->nextBillingDate());
          $nextBilling =  $this->nextMonthlyPayment($lastBilling)->format('Y-m-d');
           $this->update([
                            'nextBillingDate' => $nextBilling,
                        ]);
                        }
}

