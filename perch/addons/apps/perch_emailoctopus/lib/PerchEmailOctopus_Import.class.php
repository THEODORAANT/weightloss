<?php

class PerchEmailOctopus_Import extends PerchAPI_Base
{
	protected $factory_classname = 'PerchEmailOctopus_Imports';
	protected $table             = 'emailoctopus_imports';
	protected $pk                = 'importID';

	protected $modified_date_column = 'importUpdated';


	public function run()
	{
		$result = false;

		switch($this->importType()) {

			case 'subscribers':
				$Subscribers = new PerchEmailOctopus_Subscribers($this->api);
				$result = $Subscribers->import_next($this);
				break;

			case 'campaigns':
				$Campaigns = new PerchEmailOctopus_Campaigns($this->api);
				$result = $Campaigns->import_next($this);
				break;

			case 'webhooks':
				$Webhooks = new PerchEmailOctopus_Webhooks($this->api);
				$result = $Webhooks->import_next($this);
				break;

		}

		if ($result && $result['result']=='success') {

			if ($result['count'] < $this->importCount()) {
				// looks like that's all the pages, so self-delete.
				PerchUtil::debug('Result count is '.$result['count']);
				PerchUtil::debug('Import count is '.$this->importCount());
				PerchUtil::debug('Deleting.');
				$this->delete();
				return;
			}

			$this->update([
				'importOffset' => $this->importOffset()+$this->importCount()
				]);

			return $result;
		}

		return $result;
	}





}
