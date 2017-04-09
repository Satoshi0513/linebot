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

    throw new Exception('Access Erorr gnavi',0);
  } catch (Exception $e){
    echo($e->getMessage());
  }
  //Web APIから取得したXMLデータをオブジェクトに代入
    $ret = json_decode($json);


  //Web APIのレスポンスがエラーか確認
try{
  if(isset($ret->error)){
  throw new Exception('Gnaviapi Error',(int) $ret->error->code);
}
} catch(Exception $e){
  echo('Gnaviapi Resopnce Error:'.$ret->error->message);
}


return $ret;

  }
}
