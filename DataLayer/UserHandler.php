<?php

require_once("Connect.php");
require_once("Message.php");

class UserHandler {

    private $collection;
    public function __construct()
    {
        $db = new Connect();
        $this->collection = $db->getUserCollection();
    }

    public function saveUser($user){

        $query = array("email" =>$user["email"]);

        $cursor = $this->collection->find($query);

        if($cursor->count() !=0){
            return Message::ErrorMessage("This email already exist!\nTry with other email.");
        }else{
            $this->collection->insert($user);
            $user["_id"] = (string) $user["_id"];
            return Message::SuccessMessage($user);
        }
    }
    public function getUserWithSpecificID($id)
    {
        $query = array("_id" => new MongoId($id));

        $user = $this->collection->findOne($query);
        if($user!=null){
            $user["_id"] = (string)$user["_id"];
            return Message::SuccessMessage($user);
        }else{
            return Message::ErrorMessage("User doesn't exist.");
        }
    }
    public function updateUser($user){
        $query = array("_id"=> new MongoId($user["_id"]));
        unset($user["_id"]);
        $retval = $this->collection->findAndModify($query,$user);

        if($retval !=null){
            return Message::SuccessMessage("Profile is updated.");
        }else{
            return Message::ErrorMessage("User doesn't exist.");
        }
    }
    public function logInUser($email,$password){
        $query = array("email"=>$email,"password"=>$password);

        $user = $this->collection->findOne($query);

        if($user != null) {
            $user["_id"] = (string)$user["_id"];
            return Message::SuccessMessage($user);
        }
        else
            return Message::ErrorMessage("Wrong email or password!");
    }

}