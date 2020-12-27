<?php
date_default_timezone_set("Asia/Dhaka");

ob_start();
require_once "config/app.php";
require_once "config/database.php";
require_once "lib/database.php";
require_once "lib/Common.php";
require_once "../Model/QuizCompletionHistory.php";
require_once "../Model/QuizPlayer.php";
require_once "../Model/NodeLog.php";

error_reporting(E_ALL);
$fileName = "GET_LEADER_BOARD_" . (string)date("Y_m_d_A", time()) . ".txt";
$responseArray = array();

$postData = file_get_contents("php://input");
$postData = json_decode($postData, true);

$logTxt = json_encode($postData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . $logSeparator;

$messengerId = isset($postData['MESSENGER_ID']) ? $postData['MESSENGER_ID'] : $_REQUEST['MESSENGER_ID'];
$quizIdCategory  = isset($postData['carouselvalue']) ? $postData['carouselvalue'] : $_REQUEST['carouselvalue'];

$quizIdCategoryArray = explode(".", $quizIdCategory);

$quiz_id = $quizIdCategoryArray[1];
$quiz_cat_id = $quizIdCategoryArray[0];

$logTxt .= json_encode($_REQUEST). $logSeparator;

$dbObj = new Database($bot_quiz_Database, $bot_quiz_Server, $bot_quiz_UserID, $bot_quiz_Password);
$connection = $dbObj->getConnection();

$qchObj = new QuizCompletionHistory($connection);
$playersPositionData = $qchObj->getLeaderBoardData($quiz_cat_id, $quiz_id);

$dbObj->closeConnection();

$dbObj = new Database($database, $dbServer, $dbUserID, $dbPassword);
$connection = $dbObj->getConnection();

$qzPlayerObj = new QuizPlayer($connection);
$playersInfo = $qzPlayerObj->getPlayerData();

$objCommon = new Common();
$leaderBoardData = $objCommon->generateLeaderboardData($playersPositionData,$playersInfo);

$leaderBoardData = array_slice($leaderBoardData, 0, $limit);

$my_position = $objCommon->getMyPosition($leaderBoardData,$messengerId);

$objCommon->logWrite($fileName, $logTxt);

$nodeLog = new NodeLog($connection);
$nodeLog->messengerId = $messengerId;
$nodeLog->inputDate = date('Y-m-d H:i:s');
$nodeLog->nodeName = 'leaderboard_page';

$status = $nodeLog->create();

include('view/leaderboard.html');

ob_end_flush();
 

