<?php

class PerchShop_PackageItems extends PerchShop_Factory
{
    public $api_method         = 'packages';
    public $api_list_method    = 'packages';
    public $singular_classname = 'PerchShop_PackageItem';
    public $static_fields      = ['packageID', 'productID', 'variantID', 'qty','paymentStatus'];

    protected $table               = 'shop_package_items';
    protected $pk                  = 'itemID';
    protected $index_table         = false;
    protected $default_sort_column = 'itemID';

    protected $event_prefix = 'shop.packageitem';

    public function get_for_package($packageID)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE packageID=' . $this->db->pdb($packageID);
        return $this->return_instances($this->db->get_rows($sql));
    }

     public function create($items)
        {
        $packageID=$items["packageID"];
        unset($items["packageID"]);
           // print_r($items);
            $sql="";
           // $this->create($items);
  $data = [];
               $cols   = [];
                    $vals   = [];

                    foreach($items as $month => $products) {
                     $cols[] = "month";
                      $vals[]=$month;
                        $data["month"] =$month;
                    foreach ($products as $key => $value){
                       $data[$key] =$value;
                       $cols[] = $key;
                     $vals[] =$this->db->pdb($value);
                    }
 		$sql .= 'INSERT INTO ' .  $this->table . '(' . implode(',', $cols) . ') VALUES(' . implode(',', $vals) . ');';
//echo $sql;
               $cols   = [];
                    $vals   = [];
                      $data = [];
                    }



                   $this->db->execute($sql);

      return true;
    }
}

