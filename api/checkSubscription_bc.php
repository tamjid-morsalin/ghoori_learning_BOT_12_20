<?php
date_default_timezone_set("Asia/Dhaka");

ob_start();

require_once "config/app.php";
require_once "config/database.php";
require_once "lib/database.php";
require_once "lib/Common.php";
require_once "../Model/Subscription.php";

$fileName = "CHECK_SUBSCRIPTION_" . (string)date("Y_m_d_A", time()) . ".txt";
$responseArray = array();

$postData = file_get_contents("php://input");
$postData = json_decode($postData, true);

$logTxt = json_encode($postData) . $logSeparator. json_encode($_REQUEST).$logSeparator;

$messengerId = isset($postData['MESSENGER_ID']) ? $postData['MESSENGER_ID'] : $_REQUEST['MESSENGER_ID'];

$serviceid="TheDailyStar";

$dbObj = new Database($cms_Database, $cms_Server, $cms_UserID, $cms_Password);
$connection = $dbObj->getConnection();

$subscription = new Subscription($connection);
$already_subscribed = $subscription->checkAlreadySubscribed($serviceid, $messengerId);

$dbObj->closeConnection();

$responseArray['sub_status'] = $already_subscribed ? "registered" : "not_registered";
$responseArray['button_value'] = $already_subscribed ? "Unsubscribe" : "Subscribe";

$result = json_encode($responseArray,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$logTxt .= $result;

$objCommon = new Common();
$objCommon->logWrite($fileName, $logTxt);
echo $result;

ob_end_flush();



