<?php

class PerchShop_Packages extends PerchShop_Factory
{
    public $api_method         = 'packages';
    public $api_list_method    = 'packages';
    public $singular_classname = 'PerchShop_Package';
    public $static_fields      = ['customerID', 'month', 'status', 'packageDate', 'packageStatus', 'uuid', 'nextBillingDate','paymentStatus','totalPaidMonths'];
    public $remote_fields      = ['customerID', 'month', 'status', 'packageDate', 'packageStatus', 'uuid', 'nextBillingDate','paymentStatus','totalPaidMonths'];
    protected $table               = 'shop_packages';
     protected $table_items               = 'shop_package_items';
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

    public function get_for_customer($customerID)
    {
        $sql = 'SELECT i.*,p.nextBillingDate FROM ' . $this->table . ' as p, '.PERCH_DB_PREFIX.'shop_package_items as i     WHERE
         p.paymentStatus="pending"
        and p.customerID=' . $this->db->pdb((int)$customerID);

        return $this->return_instances($this->db->get_rows($sql));
    }

    	public function get_admin_listing($status=array('paid'), $Paging=false)
    	{
    		$sort_val = null;
            $sort_dir = null;


    		if ($Paging && $Paging->enabled()) {
                $sql = $Paging->select_sql();
                list($sort_val, $sort_dir) = $Paging->get_custom_sort_options();
            }else{
                $sql = 'SELECT';
            }

            $sql .= ' p.*, c.*, CONCAT(customerFirstName, " ", customerLastName) AS customerName
                    FROM ' . $this->table .' p, '.PERCH_DB_PREFIX.'shop_customers c
                    WHERE p.customerID=c.customerID

                    	AND p.paymentStatus IN ('.$this->db->implode_for_sql_in($status).')';

    		if ($sort_val) {
                $sql .= ' ORDER BY '.$sort_val.' '.$sort_dir;
            } else {
    	        if (isset($this->default_sort_column)) {
    	            $sql .= ' ORDER BY p.created DESC ';
    	        }
    	    }

            if ($Paging && $Paging->enabled()) {
                $sql .=  ' '.$Paging->limit_sql();
            }

            $results = $this->db->get_rows($sql);

            if ($Paging && $Paging->enabled()) {
                $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
            }

            return $this->return_instances($results);
    	}
}

