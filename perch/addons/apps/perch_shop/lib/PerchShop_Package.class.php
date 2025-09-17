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

        public function get_unpaid_items()
        {
            $Items = new PerchShop_PackageItems($this->api);
            return $Items->get_unpaid_for_package($this->uuid());
        }

    public function update($data)
    {
        $should_cancel_unpaid = false;

        if (isset($data['status'])) {
            $incoming_status = strtolower(trim((string)$data['status']));

            if ($incoming_status === 'canceled') {
                $incoming_status = 'cancelled';
            }

            if ($incoming_status === 'cancelled') {
                $data['status'] = 'cancelled';
                $should_cancel_unpaid = true;
            }
        }

        $result = parent::update($data);

        if ($result !== false && $should_cancel_unpaid) {
            $cascade_result = $this->cancel_unpaid_payment_records();

            if ($cascade_result === false) {
                $result = false;
            }
        }

        return $result;
    }

    	public function set_customer($customerID)
    	{

    		$this->update(['customerID'=>$customerID]);
    	}

        public function set_status(){
                  $currentMonths = (int)$this->totalPaidMonths();

                      $paymentStatus= 'pending';
                        $paidmonths=$currentMonths + 1; echo  $paidmonths;
                      if( $paidmonths==$this->months()) $paymentStatus= 'paid';
                                     $this->update([
                                                        'totalPaidMonths' => $paidmonths,
                                                        'paymentStatus' => $paymentStatus,
                                                    ]);
          $sql = 'UPDATE '.PERCH_DB_PREFIX.'shop_package_items SET paymentStatus='. $this->db->pdb('paid') . '
          WHERE month=' . $this->db->pdb($paidmonths).' and  packageID=' . $this->db->pdb($this->uuid());

                           $this->db->execute($sql);

          if($currentMonths<=0){
              $Items = new PerchShop_PackageItems($this->api);
              $package_items = $Items->get_for_package($this->uuid());

              if(PerchUtil::count($package_items)){
                  $payment_date = $this->determine_first_payment_date($package_items);
                  $this->cascade_first_payment_schedule($payment_date, $package_items);
              }
          }
          }

    public function set_orderID($orderID){
 $sql = 'UPDATE '.PERCH_DB_PREFIX.'shop_package_items SET orderID='. $this->db->pdb($orderID) . '
          WHERE month="1" and  packageID=' . $this->db->pdb($this->uuid());

                           $this->db->execute($sql);
                           $this->update([ 'orderID' => $orderID
                                          ]);


    }
    private function determine_first_payment_date($package_items)
    {
        $orderID = $this->orderID();

        if (!$orderID) {
            foreach ($package_items as $PackageItem) {
                if ((int)$PackageItem->month() === 1) {
                    $item_order_id = $PackageItem->orderID();

                    if ($item_order_id) {
                        $orderID = $item_order_id;
                        break;
                    }
                }
            }
        }

        return $this->resolve_payment_date($orderID);
    }

    private function cascade_first_payment_schedule(\DateTimeImmutable $payment_date, $package_items)
    {
        $next_billing_date = null;

        foreach ($package_items as $PackageItem) {
            $month_number = (int)$PackageItem->month();

            if ($month_number <= 1) {
                continue;
            }

            $target_date = $payment_date->modify('+' . ($month_number - 1) . ' month');
            $formatted   = $target_date->format('Y-m-d');

            if ($PackageItem->billingDate() !== $formatted) {
                $PackageItem->update(['billingDate' => $formatted]);
            }

            $status = strtolower((string)$PackageItem->paymentStatus());
            if ($status !== 'paid') {
                if ($next_billing_date === null || $formatted < $next_billing_date) {
                    $next_billing_date = $formatted;
                }
            }
        }

        if ($next_billing_date !== null && $this->nextBillingDate() !== $next_billing_date) {
            $this->update(['nextBillingDate' => $next_billing_date]);
        }
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

    private function cancel_unpaid_payment_records()
    {
        $success = true;

        $current_payment_status = strtolower((string)$this->paymentStatus());
        if ($current_payment_status !== 'paid' && $current_payment_status !== 'cancelled') {
            if (!parent::update(['paymentStatus' => 'cancelled'])) {
                $success = false;
            }
        }

        $Items = new PerchShop_PackageItems($this->api);
        $package_items = $Items->get_for_package($this->uuid());

        if (PerchUtil::count($package_items)) {
            foreach ($package_items as $PackageItem) {
                $item_status = strtolower((string)$PackageItem->paymentStatus());

                if ($item_status === 'paid' || $item_status === 'cancelled') {
                    continue;
                }

                if (!$PackageItem->update(['paymentStatus' => 'cancelled'])) {
                    $success = false;
                }
            }
        }

        return $success;
    }
}


