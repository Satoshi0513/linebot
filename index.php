<?php
require_once(dirname(__FILE__)."/vendor/autoload.php");
require_once(dirname(__FILE__)."/googleapi.php");
require_once(dirname(__FILE__)."/gnaviapi.php");

$httpClient = new
\LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));
$bot = new\LINE\LINEBot($httpClient,['channelSecret' => getenv('CHANNEL_SECRET')]);

$signature = $_SERVER["HTTP_".\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
try{
  $events = $bot->parseEventRequest(file_get_contents('php://input'),$signature);
} catch(\LINE\LINEBot\Exception\InvalidSignatureException $e) {
  error_log("parseEventRequest failed.InvalidSignatureException => ".var_export($e,true));
} catch(\LINE\LINEBot\Exception\UnknownEventTypeException $e) {
  error_log("parseEventRequest failed.UnknownEventTypeException => ".var_export($e,true));
} catch(\LINE\LINEBot\Exception\UnknownMessageTypeException $e) {
  error_log("parseEventRequest failed.UnknownMessageTypeException => ".var_export($e,true));
} catch(\LINE\LINEBot\Exception\InvalidEventRequestException $e) {
  error_log("parseEventRequest failed.InvalidEventRequestException => ".var_export($e,true));
}
foreach ($events as $event) {
  if ($event instanceof\LINE\LINEBot\Event\PostbackEvent) {
    replyTextMessage($bot,$event->getReplyToken(),"Postback受信「".$event->
    getPostbackData()."」");
    continue;
  }

  if (!($event instanceof\LINE\LINEBot\Event\MessageEvent)) {
    error_log('Non message event has come');
    continue;
  }
  if (!($event instanceof\LINE\LINEBot\Event\MessageEvent\TextMessage)) {
    error_log('Non text message has come');
    continue;
  }

if ($event instanceof\LINE\LINEBot\Event\MessageEvent\LocationMessage){
  $gnaviapi = new Gnaviapi;
  $json = $gnaviapi->get($event->getLatitude(),$event->getLongitude());
  continue;
}

if ($event instanceof\LINE\LINEBot\Event\MessageEvent\TextMessage){
  $googleapi = new Googleapi;
  $latlon = $googleapi->build($event->getText());
  $gnaviapi = new Gnaviapi;
  $json = $gnaviapi->get($latlon[0],$latlon[1]);
  continue;
}
}


$columnArray = array();
$i = 0;
  foreach($json->rest as $rest) {
      $actionArray = array();
      array_push($actionArray,new
    LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder(
      "店舗URL",$rest->url_mobile));
      array_push($actionArray,new
    LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder(
      "営業時間" ,$i . "-opentime-holiday"));
      array_push($actionArray,new
    LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder(
      "地図URL",$i . "-latitude-longitude"));
      $column = new
      \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder(
      ($i+1)."番目に近い店舗",
      $rest->name,
      $rest->imageurl->shop_image1,
      $actionArray
    );
    array_push($columnArray,$column);
    $i += 1;
  }

replyCarouselTemplate($bot,$event->getReplyToken(),"今後の天気予報",$columnArray);

//Function for generating replyMessage

function replyTextMessage($bot,$replyToken,$text) {
  $response = $bot->replyMessage($replyToken,new
  \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text));
  if(!$response->isSucceeded()) {
    error_log('Failed!'.$response->getHTTPStatus.''.$response->getRawBody());
  }
}

function replyImageMessage($bot,$replyToken,$originalImageUrl,$previewImageUrl) {
  $response = $bot->replyMessage($replyToken,new
  \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($originalImageUrl,$previewImageUrl));
  if (!$response->isSucceeded()) {
    error_log('Failed!'.$response->getHTTPStatus.''.$response->getRawBody());
  }
}

function replyLocationMessage($bot,$replyToken,$title,$address,$lat,$lon) {
  $response = $bot->replyMessage($replyToken,new
  \LINE\LINEBot\MessageBuilder\LocationMessageBuilder($title,$address,$lat,$lon));
  if (!$response->isSucceeded()) {
    error_log('Failed!'.$response->getHTTPStatus.''.$response->getRawBody());
  }
}

function replyStickerMessage($bot,$replyToken,$packageId,$stickerId) {
  $response = $bot->replyMessage($replyToken,new
  \LINE\LINEBot\MessageBuilder\StickerMessageBuilder($packageId,$stickerId));
  if (!$response->isSucceeded()) {
    error_log('Failed!'.$response->getHTTPStatus.''.$response->getRawBody());
  }
}

function replyMultiMessage($bot,$replyToken,...$msgs) {
  $builder = new\LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
  foreach($msgs as $value) {
    $builder->add($value);
  }
  $response = $bot->replyMessage($replyToken,$builder);
  if (!$response->isSucceeded()) {
    error_log('Failed'.$response->getHTTPStatus.''.$response->getRawBody());
  }
}

function replyButtonsTemplate($bot,$replyToken,$alternativeText,$imageUrl,
$title,$text,...$actions) {
  $actionArray = array();
  foreach($actions as $value) {
    array_push($actionArray,$value);
  }
  $builder = new\LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
  $alternativeText,
  new\LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder($title,
  $text,$imageUrl,$actionArray)
);
  $response = $bot->replyMessage($replyToken,$builder);
  if(!$response->isSucceeded()) {
  error_log('Failed!'.$response->getHTTPStatus.''.$response->getRawBody());
  }
}

function replyConfirmTemplate($bot,$replyToken,$alternativeText,$text,...$actions) {
  $actionArray = array();
  foreach($actions as $value) {
    array_push($actionArray,$value);
  }
  $builder = new\LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
    $alternativeText,
    new\LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder($text,
    $actionArray)
  );
  $response = $bot->replyMessage($replyToken,$builder);
  if (!$response->isSucceeded()) {
  error_log('Failed!'.$response->getHTTPStatus.''.$response->getRawBody());
  }
}

function replyCarouselTemplate($bot,$replyToken,$alternativeText,$columnArray) {
  $builder = new\LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
  $alternativeText,
  new\LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder(
  $columnArray)
  );
  $response = $bot->replyMessage($replyToken,$builder);
  if(!$response->isSucceeded()) {
    error_log('Failed!'.$response->getHTTPStatus.''.$response->getRawBody());
  }
}

?>
