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
  if (!($event instanceof\LINE\LINEBot\Event\MessageEvent)) {
    error_log('Non message event has come');
    continue;
  }
  if (!($event instanceof\LINE\LINEBot\Event\MessageEvent\TextMessage)) {
    error_log('Non text message has come');
    continue;
  }

  replyStickerMessage($bot,$event->getReplyToken(),1,1);
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
    error_log('Failed!'.$response->getHTTPStatus.''$response->getRawBody());
  }
}
?>
