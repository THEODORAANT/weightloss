<?php

class PerchShop_Promotion extends PerchShop_Base
{
	protected $factory_classname = 'PerchShop_Promotions';
	protected $table             = 'shop_promotions';
	protected $pk                = 'promoID';
	protected $index_table       = 'shop_index';

	protected $modified_date_column = 'promoUpdated';

	protected $event_prefix = 'shop.promotion';

	protected $duplicate_fields  = [
									'promoTitle' => 'title', 
									'promoActive' => 'active',
									'promoFrom' => 'from',
									'promoTo' => 'to',
								   ];

	public function get_use_count($customerID=null)
	{
		$Statuses = new PerchShop_OrderStatuses($this->api);
		$paid_statuses = $Statuses->get_status_and_above('paid');

		if (!PerchUtil::count($paid_statuses)) {
			$paid_statuses = ['paid'];
		}

		$sql = 'SELECT COUNT(*)
				FROM '.PERCH_DB_PREFIX.'shop_order_promotions op
				JOIN '.PERCH_DB_PREFIX.'shop_orders o ON op.orderID=o.orderID
				WHERE op.promoID='.$this->db->pdb((int)$this->id()).'
					AND o.orderStatus IN ('.$this->db->implode_for_sql_in($paid_statuses).')';

		if ($customerID!==null) {
			$sql .= ' AND op.customerID='.$this->db->pdb((int)$customerID);
		}

		return $this->db->get_count($sql);
	}

}
