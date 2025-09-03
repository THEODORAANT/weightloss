<?php

class PerchEmailOctopus_Subscribers extends PerchAPI_Factory
{
	protected $table               = 'emailoctopus_subscribers';
    protected $pk                  = 'subscriberID';
    protected $singular_classname  = 'PerchEmailOctopus_Subscriber';

    protected $namespace           = 'subscriber';

    protected $event_prefix        = 'emailoctopus.subscriber';
	protected $master_template	   = 'emailoctopus/subscribers/subscriber.html';
	
	protected $default_sort_column = 'subscriberCreated';
	protected $created_date_column = 'subscriberCreated';

	public function subscribe_from_form(PerchAPI_SubmittedForm $SubmittedForm)
	{

		$listIDs = [];

		$merge_vars = [];
        $interests  = [];
        $confirmed  = false;
        $status     = 'subscribed';

        $FormTag = $SubmittedForm->get_form_attributes();

        /*if ($FormTag->is_set('double_optin')) {
            if ($FormTag->double_optin()) {
            	$status = 'pending';
            }
        }*/

        $attr_map = $SubmittedForm->get_attribute_map('mailer');
        if (!empty($SubmittedForm->data['honeypot'])) {
            die('Spam detected!');
        }

        if (PerchUtil::count($attr_map)) {
            foreach($attr_map as $fieldID=>$merge_var) {
                switch($merge_var) {
                	case 'list':
                		if (isset($SubmittedForm->data[$fieldID])) {
                			$listIDs[] = $SubmittedForm->data[$fieldID];	
                		}
                		break;

                    case 'email':
                        $email = $SubmittedForm->data[$fieldID];
                        break;

                    case 'confirm_subscribe':
                        $confirmed = PerchUtil::bool_val($SubmittedForm->data[$fieldID]);
                        break;

                    case 'interests':
                    	$interests[$SubmittedForm->data[$fieldID]] = true;
                    	break;

                    default:
                        $merge_vars[$merge_var] = $SubmittedForm->data[$fieldID];
                        break;

                }
            }
        }

	    if (PerchUtil::count($listIDs)) {
         //  $matchtags=array("iow"=>"Isle of Wight Paid Client","sa"=>"Southampton","harlow"=>"Harlow");
	        $data = [];
			$data['email_address'] = $email;
			$data['status']        = $status;
			//$data['tags']= ["Isle of Wight Website Subscriber"];
			
			/*if (PerchUtil::count($merge_vars)) {
				$data['merge_fields']  = $merge_vars;
			}
			
			if (PerchUtil::count($interests)) {
				$data['interests']     = $interests;	
			}*/

			if ($confirmed) {
$Factory = new PerchEmailOctopus_Factory();
$opts=array();
$opts["url"]="/lists/cf65a0b0-30a7-11f0-b9e5-5dcf5dd9b607/contacts";
$opts["data"]=$data;
//print_r($opts);
$result = $Factory->curl_api($opts);
				/*$Lists = new PerchEmailOctopus_Lists($this->api);
				$perch_emailoctopusAPI = $this->get_api_instance();

				foreach($listIDs as $listperch_emailoctopusID) {
					$List = $Lists->get_one_by('listperch_emailoctopusID', $listperch_emailoctopusID);

					if (is_object($List)) {

						PerchUtil::debug('Subscribing to: '.$List->listTitle());

						$listID = $listperch_emailoctopusID;

						$result = $perch_emailoctopusAPI->post("lists/$listID/members", $data);

						if ($perch_emailoctopusAPI->success()) {
							
						}else{
							PerchUtil::debug($perch_emailoctopusAPI->getLastResponse());
						}

					}

				}*/
			}

			
		}

	}


