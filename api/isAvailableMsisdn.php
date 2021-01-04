<?php
date_default_timezone_set("Asia/Dhaka");

require_once "config/app.php";
require_once "config/database.php";
require_once "lib/database.php";
require_once "lib/Common.php";
require_once "../Model/Users.php";

$fileName = "IS_AVAILABLE_MSISDN_" . (string)date("Y_m_d_A", time()) . ".txt";
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

$userObj = new Users($connection);
$status = $userObj->checkMsisdnExists($messengerId);

if(!$status)
{
     $responseArray['msisdn_status'] = 'no_msisdn';
}
else{
   $responseArray['msisdn_status'] = 'has_msisdn';
}

$dbObj->closeConnection();
$result = json_encode($responseArray);
$logTxt .= $result;

$objCommon = new Common();
$objCommon->logWrite($fileName, $logTxt);

echo $result;




