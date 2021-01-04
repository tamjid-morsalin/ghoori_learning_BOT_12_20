<?php

class Common
{
    function redirect($url, $statusCode = 303)
    {
        header('Location: ' . $url, true, $statusCode);
    }

    function logWrite($fileName, $logTxt)
    {
        global $logEnable, $logSeparator;
        //error_reporting(E_ALL);

        if ($logEnable) {

            // $path = $_SERVER['DOCUMENT_ROOT']."/ghoori_learning_BOT_12_20/logs/" . $fileName;
            $path = "../logs/". $fileName;
            $file = fopen($path, 'a+');
            //var_dump(error_get_last());
            fwrite($file, date("Y-m-d H:i:s", time()) . $logSeparator . $logTxt . PHP_EOL);
            fclose($file);
        }
        //var_dump(error_get_last());
    }

    public function generateLeaderboardData($playersPositionData,$playersInfo)
    {
        $result = [];
        
        if(count($playersPositionData))
        {
            $position = 1;

            foreach($playersPositionData as $key => $value)
            {
                $result[$position]['position'] = $position;
                $result[$position]['msisdn'] = $key;
                $result[$position]['score'] = $value['score'];
                $result[$position]['time_required'] = $value['time_required'];
		$result[$position]['msngr_id'] = $value['msngr_id'];

                if(isset($playersInfo[$key]['name']))
                {
                    $result[$position]['name'] = $playersInfo[$key]['name'];
                } else {
                    $result[$position]['name'] = NULL;
                }

                $position ++;
            }
        }

        return $result;
    }

    public function getMyPosition($leaderBoardData, $messengerId)
    {
        $my_position = 0;

        foreach($leaderBoardData as $key => $value)
        {
            if($value['msngr_id'] == $messengerId)
            {
                $my_position = $value['position'];
                break;
            }
        }

        return $my_position;
    }

}

?>