<?php
class Googleapi {
	//Web API URI
	public function __construct($apikey){
	define('TEXTAPI','https://maps.googleapis.com/maps/api/place/textsearch/json');
	define('NEARBYAPI','https://maps.googleapis.com/maps/api/place/nearbysearch/json');
	define('DETAILAPI','https://maps.googleapis.com/maps/api/place/details/json');
	define('PHOTOAPI','https://maps.googleapis.com/maps/api/place/photo');
	//API Key
	$this->apikey = $apikey;
}
//Build URI for Google Place API
public function textapiBuild($userevent){
	$userevent = urlencode($userevent);
	$params = array();
	$usertext = "query=" . $userevent;
	array_push($params,$usertext);
	$uri = $this->_build($params,TEXTAPI);
	return $uri;
}

public function nearbyapiBuild($lat,$lng){
	$params = array();
	$location = "location=" . $lat . "," . $lng;
	$type = "types=cafe";
	$sort = "rankby=distance";
	array_push($params,$location,$type,$sort);
	$uri = $this->_build($params,NEARBYAPI);
	return $uri;
}

public function detailapiBuild($placeid){
	$params = array();
	$id = "placeid=" . $placeid;
	array_push($params,$id);
	$uri = $this->_build($params,DETAILAPI);
	return $uri;
}

public function photoapiBuild($reference,$maxwidth){
	$params = array();
	$photoref = "photoreference=" . $reference;
	$width = "maxwidth=" . $maxwidth;
	array_push($params,$photoref,$width);
	$uri = $this->_build($params,PHOTOAPI);
	try{
	$photo = file_get_contents($uri);
	throw new Exception('Access error for google place photo',2);
} catch (Exception $e){
	error_log("Cannot get photo data");
}
	return $photo;
}


private function _build($params,$baseUri){
	if(is_array($params)){
	$params = implode("&",$params);
}
	$uri = $baseUri . '?key=' . $this->apikey . "&";
	$uri .= $params;
	return $uri;
 }

private function _setParams($params,$value){
	$param .= $value;
}

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
