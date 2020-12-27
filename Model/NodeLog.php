<?php

class NodeLog
{
    // database connection and table name
    private $conn;
    private $table_name = "nodeLog";
  
    // object properties
    public $messengerId;
    public $inputDate;
    public $nodeName;
  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO $this->table_name  
                    (
                        `messengerId`, 
                        `input_date`,
                        `nodeName`
                    ) 
                VALUES 
                    (
                        '$this->messengerId', 
                        '$this->inputDate' ,
                        '$this->nodeName' 
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
}