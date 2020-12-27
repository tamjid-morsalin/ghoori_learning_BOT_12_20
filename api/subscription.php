<?php
date_default_timezone_set("Asia/Dhaka");

require_once "config/app.php";
require_once "config/database.php";
require_once "lib/database.php";
require_once "lib/Common.php";
require_once "../Model/Subscription.php";

error_reporting(E_ALL);
$fileName = "SUBSCRIPTION_" . (string)date("Y_m_d_A", time()) . ".txt";
$responseArray = array();

$postData = file_get_contents("php://input");
$postData = json_decode($postData, true);

$logTxt = json_encode($postData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . $logSeparator;

$messengerId = isset($postData['MESSENGER_ID']) ? $postData['MESSENGER_ID'] : $_REQUEST['MESSENGER_ID'];

$subscriptionGroupId="TheDailyStar";

$CategoryID = $subscriptionGroupId;

$day=date("l");

$dbObj = new Database($cms_Database, $cms_Server, $cms_UserID, $cms_Password);
$connection = $dbObj->getConnection();

$subscription = new Subscription($connection);
$already_subscribed = $subscription->checkAlreadySubscribed($subscriptionGroupId, $messengerId);

if (!$already_subscribed) {

    $subscription->messengerId = $messengerId;
    $subscription->subscriptionGroupId = $subscriptionGroupId;
    $subscription->categoryID = $CategoryID;
    $subscription->status = 'Registered';
    $subscription->registrationDate = date('Y-m-d H:i:s');
    $subscription->requestSource = 'BOT';
    $subscription->lastUpdate = date('Y-m-d H:i:s');

    $subscription->create();
}

$dbObj->closeConnection();

$responseArray['status'] = 'registration_success';

$result = json_encode($responseArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$logTxt .= $result;

$objCommon = new Common();
$objCommon->logWrite($fileName, $logTxt);
echo $result;
 

