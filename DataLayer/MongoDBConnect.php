<?php 

class MongoDBConnect
{
	private $connection_string = "mongodb://localhost:27017";
    private $con;
    private $db;

    public function __construct()
    {
        $this->con = new MongoClient($this->connection_string);//konekcija na mongoDB
        $this->db = $this->con->selectDB("buymeadrink");//selektujem buymeadrink DataBazu
    }

    public function getUserCollection(){
    	return new MongoCollection($this->db,"user");
    }
}
