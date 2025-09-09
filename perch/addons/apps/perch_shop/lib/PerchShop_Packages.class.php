<?php

class PerchShop_Packages extends PerchShop_Factory
{
    public $api_method         = 'packages';
    public $api_list_method    = 'packages';
    public $singular_classname = 'PerchShop_Package';
    public $static_fields      = ['customerID', 'month', 'status', 'packageDate', 'packageStatus', 'uuid', 'nextBillingDate','paymentStatus','totalPaidMonths'];
    public $remote_fields      = ['customerID', 'month', 'status', 'packageDate', 'packageStatus', 'uuid', 'nextBillingDate','paymentStatus','totalPaidMonths'];
    protected $table               = 'shop_packages';
    protected $pk                  = 'packageID';
    protected $default_sort_column = 'packageID';

    protected $event_prefix = 'shop.package';

    /*public function find_by_uuid($uuid)
          {
       $sql = 'SELECT * FROM ' . $this->table . ' WHERE uuid=' . $this->db->pdb($uuid);

          return $this->return_instances($this->db->get_rows($sql));
   }*/
   // PerchShop_Packages.class.php
   public function find_by_uuid($uuid)
   {
       return $this->get_one_by('uuid', $uuid);
   }
	public function set_customer($customerID)
	{

		$this->update(['customerID'=>$customerID]);
	}
    public function get_for_customer($customerID)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE customerID=' . $this->db->pdb((int)$customerID);
        return $this->return_instances($this->db->get_rows($sql));
    }
}

