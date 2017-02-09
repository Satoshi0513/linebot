<?php
require_once __DIR__ .'/vendor/autoload.php';

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

replyConfirmTemplate(
  $bot,
  $event->getReplyToken(),
  "Webで詳しくみますか？",
  "Webで詳しくみますか？",
  new\LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder(
  "見る","http://google.jp"),
  new\LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder(
  "見ない","ignore"),
  new\LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder(
  "非表示","never")
);
}


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
?>
