<?php

class PerchEmailOctopus_Subscription extends PerchAPI_Base
{
	protected $factory_classname = 'PerchEmailOctopus_Subscriptions';
	protected $table             = 'emailoctopus_subscriptions';
	protected $pk                = 'subID';

	protected $modified_date_column = 'subUpdated';
}
