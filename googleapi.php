<?php
class Googleapi {
	//Google Place API URI
	public function __construct($apikey){
	define('TEXTAPI','https://maps.googleapis.com/maps/api/place/textsearch/json?');
	define('NEARBYAPI','https://maps.googleapis.com/maps/api/place/nearbysearch/json?');
	define('DETAILAPI','https://maps.googleapis.com/maps/api/place/details/json?');
	define('PHOTOAPI','https://maps.googleapis.com/maps/api/place/photo?');
	//API Key
	$this->apikey = $apikey;
}
//Get data from text search API
public function textApi($usertext){
	$params = array();
	$key = "key=" . $this->apikey;
	$usertext = urlencode($usertext);
	$usertext = "query=" . $usertext;
	array_push($params,$usertext,$key);
	$uri = TEXTAPI . implode("&",$params);
	$json = $this->_get($uri);
	return $json;
}

//Get data from nearby search API
public function nearbyApi($lat,$lng){
	$params = array();
	$key = "key=" . $this->apikey;
	$location = "location=" . $lat . "," . $lng;
	$type = "types=cafe";
	$sort = "rankby=distance";
	array_push($params,$location,$type,$sort,$key);
	$uri = NEARBYAPI . implode("&",$params);
	//get JSON place data
	$json = $this->_get($uri);
	return $json;
}

//Get data from place detail API
public function detailApi($placeid){
	$params = array();
	$key = "key=" . $this->apikey;
	$id = "placeid=" . $placeid;
	array_push($params,$id);
	$uri = DETAILAPI . implode("&",$params);
	$json = $this->_get($uri);
	return $json;
}

//Get photo data from place photo API
public function photoApi($reference,$maxwidth){
	$params = array();
	$key = "key=" . $this->apikey;
	$photoref = "photoreference=" . $reference;
	$width = "maxwidth=" . $maxwidth;
	array_push($params,$photoref,$width,$key);
	$uri = PHOTOAPI . implode("&",$params);
	try{
	$photo = file_get_contents($uri);

	throw new Exception('Access error to google place photo',2);
} catch (Exception $e){
	error_log("Cannot get photo data");
}
	return $photo;
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
				throw new Exception ('Access Error google',1);
			}

			//Transform to JSON object
			$ret = json_decode($json);

			//Check API Request if failed
			if(isset($json->error_message)) {
				throw new Exception('GoogleApi Error:'.$json->error_message);
			}
			return $ret;
		}

}
