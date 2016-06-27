<?php

require_once("MongoDBConnect.php");
require_once("Message.php");

class UserHandler {

    private $collection;
    public function __construct()
    {
        $db = new MongoDBConnect();
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
    public function getAllUsers(){
        $cursor = $this->collection->find();
        if($cursor == null)
            return Message::ErrorMessage("No users in data base.");

        $cursor->sort(array("rating"=>1));

        $users = array();
        foreach($cursor as $doc){
            $doc["_id"]= (string)$doc["_id"];
            array_push($users,$doc);
        }
        return Message::SuccessMessage($users);
    }
    public function getUserWithSpecificID($id)
    {
        if(!MongoId::isValid($id))
            return Message::ErrorMessage("User id is not valid!");

        $query = array("_id" => new MongoId($id));

        $user = $this->collection->findOne($query);
        if($user!=null){
            $user["_id"] = (string)$user["_id"];
            return Message::SuccessMessage($user);
        }else{
            return Message::ErrorMessage("User doesn't exist.");
        }
    }
    public function getUserFriends($id){

        if(!MongoId::isValid($id))
            return Message::ErrorMessage("User id is not valid!");

        $query = array("_id" => new MongoId($id));

        $user = $this->collection->findOne($query);
        if($user!=null){
            $friendsId = $user["friends"];
            $friendsAsObjects = array();
            foreach($friendsId as $friend)
            {
                $query = array("_id" => new MongoId($friend));
                $friendObject =  $this->collection->findOne($query);
                if($friendObject!=null)
                {
                    $friendObject["_id"] = (string)$friendObject["_id"];
                    array_push($friendsAsObjects,$friendObject);
                }
            }
            return Message::SuccessMessage($friendsAsObjects);
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