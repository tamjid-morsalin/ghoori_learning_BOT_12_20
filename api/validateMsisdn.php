<?php

date_default_timezone_set("Asia/Dhaka");

require_once "config/app.php";
require_once "config/database.php";
require_once "lib/database.php";
require_once "lib/Common.php";
require_once "../Model/Users.php";

$fileName = "VALIDATE_MSISDN_" . (string)date("Y_m_d_A", time()) . ".txt";
$responseArray = array();

$postData = file_get_contents("php://input");
$postData = json_decode($postData, true);

$messengerId = isset($postData['MESSENGER_ID']) ? $postData['MESSENGER_ID'] : $_REQUEST['MESSENGER_ID'];

$msisdn = isset($postData['msisdn']) ? $postData['msisdn'] : $_REQUEST['msisdn'];

$logTxt = $messengerId .$logSeparator .$msisdn . $logSeparator . $logSeparator;

$firstName = isset($postData['first_name']) ? $postData['first_name'] : $_REQUEST['first_name'];
$lastName = isset($postData['last_name']) ? $postData['last_name'] : $_REQUEST['last_name'];
$fb_name=$firstName." ".$lastName;

$dbObj = new Database($database, $dbServer, $dbUserID, $dbPassword);
$connection = $dbObj->getConnection();

$mobileValidateRegularEx = "/^01[3456789][0-9]{8}$/";

if (!preg_match($mobileValidateRegularEx, $msisdn)) {

    $logTxt .= "INVALID MSISDN" . $logSeparator;
    $responseArray['status'] = 'invalid_msisdn';

} else {

    $userObj = new Users($connection);
    $exitsMsisdn = $userObj->checkMsisdnExists($messengerId);
    
    if ($exitsMsisdn) {

        $userObj->msisdn = $msisdn;
        $userObj->name = $name;

        $status = $userObj->updateMobile($messengerId);
  
    } else {

        $userObj->msisdn = $msisdn;
        $userObj->fb_name = $fb_name;
        $userObj->messengerId = $messengerId;
        $userObj->updatedAt = date('Y-m-d H:i:s');
        $userObj->createdAt = date('Y-m-d H:i:s');

        $status = $userObj->insertMobile();
       
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


