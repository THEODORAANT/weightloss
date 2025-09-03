<?php

class PerchEmailOctopus_Webhook extends PerchAPI_Base
{
	protected $factory_classname = 'PerchEmailOctopus_Webhooks';
	protected $table             = 'mailchimp_webhooks';
	protected $pk                = 'webhookID';
}