<?php

class Users
{
    // database connection and table name
    private $conn;
    private $table_name = "Users";
  
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

    public function checkMsisdnExists($messengerId)
    {
        $query="SELECT * 
                FROM 
                    $this->table_name
                WHERE 
                    `messenger_id`='$messengerId' AND 
                    `msisdn`<> ''";

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

    public function insertMobile()
    {
        $query = "INSERT INTO $this->table_name 
                    (
                        `fb_name`,
                        `messenger_id`,
                        `msisdn`,
                        `created_at`
                    )
                VALUES 
                    (
                        '$this->fb_name',
                        '$this->messengerId',
                        '$this->msisdn',
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

    public function updateEmail($messengerId)
    {
        $query = "UPDATE 
                    $this->table_name 
                SET 
                    `email`='$this->email',
                    `updated_at` = '$this->updatedAt'
                WHERE  `messenger_id`='$messengerId' ";

        $stmt = $this->conn->prepare($query);
        
        try{
            $status = $stmt->execute();

        } catch (PDOException $exception)
        {
            $status = false;
        }
        
        return $status;
    }

    public function updateMobile($messengerId)
    {
        $query = "UPDATE 
                    $this->table_name 
                SET 
                    `msisdn`='$this->msisdn'
                WHERE  `messenger_id`='$messengerId' ";

        $stmt = $this->conn->prepare($query);
        
        try{
            $status = $stmt->execute();

        } catch (PDOException $exception)
        {
            $status = false;
        }
        
        return $status;
    }

    public function checkEmailExists($messengerId)
    {
        $query="SELECT * 
                FROM 
                    $this->table_name
                WHERE 
                    `messenger_id`='$messengerId' AND 
                    `email`<>''";

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

    public function getEmail($messengerId)
    {
        $query="SELECT * 
                FROM 
                    $this->table_name
                WHERE 
                    `messenger_id`='$messengerId'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data;
    }
}