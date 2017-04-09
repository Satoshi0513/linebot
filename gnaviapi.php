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
  //Web APIへリクエスト送信
  try{
    $json = file_get_contents($this->_build($lat,$lng),0,$context);

    throw new Exception('Access Erorr',0);
  } catch (Exception $e){
    error_log($e->getMessage());
  }
  //Web APIから取得したXMLデータをオブジェクトに代入
    $ret = json_decode($json);


  //Web APIのレスポンスがエラーか確認
try{
  if(isset($ret->error)){
  throw new Exception('Gnaviapi Error',(int) $ret->error->code);
}
} catch(Exception $e){
  error_log('Gnaviapi Resopnce Error:'.$ret->error->message);
}


return $ret;

  }
}

putenv("GNAVI_API_KEY=3502416fd931ec2db08b7358234398c5");
$api = new Gnaviapi;
$json = $api->get(36.253464,139.149407);
var_dump($json);
