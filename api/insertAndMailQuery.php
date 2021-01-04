<?php
date_default_timezone_set("Asia/Dhaka");

require_once "config/app.php";
require_once "config/database.php";
require_once "lib/database.php";
require_once "lib/Common.php";
require_once "../Model/UserQueries.php";
require_once "../Model/Users.php";
require_once "lib/CallExternalApi.php";
require_once "config/service_url.php";

$fileName = "INSERT_QUERY_" . (string)date("Y_m_d_A", time()) . ".txt";

$postData = file_get_contents("php://input");
$postData = json_decode($postData, true);

$messengerId = isset($postData['MESSENGER_ID']) ? $postData['MESSENGER_ID'] : $_REQUEST['MESSENGER_ID'];

$pageId = isset($postData['PAGE_ID']) ? $postData['PAGE_ID'] : $_REQUEST['PAGE_ID'];

$query = isset($postData['query']) ? $postData['query'] : $_REQUEST['query'];

$logTxt = json_encode($postData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . $logSeparator ;

$dbObj = new Database($database, $dbServer, $dbUserID, $dbPassword);
$connection = $dbObj->getConnection();

$userQueryObj = new UserQueries($connection);

$userQueryObj->pageId = $pageId;
$userQueryObj->messengerId = $messengerId;
$userQueryObj->userQuerie = $query;
$userQueryObj->createdAt = date('Y-m-d H:i:s');

$res = $userQueryObj->create();

if($res['status'])
{
	$id = $res['id'];

	$userObj = new Users($connection);

	$userInfo = $userObj->getEmail($messengerId);

	$logTxt = json_encode($userInfo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . $logSeparator ;

	if(!empty($userInfo))
	{	
		if(!is_null($userInfo['email']))
		{	
			$externalApiObj = new CallExternalApi();
			$url = $basePath .'api/emailOrTicket.php';
			
			$emailPostData = [
				'email' => $userInfo['email'],
				'query' => $query
			];
			
			$data = json_decode($externalApiObj->callAPI('POST', $url, $emailPostData));

			if($data->status == 'success')
			{
				$userQueryObj->updateMailSentStatus($id,$data->msg,1);
			} else {
				$userQueryObj->updateMailSentStatus($id,$data->msg,0);
			}
		} else {
			$logTxt .= 'No email found ' . $logSeparator ;
		}

	} else {
		$logTxt .= 'No user found ' . $logSeparator ;
	}

} else {

	$logTxt .= 'User query not created successfully ' . $logSeparator ;
}

$dbObj->closeConnection();
$objCommon = new Common();
$objCommon->logWrite($fileName, $logTxt);
