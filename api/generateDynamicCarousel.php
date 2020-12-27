<?php
date_default_timezone_set("Asia/Dhaka");
ob_start();

require_once "config/app.php";
require_once "config/service_url.php";
require_once "lib/CallExternalApi.php";
require_once "lib/Common.php";

$fileName = "GENERATE_DYNAMIC_CAROUSEL_" . (string)date("Y_m_d_A", time()) . ".txt";
$newsKey = $_REQUEST['newsKey'];

$logTxt = json_encode($newsKey) . $logSeparator;

if(is_null($newsKey))
{
    $res = [
        'status' => 'failed',
        'message' => 'No news key found'
    ];

    $logTxt .= json_encode($res) . $logSeparator;

    http_response_code(422);
    echo json_encode($res);
    exit;
}

$url = $dynamicNewsArr[$newsKey];

$logTxt .= json_encode($url) . $logSeparator;

$externalApiObj = new CallExternalApi();

$data = json_decode($externalApiObj->callAPI('GET', $url));

if($data->code != '200')
{
    $res = [
        'status' => 'failed',
        'message' => 'Error occured'
    ];

    $logTxt .= json_encode($res) . $logSeparator;

    http_response_code($data->code);
    echo json_encode($res);
    exit;
}


if(count($data->data) == 0)
{
    $res = [
        'status' => 'success',
        'message' => 'No data found'
    ];

    $logTxt .= json_encode($res) . $logSeparator;

    http_response_code(404);
    echo json_encode($res);
    exit;
}

$responseArray = [];

for ($i = 0; $i < count($data->data); $i++) {
    $responseArray[$i]['payload_value'] = $newsKey . "_" . $data->data[$i]->payload_value;
    $responseArray[$i]['title'] = trim($data->data[$i]->title);
    $responseArray[$i]['image_url'] = $data->data[$i]->image_url;
    $responseArray[$i]['subtitle'] = trim($data->data[$i]->subtitle);
}

echo $result = json_encode($responseArray, JSON_UNESCAPED_SLASHES);
$logTxt .= json_encode($result) . $logSeparator;

$objCommon = new Common();
$objCommon->logWrite($fileName, $logTxt);

ob_end_flush();