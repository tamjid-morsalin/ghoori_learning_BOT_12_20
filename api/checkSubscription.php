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

$logTxt = json_encode($postData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . $logSeparator;

$messengerId = isset($postData['MESSENGER_ID']) ? $postData['MESSENGER_ID'] : $_REQUEST['MESSENGER_ID'];

SetDBInfo($cms_Server, $cms_Database, $cms_UserID, $cms_Password, $cms_dbtype);
$cn = connectDB();

$islamdailyStatus = getStatus($cn, 'IslamDailyIstishon', $messengerId);
$horoscopeStatus = getStatus($cn, 'HoroscopeIstishon', $messengerId);
$newsStatus = getStatus($cn, 'NewsIstishon', $messengerId);
$loveQuotesStatus = getStatus($cn, 'DailyBuzzLoveQuotes', $messengerId);
$relationshipTipsStatus = getStatus($cn, 'DailyBuzzRelationshipTips', $messengerId);
$wordOfTheDayStatus = getStatus($cn, 'DailyBuzzWordOfTheDay', $messengerId);

ClosedDBConnection($cn);

$responseArray['islam_daily_status'] = $islamdailyStatus > 0 ? "registered" : "not_registered";
$responseArray['islam_daily_button_value'] = $islamdailyStatus > 0 ? "Unsubscribe 📰" : "Subscribe 📰";
$responseArray['horoscope_status'] = $horoscopeStatus > 0 ? "registered" : "not_registered";
$responseArray['horoscope_button_value'] = $horoscopeStatus > 0 ? "Unsubscribe 📰" : "Subscribe 📰";
$responseArray['news_status'] = $newsStatus > 0 ? "registered" : "not_registered";
$responseArray['news_button_value'] = $newsStatus > 0 ? "Unsubscribe 📰" : "Subscribe 📰";
$responseArray['love_quotes_status'] = $loveQuotesStatus > 0 ? "registered" : "not_registered";
$responseArray['love_quotes_button_value'] = $loveQuotesStatus > 0 ? "Unsubscribe 📰" : "Subscribe 📰";
$responseArray['relationship_tips_status'] = $relationshipTipsStatus > 0 ? "registered" : "not_registered";
$responseArray['relationship_tips_button_value'] = $relationshipTipsStatus > 0 ?  "Unsubscribe 📰" : "Subscribe 📰";
$responseArray['word_of_the_day_status'] = $wordOfTheDayStatus > 0 ? "registered" : "not_registered";
$responseArray['word_of_the_day_button_value'] = $wordOfTheDayStatus > 0 ?  "Unsubscribe 📰" : "Subscribe 📰";
echo $result = json_encode($responseArray,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$logTxt .= $logSeparator . $result;
logWrite($fileName, $logTxt);

function getStatus($cn, $serviceid, $msisdn)
{

    $query = "SELECT *FROM `subscriberservices` WHERE serviceid like '$serviceid%'AND MSISDN='$msisdn'AND `Status`='Registered'";
    $rs = Sql_exec($cn, $query);
    $count = Sql_Num_Rows($rs);
    return $count;


}



