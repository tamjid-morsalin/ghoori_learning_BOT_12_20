<?php
date_default_timezone_set("Asia/Dhaka");

require_once "config/app.php";
require_once "config/database.php";
require_once "lib/database.php";
require_once "lib/Common.php";
require_once "../Model/GetStarted.php";


$fileName = "GET_STARTED_GAME_ON" . (string)date("Y_m_d_A", time()) . ".txt";
$responseArray = array();

$postData = file_get_contents("php://input");
$postData = json_decode($postData, true);

$messengerId = isset($postData['MESSENGER_ID']) ? $postData['MESSENGER_ID'] : $_REQUEST['MESSENGER_ID'];
$firstName = isset($postData['first_name']) ? $postData['first_name'] : $_REQUEST['first_name'];
$lastName = isset($postData['last_name']) ? $postData['last_name'] : $_REQUEST['last_name'];
$fullName=$firstName." ".$lastName;


$logTxt = json_encode($postData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . $logSeparator ;

$dbObj = new Database($database, $dbServer, $dbUserID, $dbPassword);
$connection = $dbObj->getConnection();

$getStartedObj = new GetStarted($connection);
$is_exists = $getStartedObj->isExists($messengerId);

if(!$is_exists)
{
    $getStartedObj->facebookId = $messengerId;
    $getStartedObj->facebookName = $fullName;
    $getStartedObj->entryTime = date('Y-m-d H:i:s');

    $getStartedObj->create();

    $logTxt .= "new_user";
}
else{
    $logTxt .= "old_user";
}

$dbObj->closeConnection();
$objCommon = new Common();
$objCommon->logWrite($fileName, $logTxt);




