<?php
date_default_timezone_set("Asia/Dhaka");

require_once "config/app.php";
require_once "config/database.php";
require_once "lib/database.php";
require_once "lib/Common.php";
require_once "../Model/NodeLog.php";

$fileName = "USER_LAST_INTERACTION_WITH_BOT" . (string)date("Y_m_d_A", time()) . ".txt";

$returnValue = "";
$postData = file_get_contents("php://input");
$postData = json_decode($postData, true);

$messengerId = !empty($postData['MESSENGER_ID']) ? $postData['MESSENGER_ID'] : $_GET['MESSENGER_ID'];
$nodeName = isset($postData['nodename']) ? $postData['nodename'] : $_REQUEST['nodename'];

$dbObj = new Database($database, $dbServer, $dbUserID, $dbPassword);
$connection = $dbObj->getConnection();

if (isset($nodeName))
{
    $nodeLog = new NodeLog($connection);

    $nodeLog->messengerId = $messengerId;
    $nodeLog->inputDate = date('Y-m-d H:i:s');
    $nodeLog->nodeName = $nodeName;

    $status = $nodeLog->create();

    if($status)
    {
        $returnValue = 'success';
    } else {
        $returnValue = 'failed';
    }
}

$dbObj->closeConnection();

$logTxt = json_encode($_REQUEST) . $logSeparator . $returnValue . $logSeparator."messngerID:". $messengerId .$logSeparator . json_encode($postData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$objCommon = new Common();
$objCommon->logWrite($fileName, $logTxt);

echo json_encode($returnValue);