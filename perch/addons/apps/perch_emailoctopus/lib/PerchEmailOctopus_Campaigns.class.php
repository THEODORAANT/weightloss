<?php

class PerchEmailOctopus_Campaigns extends PerchAPI_Factory
{
	protected $table               = 'emailoctopus_campaigns';
    protected $pk                  = 'campaignID';
    protected $singular_classname  = 'PerchEmailOctopus_Campaign';

    protected $namespace           = 'emailoctopus';

    protected $event_prefix        = 'emailoctopus.campaign';
	protected $master_template	   = 'emailoctopus/campaigns/campaign.html';
	
	protected $default_sort_column = 'campaignCreated';
	protected $default_sort_direction = 'DESC';
	protected $created_date_column = 'campaignCreated';


	public function get_custom($opts)
	{
		$opts['template'] = 'emailoctopus/'.$opts['template'];
		
		return $this->get_filtered_listing($opts, function(PerchQuery $Query) use ($opts) {

			$DB = $this->api->get('DB');

			$Query->from .= ', '.PERCH_DB_PREFIX.'emailoctopus_lists l ';
			$Query->where[] = 'main.listID=l.listID';
			$Query->where[] = 'l.listPublic=1';

			if ($opts['list']) {
				$Query->where[] = 'l.listEmailOctopusID='.$DB->pdb($opts['list']);
			}

			return $Query;
		});
	}

	public function import()
	{
		$Imports = new PerchEmailOctopus_Imports($this->api);

		$Import = $Imports->create([
						'importType'     => 'campaigns',
						'importSourceID' => null,
						'importCount'    => 20,
						'importOffset'   => 0,
					]);

		if ($Import) {
			$Import->run();
		}
		
	}

	public function import_next(PerchEmailOctopus_Import $Import)
	{

      $Factory = new PerchEmailOctopus_Factory();
                      $opts=array();
                      $opts["url"]="/campaigns?limit=100";


		$campaigns = $Factory->get_curl_api($opts);

		if ($campaigns) {

			if (isset($campaigns['data']) && PerchUtil::count($campaigns['data'])) {
				
				$Subscriptions = new PerchEmailOctopus_Subscriptions($this->api);
				$Lists = new PerchEmailOctopus_Lists($this->api);

				$all_campaigns = $campaigns['data'];

				foreach($all_campaigns as $campaign) {
					
					$data = $this->map_fields($campaign);

					$List = $Lists->get_one_by('listEmailOctopusID', $campaign['recipients']['list_id']);
					if ($List) {
						$data['listID'] = $List->id();	
					}
					

					if (!$this->remote_campaign_exists_locally($campaign['id'])) {
						$Campaign = $this->create($data);
					}else{
						// Campaign exists
						$Campaign = $this->get_one_by('campaignEmailOctopusID', $campaign['id']);
						$Campaign->update($data);
					}

					if ($Campaign) {
						$Campaign->import_content($MailChimpAPI);
					}

				}

				// Return so that the cursor within the import is moved forward
				return [
					'result' => 'success',
					'count' => PerchUtil::count($campaigns['campaigns']),
					'message' => sprintf('Imported %d campains', PerchUtil::count($campaigns['campaigns']))
				];
			}else{
				return [
					'result' => 'success',
					'count' => 0,
					'message' => sprintf('Imported all campaigns.'),
				];
			}
		}else{
			PerchUtil::debug("no response", 'error');
		}

		return false;
	}

	public function import_one($campaignEmailOctopusID)
	{
		/*$MailChimpAPI = $this->get_api_instance();

		$campaign = $MailChimpAPI->get("campaigns/$campaignEmailOctopusID");

		if ($MailChimpAPI->success()) {

			$data = $this->map_fields($campaign);

			$Lists = new PerchEmailOctopus_Lists($this->api);
			$List = $Lists->get_one_by('listEmailOctopusID', $campaign['recipients']['list_id']);
			if ($List) {
				$data['listID'] = $List->id();	
			}

			if (!$this->remote_campaign_exists_locally($campaign['id'])) {
				$Campaign = $this->create($data);
			}else{
				// Campaign exists
				$Campaign = $this->get_one_by('campaignEmailOctopusID', $campaign['id']);
				$Compaign->update($data);
			}

			if ($Campaign) {
				$Campaign->import_content($MailChimpAPI);
			}
			
		}else{
			PerchUtil::debug($MailChimpAPI->getLastResponse(), 'error');	
		}*/

		return false;
	}

	private function remote_campaign_exists_locally($campaignEmailOctopusID)
	{
		$sql = 'SELECT COUNT(*) FROM '.$this->table.' WHERE campaignEmailOctopusID='.$this->db->pdb($campaignEmailOctopusID);
		if ($this->db->get_count($sql) > 0) {
			return true;
		}

		return false;
	}

	private function map_fields($campaign)
	{
		return [
				'campaignEmailOctopusID' => $campaign['id'],
				'campaignSendTime'    => ($campaign['sent_at'] ? date('Y-m-d H:i:s', strtotime($campaign['sent_at'])) : null),
				//'campaignArchiveURL'  => $campaign['archive_url'],
				'campaignStatus'      => $campaign['status'],
				'campaignEmailsSent'  => $campaign['emails_sent'],
				'campaignSubject'     => $campaign['subject'],
				'campaignTitle'       => $campaign['name'],
				'campaignSlug'        => PerchUtil::urlify($campaign['name']),
				'campaignCreated'     => ($campaign['created_at'] ? date('Y-m-d H:i:s', strtotime($campaign['created_at'])) : null),
			];
	}
}
