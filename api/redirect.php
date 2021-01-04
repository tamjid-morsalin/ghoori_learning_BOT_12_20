<?php
date_default_timezone_set("Asia/Dhaka");
ob_start();

require_once "config/app.php";
require_once "config/service_url.php";
require_once "config/database.php";
require_once "lib/database.php";
require_once "lib/Common.php";
require_once "../Model/NodeLog.php";

$fileName = "REDIRECT_URL_" . (string)date("Y_m_d_A", time()) . ".txt";
$redirectType = $_REQUEST['redirectType'];

$postData = file_get_contents("php://input");
$postData = json_decode($postData, true);

$messengerId = isset($postData['MESSENGER_ID']) ? $postData['MESSENGER_ID'] : $_REQUEST['MESSENGER_ID'];

$logTxt = json_encode($postData) . $logSeparator. json_encode($_REQUEST) . $logSeparator . json_encode($redirectType) . $logSeparator;

if(is_null($redirectType))
{
    $res = [
        'status' => 'failed',
        'message' => 'No redirect type found'
    ];

    $logTxt .= json_encode($res) . $logSeparator;

    http_response_code(422);
    echo json_encode($res);
    exit;
}

$redirectUrl = $redirectUrl[$redirectType];

$logTxt .= json_encode($redirectUrl) . $logSeparator;

$dbObj = new Database($database, $dbServer, $dbUserID, $dbPassword);
$connection = $dbObj->getConnection();

$nodeLog = new NodeLog($connection);

$nodeLog->messengerId = $messengerId;
$nodeLog->inputDate = date('Y-m-d H:i:s');
$nodeLog->nodeName = $redirectType;

$status = $nodeLog->create();

if($status)
{
    $returnValue = 'success';
} else {
    $returnValue = 'failed';
}

$commonObj = new Common();
$commonObj->logWrite($fileName, $logTxt);
$commonObj->redirect($redirectUrl);

ob_end_flush();