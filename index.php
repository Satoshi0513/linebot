<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/gnaviapi.php';

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);

$signature = $_SERVER["HTTP_" . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
try {
  $events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);
} catch(\LINE\LINEBot\Exception\InvalidSignatureException $e) {
  error_log("parseEventRequest failed. InvalidSignatureException => ".var_export($e, true));
} catch(\LINE\LINEBot\Exception\UnknownEventTypeException $e) {
  error_log("parseEventRequest failed. UnknownEventTypeException => ".var_export($e, true));
} catch(\LINE\LINEBot\Exception\UnknownMessageTypeException $e) {
  error_log("parseEventRequest failed. UnknownMessageTypeException => ".var_export($e, true));
} catch(\LINE\LINEBot\Exception\InvalidEventRequestException $e) {
  error_log("parseEventRequest failed. InvalidEventRequestException => ".var_export($e, true));
}

foreach ($events as $event) {

  if (!($event instanceof \LINE\LINEBot\Event\MessageEvent)) {
    error_log('Non message event has come');
    continue;
  }
  if (!($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage ||$event instanceof \LINE\LINEBot\Event\MessageEvent\LocationMessage)) {
    error_log('Non text message has come');
    continue;
  }
//check if usertext come
  if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {
    $api = new Gnaviapi(getenv('GNAVI_API_KEY'));
    $json = $api->restTextSearch($event->getText());
  }
//check if location message come
  if ($event instanceof \LINE\LINEBot\Event\MessageEvent\LocationMessage) {
    $api = new Gnaviapi(getenv('GNAVI_API_KEY'));
    $json = $api->restLocationSearch($event->getLatitude(),$event->getLongitude());
  }

 if (isset($json->rest)) {
$bot->replyText($event->getReplyToken(), $json);
  // $columnArray = array();
  //
  //   foreach ($json->rest as $rest) {
  //     if ($i >4){
  //       break;
  //     }
  //     $actionArray = array();
  //     $mapUri = "https://www.google.co.jp/maps/place/" . urlencode($rest->address);//generate URI for searching shop location on Google map //
  //
  //     array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder (
  //       "Webサイト", $rest->url));
  //       array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder (
  //       "地図", $mapUri));
  //
  //     $column = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder (
  //       ($i + 1) . "番目に近いカフェ",
  //       $rest->name,
  //       "https://" . $_SERVER["HTTP_HOST"] .  "/imgs/cafe.jpg",
  //       $actionArray
  //     );
  //     array_push($columnArray, $column);
  //     $i += 1;
  //   }
  //   replyCarouselTemplate($bot, $event->getReplyToken(),"近くのカフェ", $columnArray);
  }else{
    $bot->replyText($event->getReplyToken(), "うまく探せませんでした。。。1km圏内にカフェはないかもしれません。");
  }

  // foreach($json->rest as $rest) {
  //   if ($i > 2){
  //     break;
  //   }
  //   $actionArray = array();
  //   $mapUri = "https://www.google.co.jp/maps/place/" . $rest->address;//generate URI for searching shop location on Google map //
  //   //　set shop image if exists;
  //   if(isset($rest->image_url->shop_image1)) {
  //     $photo = $rest->image_url->shop_image1;
  //   } elseif(isset($rest->image_url->shop_image2)) {
  //     $photo = $rest->image_url->shop_image2;
  //   } else{
  //     $photo = __DIR__ . "/imgs/cafe.jpg";
  //   }
  //
  //   //prepare for creating carousel
  //   array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder (
  //     "Webサイト", $rest->url));
  //   array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder (
  //     "地図", $mapUri));
  //   // array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
  //   //   "営業時間", "c-" . $i . "-" . 3));
  //   $column = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder (
  //     ($i + 1) . "番目に近いカフェ",
  //     $rest->name,
  //     $photo,
  //     $actionArray
  //   );
  //   array_push($columnArray, $column);
  //   $i += 1;
  // }
  // replyCarouselTemplate($bot, $event->getReplyToken(),"近くのカフェ", $columnArray);

}


//function to generate message template
function replyTextMessage($bot, $replyToken, $text) {
  $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text));
  if (!$response->isSucceeded()) {
    error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

function replyImageMessage($bot, $replyToken, $originalImageUrl, $previewImageUrl) {
  $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($originalImageUrl, $previewImageUrl));
  if (!$response->isSucceeded()) {
    error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

function replyLocationMessage($bot, $replyToken, $title, $address, $lat, $lon) {
  $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\LocationMessageBuilder($title, $address, $lat, $lon));
  if (!$response->isSucceeded()) {
    error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

function replyStickerMessage($bot, $replyToken, $packageId, $stickerId) {
  $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder($packageId, $stickerId));
  if (!$response->isSucceeded()) {
    error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

function replyMultiMessage($bot, $replyToken, ...$msgs) {
  $builder = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
  foreach($msgs as $value) {
    $builder->add($value);
  }
  $response = $bot->replyMessage($replyToken, $builder);
  if (!$response->isSucceeded()) {
    error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

function replyButtonsTemplate($bot, $replyToken, $alternativeText, $imageUrl, $title, $text, ...$actions) {
  $actionArray = array();
  foreach($actions as $value) {
    array_push($actionArray, $value);
  }
  $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
    $alternativeText,
    new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder ($title, $text, $imageUrl, $actionArray)
  );
  $response = $bot->replyMessage($replyToken, $builder);
  if (!$response->isSucceeded()) {
    error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

function replyConfirmTemplate($bot, $replyToken, $alternativeText, $text, ...$actions) {
  $actionArray = array();
  foreach($actions as $value) {
    array_push($actionArray, $value);
  }
  $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
    $alternativeText,
    new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder ($text, $actionArray)
  );
  $response = $bot->replyMessage($replyToken, $builder);
  if (!$response->isSucceeded()) {
    error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

function replyCarouselTemplate($bot, $replyToken, $alternativeText, $columnArray) {
  $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
  $alternativeText,
  new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder (
   $columnArray)
  );
  $response = $bot->replyMessage($replyToken, $builder);
  if (!$response->isSucceeded()) {
    error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

function generateImage($data,$name){
  file_put_contents(__DIR__ . '/imgs/' . $name .'.jpg',$data);
}

 ?>
