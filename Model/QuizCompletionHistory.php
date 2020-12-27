<?php

class QuizCompletionHistory
{
    // database connection and table name
    private $conn;
    private $table_name = "quiz_completion_history";

    public function __construct($db){
        $this->conn = $db;
    }

    public function getLeaderBoardData($quiz_cat_id, $quiz_id)
    {
        $query = "SELECT 
                    mt.msisdn,score,time_required,mt.msngr_id 
                FROM 
                    $this->table_name as mt
                inner join (
                    SELECT MAX(score) as maxScore, msngr_id 
                    FROM quiz_completion_history 
                    where quiz_category_id = $quiz_cat_id
                    AND quiz_id = $quiz_id
                    group by msngr_id
                ) as it on (mt.msngr_id = it.msngr_id and mt.score = it.maxScore)
                where quiz_category_id = $quiz_cat_id AND quiz_id = $quiz_id
                ORDER BY score DESC, time_required ASC";


        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if(count($data)){

            return $this->processLeaderBoardData($data);
            
        } else {

            return [];
        }
        
    }

    private function processLeaderBoardData($data)
    {
        $processData = [];

        foreach($data as $key => $value)
        {
            $processData[$value['msngr_id']]['msisdn'] = $value['msisdn'];
            $processData[$value['msngr_id']]['score'] = $value['score'];
            $processData[$value['msngr_id']]['msngr_id'] = $value['msngr_id'];
            $time_required = $value['time_required'];

            if(!isset($processData[$value['msngr_id']]['time_required']))
            {
                $processData[$value['msngr_id']]['time_required'] = $time_required;
            } else {

                if($time_required < $processData[$value['msngr_id']]['time_required'])
                {
                    $processData[$value['msngr_id']]['time_required'] = $time_required;
                }
            }
        }
        
        return $processData;
    }
}