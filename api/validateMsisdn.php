<?php

date_default_timezone_set("Asia/Dhaka");

require_once "config/app.php";
require_once "config/database.php";
require_once "lib/database.php";
require_once "lib/Common.php";
require_once "../Model/QuizPlayer.php";

$fileName = "VALIDATE_MSISDN_" . (string)date("Y_m_d_A", time()) . ".txt";
$responseArray = array();

$postData = file_get_contents("php://input");
$postData = json_decode($postData, true);

$messengerId = isset($postData['MESSENGER_ID']) ? $postData['MESSENGER_ID'] : $_REQUEST['MESSENGER_ID'];
$pageId = $_REQUEST['page_id'];

$msisdn = isset($postData['msisdn']) ? $postData['msisdn'] : $_REQUEST['msisdn'];
$purpose = isset($postData['purpose']) ? $postData['purpose'] : $_REQUEST['purpose'];
$mobileNumber = substr($msisdn, -10);
$opCode = substr($mobileNumber, 0, 2);
$validOpCodeArray = array( "13", "14", "15", "16", "17", "18", "19");
$logTxt = $messengerId .$logSeparator .$msisdn . $logSeparator. $purpose . $logSeparator;
$name = isset($postData['name']) ? $postData['name'] : $_REQUEST['name'];
$firstName = isset($postData['first_name']) ? $postData['first_name'] : $_REQUEST['first_name'];
$lastName = isset($postData['last_name']) ? $postData['last_name'] : $_REQUEST['last_name'];
$fb_name=$firstName." ".$lastName;

if ($msisdn == "" || empty($msisdn) || !in_array($opCode, $validOpCodeArray)) {

    $logTxt .= "INVALID MSISDN" . $logSeparator;
    $responseArray['status'] = 'invalid_msisdn';
} else {

    $dbObj = new Database($database, $dbServer, $dbUserID, $dbPassword);
    $connection = $dbObj->getConnection();

    $quizPlayer = new QuizPlayer($connection);
    $exitsMsisdn = $quizPlayer->checkMsisdnExists($messengerId);

    if ($exitsMsisdn) {

        $quizPlayer->msisdn = $msisdn;
        $quizPlayer->name = $name;

        $status = $quizPlayer->update($messengerId);
  
    } else {

        $quizPlayer->msisdn = $msisdn;
        $quizPlayer->name = $name;
        $quizPlayer->fb_name = $fb_name;
        $quizPlayer->messengerId = $messengerId;
        $quizPlayer->updatedAt = date('Y-m-d H:i:s');
        $quizPlayer->createdAt = date('Y-m-d H:i:s');

        $status = $quizPlayer->create();
       
    }

    $logTxt .= "VALID MSISDN" . $logSeparator;
    $responseArray['status'] = 'valid_msisdn';

}

$dbObj->closeConnection();

$result = json_encode($responseArray);

$logTxt .=$logSeparator . $result;

$objCommon = new Common();
$objCommon->logWrite($fileName, $logTxt);

echo $result;


