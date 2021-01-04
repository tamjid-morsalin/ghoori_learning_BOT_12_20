<?php

class UserQueries
{
    // database connection and table name
    private $conn;
    private $table_name = "user_queries";
  
    // object properties
    public $pageId;
    public $messengerId;
    public $userQuerie;
    public $createdAt;
    public $updatedAt;
  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO $this->table_name  
                    (
                        `page_id`,
                        `messenger_id`, 
                        `user_query`,
                        `created_at`
                    ) 
                VALUES 
                    (
                        '$this->pageId',
                        '$this->messengerId', 
                        '$this->userQuerie' ,
                        '$this->createdAt' 
                    )";

        $stmt = $this->conn->prepare($query);
        
        try{
            $status = $stmt->execute();
            $id = $this->conn->lastInsertId();

        } catch (PDOException $exception)
        {
            $status = false;
            $id = NULL;
        }
        
        // return $status;
        return [
            'status' => $status,
            'id' => $id
        ];
    }

    public function updateMailSentStatus($id,$response,$status)
    {
        $query = "UPDATE 
                    $this->table_name 
                SET 
                    `is_mail_sent`='$status',
                    `response`='$response'
                WHERE  `id`='$id' ";

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