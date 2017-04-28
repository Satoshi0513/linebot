<?php
require_once(dirname(__FILE__)."/vendor/autoload.php");
var_dump('a');
require_once(dirname(__FILE__)."/googleapi.php");
var_dump('b');
require_once(dirname(__FILE__)."/gnaviapi.php");

putenv("GNAVI_API_KEY=3502416fd931ec2db08b7358234398c5");
putenv("GOOGLE_API_KEY=AIzaSyAIdcpEhhl7JvxZPH1J3QbbY3H803o6MJc");

$gnaviapi = new Gnaviapi(getenv('GNAVI_API_KEY'));
$json = $gnaviapi->get(36.1398987,139.3873951);

foreach($json->rest as $data){

var_dump($data->name);
var_dump($data->url_mobile);
}
