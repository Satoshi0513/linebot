<?php
class Googleapi {
	//Web API URI
	public function __construct(){
	define('TEXTAPI','https://maps.googleapis.com/maps/api/place/textsearch/json');
	//API Key
	$this->apikey = getenv('GOOGLE_API_KEY');
}
//Build URI for Google Place API
private function _build($usermessage){
	$latlng = array();
	$usermessage = urlencode($usermessage);
	$uri = TEXTAPI .'?query=' . $usermessage . '&key=' . $this->apikey;

	return $uri;
 }


//Get JSON data
public function get($usermessage) {

			// Get Request limited for 5 seconds
			$opts = array('http'=>
									array('timeout'=>5)
									);
			$context = stream_context_create($opts);
			//Send request to Google Place API
			$json = file_get_contents($this->_build($usermessage),0,$context);
			//Check request if failed
			if ($json===FALSE) {
				throw new Exception ('Access Error google',1);
			}

			//Transform to JSON object
			$ret = json_decode($json);

			//Check API Request if failed
			if(isset($json->error_message)) {
				throw new Exception('GoogleApi Error:'.$json->error_message);
			}

			$latlng = array();
			array_push($latlng,$ret->results[0]->geometry->location->lat);
			array_push($latlng,$ret->results[0]->geometry->location->lng);

			return $latlng;
		}

}
