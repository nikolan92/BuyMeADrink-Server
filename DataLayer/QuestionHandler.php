<?php

require_once("DataBaseConnections/MongoDBConnect.php");
require_once("Message.php");
require_once("LocationHelper.php");
require_once("UserHandler.php");
class QuestionHandler
{
    private $collection;
    function __construct()
    {
        $db = new MongoDBConnect();
        $this->collection = $db->getQuestionCollection();
    }
    function searchQuestions($query,$lat,$lng,$category,$range){
        $finalQuery = array("question"=>(array('$regex'=> new MongoRegex("/$query/i"))));

        $cursor = $this->collection->find($finalQuery);

        if($cursor->count()==0){
            return Message::ErrorMessage("There is no question does not meet the requirements.");
        }

        $questions = array();
        foreach($cursor as $question){
            $question["_id"] = (string)$question["_id"];
            array_push($questions,$question);
        }
        return Message::SuccessMessage($questions);
    }
    function saveQuestion($question)
    {
        $ownerID = $question["ownerID"];
        if (MongoId::isValid($ownerID)) {
            $this->collection->insert($question);
            $question["_id"] = (string)$question["_id"];

            //set location of new question in redis database
            $locationHelper = new LocationHelper();
            $locationHelper->setQuestionLocation($question["_id"],array("lat"=>$question["lat"],"lng"=>$question["lng"]));

            return Message::SuccessMessage($question);
        }else{
            return Message::ErrorMessage("Wrong ownerID!");
        }
    }

    public function getAllQuestions(){
        $cursor = $this->collection->find();

        if($cursor->count()==0)
            return Message::ErrorMessage("No questions in data base.");

        $questions = array();
        foreach($cursor as $doc){
            $doc["_id"]= (string)$doc["_id"];
            array_push($questions,$doc);
        }
        return Message::SuccessMessage($questions);
    }

    function getQuestionWithSpecificID($id){
        if(!MongoId::isValid($id))
            return Message::ErrorMessage("Question id is not valid!");

        $query = array("_id" => new MongoId($id));

        $question = $this->collection->findOne($query);
        if($question!=null){
            $question["_id"] = (string)$question["_id"];
            return Message::SuccessMessage($question);
        }else{
            return Message::ErrorMessage("Question does't exist. Maybe someone answer on this question in meanwhile.");
        }
    }

    public function tryToAnswerTheQuestion($questionID,$userID,$answerNum){

        $questionData = $this->getQuestionWithSpecificID($questionID);
        $locationHelper = new LocationHelper();

        //if is this user already try to answer on this question, return error message.
        if($locationHelper->checkUserBlockList($userID,$questionID))
            return Message::ErrorMessage("You are already try to answer on this question, try again after one day.");

        if($questionData["Success"]){
            $question = $questionData["Data"];
            if($question["ownerID"]==$userID){
                return Message::ErrorMessage("You can't answer on your own question!");
            }else{
                //get userLocation from data base
                $userLocation = $locationHelper->getUserLocation($userID);
                $questionLocation = array("lat"=>$question["lat"],"lng"=>$question["lng"]);

                //range is fix and is 20m
                if(!$locationHelper->calculateDistance($userLocation,$questionLocation,20)){
                    return Message::ErrorMessage("You can't answer on this question because you are too far.");
                }else {
                    if ($question["trueAnswer"] != $answerNum) {
                        //this user can't answer on this question for awhile.
                        $locationHelper->setUserBlockList($userID, $questionID);
                        return Message::ErrorMessage("Sorry but that answer is not correct, try again after one day.");
                    } else {
                        //Delete question from redis data base
                        //TODO:raise user rating
                        $userHelper = new UserHandler();
                        $userData = $userHelper->getUserWithSpecificID($userID);
                        //if for some user does't exist return but this should never happen.
                        if(!$userData["Success"]){
                            return Message::ErrorMessage("Something went wrong, sorry about that, try again later.");
                        }
                        $user = $userData["Data"];
                        //raise user rating and update user
                        var_dump($user);
                        $user["rating"] = $user["rating"]+10;

                        $userHelper->updateUser($user);

                        $locationHelper->deleteQuestionLocation($questionID);
                        $this->deleteQuestionWithSpecificID($questionID);
                        return Message::SuccessMessage("Question deleted.");
                    }
                }
            }

        }else{
            return $questionData;
        }
    }
    private function deleteQuestionWithSpecificID($questionID){

        //Delete location from redis database
        $locationHelper = new LocationHelper();
        $locationHelper->deleteQuestionLocation($questionID);

        if($this->deleteQuestion($questionID))
        {
            return Message::SuccessMessage("Question deleted.");
        }else{
            return Message::ErrorMessage("Question does't exist.");
        }

    }
    private function deleteQuestion($questionID){
        if(MongoId::isValid($questionID)) {
            $result = $this->collection->remove(array("_id" => new MongoId($questionID)), array("justOne" => true));
            //check whether question has been really deleted
            if($result["n"] ==0)
            {
                return false;
            }else{
                return true;
            }
        }
        else{
            return false;
        }
    }
}