<?php
class Gnaviapi {

  public function __construct(){
  define('APIURI','https://api.gnavi.co.jp/RestSearchAPI/20150630/?format=json');
  $this->apikey = getenv('GNAVI_API_KEY');
}

  private function _build($lat,$lng) {
    $lat = floatval($lat);
    $lng = floatval($lng);
    $uri = APIURI . '&keyid=' . $this->apikey . '&latitude=' . $lat . '&longitude=' . $lng .
    '&range=3&category_s=RSFST18001';
    return $uri;
  }

  public function get($lat,$lng) {
  $opts = array('http'=>
  						array('timeout'=>5)
  						);
  $context = stream_context_create($opts);

    $json = file_get_contents($this->_build($lat,$lng),0,$context);

    if ($json===FALSE) {
      throw new Exception ('Access Error gnavi',0);
    }

  //Web APIから取得したXMLデータをオブジェクトに代入
    $ret = json_decode($json);


  //Web APIのレスポンスがエラーか確認
  if(isset($json->error)) {
    throw new Exception('Gnaciapi Error:'.$json->error->error_message);
  }

return $ret;

  }
}
