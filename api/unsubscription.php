<?php
date_default_timezone_set("Asia/Dhaka");

require_once "config/app.php";
require_once "config/database.php";
require_once "lib/database.php";
require_once "lib/Common.php";
require_once "../Model/Subscription.php";


$fileName = "UNSUBSCRIPTION_" . (string)date("Y_m_d_A", time()) . ".txt";
$responseArray = array();

$postData = file_get_contents("php://input");
$postData = json_decode($postData, true);

$logTxt = json_encode($postData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . $logSeparator;

$messengerId = isset($postData['MESSENGER_ID']) ? $postData['MESSENGER_ID'] : $_REQUEST['MESSENGER_ID'];
$subscriptionGroupId="TheDailyStar";

$dbObj = new Database($cms_Database, $cms_Server, $cms_UserID, $cms_Password);
$connection = $dbObj->getConnection();

$subscription = new Subscription($connection);
$already_subscribed = $subscription->checkAlreadySubscribed($subscriptionGroupId, $messengerId);

if ($already_subscribed) {
    $subscription->unSubscribed($messengerId,$subscriptionGroupId);
}

$dbObj->closeConnection();
$responseArray['status'] = 'deregistration_success';

$result = json_encode($responseArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$logTxt .= $result;

$objCommon = new Common();
$objCommon->logWrite($fileName, $logTxt);
echo $result;





