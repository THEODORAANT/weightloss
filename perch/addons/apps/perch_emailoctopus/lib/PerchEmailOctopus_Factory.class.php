<?php


class PerchEmailOctopus_Factory //extends PerchAPI_Factory
{ private $api_instance = null;
	private $octopus_api_key = null;
private $octopus_api_url = null;


	protected function get_api_instance()
	{

		$Settings  = PerchSettings::fetch();
		$api_key   = $Settings->get('perch_emailoctopus_api_key')->val();

		 $this->octopus_api_url = 'https://api.emailoctopus.com'; // Replace with the actual Octopus API endpoint
        $this->octopus_api_key = $api_key ; // Replace with your Octopus API key


		return false;
	}
 /**
     * Convert an email address into a 'subscriber hash' for identifying the subscriber in a method URL
     * @param   string $email The subscriber's email address
     * @return  string          Hashed version of the input
     */
    public function subscriberHash($email)
    {
        return md5(strtolower($email));
    }
	public function  get_curl_api($opts){


    // Initialize cURL
    $this->get_api_instance();
    $curl = curl_init();
    $octopus_api_url=$this->octopus_api_url.$opts["url"];

  // Initialize cURL session
  $ch = curl_init();

  // Set cURL options
  curl_setopt($ch, CURLOPT_URL, $octopus_api_url);  // Set the URL
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Return the response as a string
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
      "Authorization: Bearer $this->octopus_api_key"  // Add the Authorization header
  ]);

    $response = curl_exec($ch);
    // echo "response"; print_r($response);
    $err = curl_error($ch);

    curl_close($ch);

    if ($err) {
      echo "cURL Error #:" . $err;
      return false;
    } else {
    $data = json_decode($response, true);

    // Check if JSON was decoded successfully
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "JSON decode error: " . json_last_error_msg();
       return false;
    }
      return  $data;
    }


	}
public function curl_put_api($opts)
	{
	// Initialize cURL
    $this->get_api_instance();

    $ch = curl_init();
     $octopus_api_url=$this->octopus_api_url.$opts["url"];
        $fields=$opts["data"];
        unset($fields["email"]);
        $send_data["email_address"]=$opts["data"]["email"];
        $send_data["fields"]=$fields;

    curl_setopt($ch, CURLOPT_URL, $octopus_api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

 // curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"email_address\":\"otto@example.com\",\"fields\":{\"referral\":\"Otto\",\"birthday\":\"2015-12-01\"},\"tags\":{\"vip\":true,\"tagToRemove\":false},\"status\":\"subscribed\"}",

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($send_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $this->octopus_api_key
    ]);
   // echo "data"; print_r($send_data);
    $err = curl_error($ch);
   // echo "curl_error"; print_r($err);

    $response = curl_exec($ch);
  //  echo "response"; print_r($response);
    curl_close($ch);

    // Handle the response from Octopus API (if needed)
    if (!$err) {
       return true;
    } else {
     die("Error occurred");
     exit;

    }
	}
public function curl_api($opts)
	{

// Initialize cURL
$this->get_api_instance();

$ch = curl_init();
 $octopus_api_url=$this->octopus_api_url.$opts["url"];

curl_setopt($ch, CURLOPT_URL, $octopus_api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
 // curl_setopt($ch,CURLOPT_POSTFIELDS , "{\"email_address\":\"otto@example.com\",\"status\":\"subscribed\"}");

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($opts["data"]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $this->octopus_api_key
]);
//echo "data"; print_r($opts["data"]);
$err = curl_error($ch);
//echo "curl_error"; print_r($err);
$response = curl_exec($ch);
//echo "response"; print_r($response);
curl_close($ch);

// Handle the response from Octopus API (if needed)
if (!$err) {
   return true;
} else {
 return false;
}
}
	/*public function get_custom($opts)
	{
		$opts['template'] = 'emailoctopus/'.$opts['template'];
		
		return $this->get_filtered_listing($opts);
	}*/

}
