<?php
date_default_timezone_set("Asia/Dhaka");
ob_start();

require_once "config/app.php";
require_once "config/service_url.php";
require_once "config/database.php";
require_once "lib/database.php";
require_once "lib/CallExternalApi.php";
require_once "lib/Common.php";
require_once "../Model/NodeLog.php";

$fileName = "GENERATE_DYNAMIC_NEWS_LINK_" . (string)date("Y_m_d_A", time()) . ".txt";
$newsKey = $_REQUEST['newsKey'];
$isRegularContentPush = $_REQUEST['type'];

$postData = file_get_contents("php://input");
$postData = json_decode($postData, true);

$payload=isset($postData['carousel_value']) ? $postData['carousel_value'] : $_REQUEST['carousel_value'];
$messengerId = isset($postData['MESSENGER_ID']) ? $postData['MESSENGER_ID'] : $_REQUEST['MESSENGER_ID'];

$logTxt = json_encode($postData) . $logSeparator. json_encode($_REQUEST) . $logSeparator . json_encode($newsKey) . $logSeparator;

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
$url = null;

for ($i = 0; $i < count($data->data); $i++) {
    $payloadValue = $newsKey . "_" . $data->data[$i]->payload_value;
    
    if($payloadValue == $payload)
    {
        $url = $data->data[$i]->link_url;
        break;
    }
}

$logTxt .= json_encode($url) . $logSeparator;

$dbObj = new Database($database, $dbServer, $dbUserID, $dbPassword);
$connection = $dbObj->getConnection();

$nodeLog = new NodeLog($connection);

//new added
if(isset($isRegularContentPush) || $isRegularContentPush<>'')
$payload .=	"_" . $isRegularContentPush;


$nodeLog->messengerId = $messengerId;
$nodeLog->inputDate = date('Y-m-d H:i:s');
$nodeLog->nodeName = $payload;

$status = $nodeLog->create();

if($status)
{
    $returnValue = 'success';
} else {
    $returnValue = 'failed';
}

$commonObj = new Common();
$commonObj->logWrite($fileName, $logTxt);
$commonObj->redirect($url);

ob_end_flush();