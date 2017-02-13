<?php
class Googleapi {
	//Web API URI
	 const APIURI = 'https://maps.googleapis.com/maps/api/place/textsearch/json';

	//API Key
	private $apikey = getenv('API_KEY');


//Build URI for Google Place API
public function build($userMessage){
	$uri = self::APIURI.'?query=' . $userMessage . '&key=' . $this->$apikey .
	 '&radius=1000&language=ja';

	 return $uri;
}


//Get JSON data
public function get($uri) {
			// Get Request limited for 5 seconds
			$opts = array('http'=>
									array('timeout'=>5)
									);
			$context = stream_context_create($opts);
			//Send request to Google Place API
			$json = file_get_contents($uri,0,$context);
			//Check request if failed
			if ($json===FALSE) {
				throw new Exception ('Access Error',0);
			}

			//Transform to JSON object
			$ret = json_decode($json);

			//Check API Request if failed
			if(isset($json->Error)) {
				throw new Exception('YahooApi Error:'.$json->error_message);
			}
			return $ret;
		}

}
