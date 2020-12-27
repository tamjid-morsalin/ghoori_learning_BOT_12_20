<?php

class Subscription
{
    // database connection and table name
    private $conn;
    private $table_name = "subscriberservices";
  
    // object properties
    public $messengerId;
    public $subscriptionGroupId;
    public $categoryID;
    public $status;
    public $registrationDate;
    public $requestSource;
    public $lastUpdate;
  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO $this->table_name 
                    (
                        `MSISDN`,
                        `ServiceID`,
                        `CategoryID`,
                        `Status`,
                        `RegistrationDate`,
                        `RequestSource`,
                        `LastUpdate`
                    )
                VALUES 
                    (
                        '$this->messengerId',
                        '$this->subscriptionGroupId',
                        '$this->categoryID',
                        '$this->status',
                        '$this->registrationDate',
                        '$this->requestSource',
                        '$this->lastUpdate'
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

    public function checkAlreadySubscribed($serviceid, $msisdn)
    {
        $query = "SELECT *FROM $this->table_name WHERE serviceid='$serviceid'AND MSISDN='$msisdn'AND `Status`='Registered'";

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

    public function unSubscribed($messengerId,$subscriptionGroupId)
    {
        $query = "UPDATE $this->table_name 
                SET 
                    `Status`='Deregistered',
                    `LastUpdate`=NOW(),
                    `DeregistrationDate`=NOW() 
                WHERE  
                    `MSISDN`='$messengerId' AND 
                    `ServiceID`='$subscriptionGroupId' AND 
                    `Status`='Registered'";

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