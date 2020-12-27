<?php

class QuizPlayer
{
    // database connection and table name
    private $conn;
    private $table_name = "quiz_player";
  
    // object properties
    public $msisdn;
    public $name;
    public $fb_name;
    public $messengerId;
    public $updatedAt;
    public $createdAt;
  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO $this->table_name 
                    (
                        `name`,
                        `fb_name`,
                        `MESSENGER_ID`,
                        `msisdn`,
                        `updated_at`,
                        `created_at`
                    )
                VALUES 
                    (
                        '$this->name',
                        '$this->fb_name',
                        '$this->messengerId',
                        '$this->msisdn',
                        '$this->updatedAt',
                        '$this->createdAt'
                    )";

        $stmt = $this->conn->prepare($query);
        
        try{
            $status = $stmt->execute();

        } catch (PDOException $exception)
        {
            $status = false;
        }
        
        return $status;
    }

    public function checkMsisdnExists($messengerId)
    {
        $query="SELECT * 
                FROM 
                    $this->table_name
                WHERE 
                    `MESSENGER_ID`='$messengerId' AND 
                    `msisdn`<>''";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if(count($data))
        {
            return true;

        } else {

            return false;
        }
    }

    public function update($messengerId)
    {
        $query = "UPDATE 
                    $this->table_name 
                SET 
                    `msisdn`='$this->msisdn' ,
                    `name`='$this->name' 
                WHERE  `MESSENGER_ID`='$messengerId' ";
    }

    public function getPlayerData()
    {
        $query="SELECT * 
                FROM 
                    $this->table_name";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(count($data))
        {
            return $this->processPlayerData($data);
        } else {
            return [];
        }
    }

    private function processPlayerData($data)
    {
        $processData = [];
        
        foreach($data as $key => $value)
        {
            $processData[$value['MESSENGER_ID']]['name'] = $value['name'];
        }

        return $processData;
    }
}