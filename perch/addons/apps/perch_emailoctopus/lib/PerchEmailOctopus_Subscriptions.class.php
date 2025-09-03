<?php

class PerchEmailOctopus_Subscriptions extends PerchAPI_Factory
{
	protected $table               = 'emailoctopus_subscriptions';
    protected $pk                  = 'subID';
    protected $singular_classname  = 'PerchEmailOctopus_Subscription';

    protected $namespace           = 'subscription';

    protected $event_prefix        = 'emailoctopus.subscription';
	protected $master_template	   = 'emailoctopus/subscriptions/subscription.html';
	
	protected $default_sort_column = 'subCreated';
	protected $created_date_column = 'subCreated';


	public function create_from_import(PerchEmailOctopus_List $List, PerchEmailOctopus_Subscriber $Subscriber, array $member)
	{
		$Subscription = $this->find_subscription($List, $Subscriber);

		if (is_object($Subscription)) {
			$Subscription->update([
				'subStatus'	 => $member['status'],
				//'subRating'  => $member['member_rating'],
				'subUpdated' => ($member['last_updated_at'] ? date('Y-m-d H:i:s', strtotime($member['last_updated_at'])) : null),
				]);

		}else{
			$Subscription = $this->create([
				'subscriberID' => $Subscriber->id(),
				'listID'       => $List->id(),
				'subStatus'    => $member['status'],
				//'subRating'    => $member['member_rating'],
				'subCreated'   => ($member['created_at'] ? date('Y-m-d H:i:s', strtotime($member['created_at'])) : date('Y-m-d H:i:s')),
				'subUpdated'   => ($member['last_updated_at'] ? date('Y-m-d H:i:s', strtotime($member['last_updated_at'])) : null),
			]);
		}

		return $Subscription;		
	}

	public function find_subscription(PerchEmailOctopus_List $List, PerchEmailOctopus_Subscriber $Subscriber)
	{
		$sql = 'SELECT * FROM '.$this->table.' 
			WHERE listID='.$this->db->pdb((int)$List->id()).' AND subscriberID='.$this->db->pdb((int)$Subscriber->id());
		return $this->return_instance($this->db->get_row($sql));
	}

}
