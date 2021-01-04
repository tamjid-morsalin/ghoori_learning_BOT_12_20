<?php
date_default_timezone_set("Asia/Dhaka");

require_once "config/app.php";
require_once "config/database.php";
require_once "lib/database.php";
require_once "lib/Common.php";
require_once "../Model/Users.php";

$fileName = "IS_AVAILABLE_EMAIL_" . (string)date("Y_m_d_A", time()) . ".txt";
$responseArray = array();

$postData = file_get_contents("php://input");
$postData = json_decode($postData, true);

$messengerId = isset($postData['MESSENGER_ID']) ? $postData['MESSENGER_ID'] : $_REQUEST['MESSENGER_ID'];

$logTxt = json_encode($postData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . $logSeparator ;

$dbObj = new Database($database, $dbServer, $dbUserID, $dbPassword);
$connection = $dbObj->getConnection();

$userObj = new Users($connection);
$status = $userObj->checkEmailExists($messengerId);

if(!$status)
{
     $responseArray['email_status'] = 'no_email';
}
else{
   $responseArray['email_status'] = 'has_email';
}

$dbObj->closeConnection();
$result = json_encode($responseArray);
$logTxt .= $result;

$objCommon = new Common();
$objCommon->logWrite($fileName, $logTxt);

echo $result;