	public function all_subscribed($Paging=false)
    {
    	$sort_val = null;
        $sort_dir = null;

        if ($Paging && $Paging->enabled()) {
            $sql = $Paging->select_sql();
            list($sort_val, $sort_dir) = $Paging->get_custom_sort_options();
        }else{
            $sql = 'SELECT';
        }

        $sql .= ' s1.* FROM '.$this->table.' s1 JOIN '.PERCH_DB_PREFIX.'emailoctopus_subscriptions s2 ON s1.subscriberID=s2.subscriberID ';

        $restrictions = $this->standard_restrictions();

        if ($restrictions!='') {
            $sql .= ' WHERE 1=1 '.$restrictions;
        }

        $sql .= ' AND s2.subStatus=\'subscribed\' ';

        if ($sort_val) {
            $sql .= ' ORDER BY '.$sort_val.' '.$sort_dir;
        } else if (isset($this->default_sort_column)) {
            $sql .= ' ORDER BY ' . $this->default_sort_column . ' '.$this->default_sort_direction;
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

	public function import(PerchEmailOctopus_List $List)
	{
		$Imports = new PerchEmailOctopus_Imports($this->api);

		$Import = $Imports->create([
						'importType' => 'subscribers',
						'importSourceID' => $List->id(),
						'importCount' => 100,
						'importOffset' => 0,
					]);

		if ($Import) {
			$Import->run();
		}
		
	}

	public function import_next(PerchEmailOctopus_Import $Import)
	{
		$Lists = new PerchEmailOctopus_Lists($this->api);
		$List = $Lists->find($Import->importSourceID());

		if (!is_object($List)) {
			return false;
		}

		$perch_emailoctopusAPI = new PerchEmailOctopus_Factory();
		$listID       = $List->listemailoctopusID();
 $opts["url"] ="/lists/$listID/contacts?limit=".$Import->importCount();//."&starting_after=".$Import->importOffset();

		$members = $perch_emailoctopusAPI->get_curl_api($opts);




		if ($members) {


			if (isset($members['data']) && PerchUtil::count($members['data'])) {
				
				$Subscriptions = new PerchEmailOctopus_Subscriptions($this->api);

				$all_members = $members['data'];


				foreach($all_members as $member) {
					
					$data = $this->map_fields($member);

					if (!$this->remote_subscriber_exists_locally($member['id'])) {
						//PerchUtil::debug('Importing subscriber: '.$member['email_address']);

						$Subscriber = $this->create($data);
					}else{
						// subscriber exists
						$Subscriber = $this->get_one_by('subscriberemailoctopusID', $member['id']);
						$Subscriber->update($data);
					}

					if ($Subscriber) {
						// create subscription
						$Subscriptions->create_from_import($List, $Subscriber, $member);
					}


				}


				// Return so that the cursor within the import is moved forward
				return [
					'result' => 'success',
					'count' => PerchUtil::count($members['data']),
					'message' => sprintf('Imported %d subscribers to list %s.', PerchUtil::count($members['data']), $List->listTitle())
				];
			}else{
				return [
					'result' => 'success',
					'count' => 0,
					'message' => sprintf('Imported all subscribers to list %s.', $List->listTitle()),
				];
			}
		}else{
			PerchUtil::debug("error response", 'error');
		}

		return false;
	}

	public function update(array $data)
	{


    		$Lists = new PerchEmailOctopus_Lists($this->api);
    		$List  = $Lists->get_one_by('listEmailOctopusID', $data['list_id']);

    		if (!is_object($List)) {
    			return false;
    		}

    		$Factory = new PerchEmailOctopus_Factory($this->api);

    		$listID       = $List->listEmailOctopusID();

		$hash 		  = $Factory->subscriberHash($data['email']);
        $opts["url"]= "/lists/$listID/contacts/$hash";
       // echo $opts["url"];
         $opts["data"]= $data;
		$update_sub = $Factory->curl_put_api($opts);

         return $update_sub;

		/*<?php

          $curl = curl_init();

          curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.emailoctopus.com/lists/00000000-0000-0000-0000-000000000000/contacts/631251b876fece73bc9dd647fe596d5f",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => "{\"email_address\":\"otto@example.com\",\"fields\":{\"referral\":\"Otto\",\"birthday\":\"2015-12-01\"},\"tags\":{\"vip\":true,\"tagToRemove\":false},\"status\":\"subscribed\"}",
            CURLOPT_HTTPHEADER => [
              "Authorization: Bearer REPLACE_BEARER_TOKEN",
              "content-type: application/json"
            ],
          ]);

          $response = curl_exec($curl);
          $err = curl_error($curl);

          curl_close($curl);

          if ($err) {
            echo "cURL Error #:" . $err;
          } else {
            echo $response;
          }*/
	}
	public function lookup_and_create(array $data)
	{
		if ($this->remote_subscriber_exists_locally($data['id'])) {
			return $this->get_one_by('subscriberEmailOctopusID', $data['id']);
		}

		$Lists = new PerchEmailOctopus_Lists($this->api);
		$List  = $Lists->get_one_by('listEmailOctopusID', $data['list_id']);

		if (!is_object($List)) {
			return false;
		}

		$Factory = new PerchEmailOctopus_Factory($this->api);

		$listID       = $List->listEmailOctopusID();
		//  CURLOPT_URL => "https://api.emailoctopus.com/lists/00000000-0000-0000-0000-000000000000/contacts/631251b876fece73bc9dd647fe596d5f",


		$hash 		  = $Factory->subscriberHash($data['email']);
        $opts["url"]= "/lists/$listID/contacts/$hash";
		$member = get_curl_api($opts);//->get("lists/$listID/members/$hash");



			$Subscriptions = new PerchEmailOctopus_Subscriptions($this->api);

			$sub_data = $this->map_fields($member);
			$sub_data['subscriberCreated'] = date('Y-m-d H:i:s');
			
			$Subscriber = $this->create($sub_data);
			
		if ($Subscriber) {
				// create subscription
				$Subscriptions->create_from_import($List, $Subscriber, $member);
			}


	}

	public function get_latest_for_list(PerchEmailOctopus_List $List)
    {
        $sql = 'SELECT * 
				FROM '.$this->table.' s 
				JOIN '.PERCH_DB_PREFIX.'emailoctopus_subscriptions subs ON s.subscriberID=subs.subscriberID
				WHERE
					subs.subStatus=\'subscribed\'
					AND subs.listID='.$this->db->pdb((int)$List->id()).'
					AND subs.subCreated IS NOT NULL
				ORDER BY subs.subCreated DESC
				LIMIT 3';
		return $this->return_instances($this->db->get_rows($sql));
    }

	private function remote_subscriber_exists_locally($subscriberEmailOctopusID)
	{
		$sql = 'SELECT COUNT(*) FROM '.$this->table.' WHERE subscriberEmailOctopusID='.$this->db->pdb($subscriberEmailOctopusID);
		if ($this->db->get_count($sql) > 0) {
			return true;
		}

		return false;
	}

	private function map_fields($member)
	{
	$FNAME="";
	if (array_key_exists("FNAME", $member['fields'])) {
	$FNAME=$member['fields']['FNAME'];
}
	$LNAME="";
	if (array_key_exists("LNAME", $member['fields'])) {
	$LNAME=$member['fields']['LNAME'];
	}

		return [
				'subscriberemailoctopusID' => $member['id'],
				'subscriberEmail'       => $member['email_address'],
				'subscriberEmailMD5'    => $member['id'],
				'subscriberFirstName'   =>$FNAME,
				'subscriberLastName'    => $LNAME,
				'subscriberCreated'     => ($member['created_at'] ? date('Y-m-d H:i:s', strtotime($member['created_at'])) : null),
				'subscriberUpdated'     => ($member['last_updated_at'] ? date('Y-m-d H:i:s', strtotime($member['last_updated_at'])) : null),
			];
	}
}
