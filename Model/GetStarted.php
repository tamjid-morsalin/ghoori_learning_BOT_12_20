<?php

class GetStarted
{
    // database connection and table name
    private $conn;
    private $table_name = "GetStarted";
  
    // object properties
    public $facebookId;
    public $facebookName;
    public $entryTime;
  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO $this->table_name  
                    ( 
                        `facebook_id`, 
                        `facebook_name`,
                        `entry_time`
                    ) 
                VALUES 
                    (
                        '$this->facebookId', 
                        '$this->facebookName' ,
                        '$this->entryTime' 
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

    public function isExists($messengerId)
    {
        $query="SELECT * 
                FROM $this->table_name 
                WHERE facebook_id='$messengerId'";

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
}