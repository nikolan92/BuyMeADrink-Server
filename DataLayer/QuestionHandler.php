<?php

require_once("DataBaseConnections/MongoDBConnect.php");
require_once("Message.php");
require_once("LocationHelper.php");
class QuestionHandler
{
    private $collection;
    private $locationHelper;
    function __construct()
    {
        $db = new MongoDBConnect();
        $this->collection = $db->getQuestionCollection();
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
            return Message::ErrorMessage("Question does't exist.");
        }
    }
    function deleteQuestionWithSpecificID($questionID){

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