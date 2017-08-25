<?php
class Gnaviapi {
	//GURUNAVI Restaurant search API URI
	public function __construct($apikey){
	define('RestSearchUri','https://api.gnavi.co.jp/RestSearchAPI/20150630/');
	//API Key
	$this->apikey = $apikey;
}

public function restTextSearch($userText){
	$userText = urlencode($userText);
	$uri = RestSearchUri . "?keyid=" . $this->apikey . "&format=json&category_s=RSFST18001&freeword=" . $userText;
	$json = $this->_get($uri);
	return $json;
}

public function restLocationSearch($lat,$lon){
	$uri = RestSearchUri . "?keyid=" . $this->apikey ."&format=json&category_s=RSFST18001&latitude=" . $lat . "&longitude=" . $lon ."&range=3";
	$json = $this->_get($uri);
	return $json;
}


// private function _build($params,$baseUri){
// 	if(is_array($params)){
// 	$params = implode("&",$params);
// }
// 	$uri = $baseUri . '?key=' . $this->apikey . "&";
// 	$uri .= $params;
// 	return $uri;
// }

// private function _setParams($params,$value){
// 	$param .= $value;
// }

// private function _build(...$params){
// 	if(func_num_args()==1){
// 	$params = urlencode($params);
// 	$uri = TEXTAPI .'?query=' . $params . '&key=' . $this->apikey . '&radius=1000&types=cafe';
// } elseif(func_num_args()==2){
// 	$lat = floatval($params[0]);
// 	$lng = floatval($params[1]);
// 	$uri = NEARBYAPI .'?location=' . $lat . ',' . $lng . '&radius=500&type=cafe&key=' . $this->apikey;
// }
// 	return $uri;
//  }


//Get JSON data
private function _get($uri) {

			// Get Request limited for 5 seconds
			$opts = array('http'=>
									array('timeout'=>5)
									);
			$context = stream_context_create($opts);
			//Send request to Google Place API
			$json = file_get_contents($uri,0,$context);
			//Check request if failed
			if ($json===FALSE) {
				throw new Exception ('Access Error',1);
			}

			//Transform to JSON object
			$ret = json_decode($json);

			//Check API Request if failed
			if(isset($json->error_message)) {
				throw new Exception('Api Error:'.$json->error_message);
			}
			return $ret;
		}

}
