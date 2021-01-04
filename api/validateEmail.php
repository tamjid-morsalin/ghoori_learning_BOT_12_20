<?php

date_default_timezone_set("Asia/Dhaka");

require_once "config/app.php";
require_once "config/database.php";
require_once "lib/database.php";
require_once "lib/Common.php";
require_once "../Model/Users.php";

$fileName = "VALIDATE_EMAIL_" . (string)date("Y_m_d_A", time()) . ".txt";
$responseArray = array();

$postData = file_get_contents("php://input");
$postData = json_decode($postData, true);

$messengerId = isset($postData['MESSENGER_ID']) ? $postData['MESSENGER_ID'] : $_REQUEST['MESSENGER_ID'];

$email = isset($postData['email']) ? $postData['email'] : $_REQUEST['email'];

$logTxt = $messengerId .$logSeparator .$email . $logSeparator . $logSeparator;

$dbObj = new Database($database, $dbServer, $dbUserID, $dbPassword);
$connection = $dbObj->getConnection();

$emailValidateRegularEx = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";

if (!preg_match($emailValidateRegularEx, $email)) {

    $logTxt .= "INVALID EMAIL" . $logSeparator;
    $responseArray['status'] = 'invalid_email';

} else {

    $userObj = new Users($connection);
    // $exitsEmail = $userObj->checkEmailExists($messengerId);

    $userObj->email = $email;
    $userObj->updatedAt = date('Y-m-d H:i:s');

    $status = $userObj->updateEmail($messengerId);

    $logTxt .= "VALID MSISDN" . $logSeparator;
    $responseArray['status'] = 'valid_email';

}

$dbObj->closeConnection();

$result = json_encode($responseArray);

$logTxt .=$logSeparator . $result;

$objCommon = new Common();
$objCommon->logWrite($fileName, $logTxt);

echo $result;


