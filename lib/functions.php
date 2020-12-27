<?php

/*
 * Function to unescaped UNICODE meshup Data
 */


function unescapedUnicodeJSON($utf8MeshupData)
{
    $json = json_encode($utf8MeshupData, JSON_UNESCAPED_UNICODE);
    return $json;
}


//function for log writing
function logWrite($fileName, $logTxt)
{
    global $logEnable, $logSeparator;

    if ($logEnable) {

        $file = fopen("logs/" . $fileName, 'a+');
        fwrite($file, date("Y-m-d H:i:s", time()) . $logSeparator . $logTxt . PHP_EOL);
        fclose($file);
    }


}

function logWriteDeeper($fileName, $logTxt)
{
    global $logEnable, $logSeparator;

    if ($logEnable) {

        $file = fopen($fileName, 'a+');
        fwrite($file, date("Y-m-d H:i:s", time()) . $logSeparator . $logTxt . PHP_EOL);
        fclose($file);
    }

}


function SetDBInfo($dbservers, $dbnames, $dbusername, $dbpassword, $dbtypes)
{
    global $Server;
    global $Database;
    global $UserID;
    global $Password;
    global $dbtype;

    $dbtype = $dbtypes;
    $Server = $dbservers;
    $Database = $dbnames;
    $UserID = $dbusername;
    $Password = $dbpassword;
}


function getResponse($dataArray, $baseUrl)//get dynamic url for each purpose using the token
{
    global $logTxt, $logSeparator;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataArray));  //Post Fields
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = [
        "Content-Type: application/json",
        "Accept: application/json",
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $serverOutput = curl_exec($ch);
    //$responseDecoded = json_decode($serverOutput, true);

    if (curl_errno($ch)) {
        $logTxt .= "cURL Error #:" . curl_error($ch);
    } else {
        $logTxt .= json_encode($dataArray) . $logSeparator . $baseUrl . $logSeparator . $serverOutput;
    }

    curl_close($ch);
    return $serverOutput;

}

function getProductID($purpose, $operator)
{
    global $cn;
    $qry = "SELECT `productID` FROM `productInfo` WHERE purpose='$purpose'AND operator='$operator'";
    $rs = Sql_exec($cn, $qry);

    while ($row = Sql_fetch_array($rs)) {
        $productID = $row['productID'];
    }
    return $productID;
}

function getCountryList($purpose)
{
    global $cn, $logTxt, $logSeparator;
    $qry = "SELECT DISTINCT country AS payload_value,country AS country,country AS title,'' AS subtitle,`countryLogo` AS image_url FROM `productInfo`
 WHERE `purpose`='topup'";
    if (strtoupper($purpose) == "INTERNET_PACK") {
        $qry .= "AND country IN('Bangladesh','Indonesia') ";
    }
    else{
        $qry .=" LIMIT 9";
    }

    $rs = Sql_exec($cn, $qry);

    while ($row = Sql_fetch_assoc_array($rs)) {

        $data[] = $row;
    }
    $logTxt .= $qry . $logSeparator;
    $result = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return $result;
}

function getOperatorList($purpose, $country)
{
    global $cn, $logTxt, $logSeparator;
    $qry = "SELECT DISTINCT operator AS payload_value,operator AS operator,operator AS title,'' AS subtitle,`operatorLogo` AS image_url
 FROM `productInfo`
WHERE purpose='$purpose'AND country='$country' LIMIT 9";
    $rs = Sql_exec($cn, $qry);

    while ($row = Sql_fetch_assoc_array($rs)) {

        $data[] = $row;
    }
    $logTxt .= $qry . $logSeparator;
    $result = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return $result;
}

function getOperatorLogo($country, $operator)
{
    global $cn, $logTxt, $logSeparator;
    $qry = "SELECT `operatorLogo` FROM `productInfo` WHERE `country`='$country'AND `operator`='$operator' LIMIT 1";
    $rs = Sql_exec($cn, $qry);

    while ($row = Sql_fetch_array($rs)) {
        $operatorLogo = $row['operatorLogo'];
    }

    $logTxt .= $qry . $logSeparator . $operatorLogo . $logSeparator;
    return $operatorLogo;
}