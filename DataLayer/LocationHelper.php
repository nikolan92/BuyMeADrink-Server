<?php
require_once("DataBaseConnections/RedisDBConnect.php");
require_once("QuestionHandler.php");
class LocationHelper
{
    private $client;

    public function __construct()
    {
        $connect = new RedisDBConnect();
        $this->client = $connect->getClient();
    }

    public function updateUserLocationAndReturnNearbyObjects(array $user){

        $user_current_location  = array("lat"=>$user["lat"],"lng" => $user["lng"]);
        $user_id = $user["user_id"];
        $range = $user["range"];
        $user_friends = $user["friends"];

        //update user current location in database.
        $this->client->set("user:$user_id",json_encode($user_current_location));

        //check for friends in nearby,
        //and get all available friends location (for map update)
        $friends_in_nearby = array();
        $friends_locations = array();
        foreach($user_friends as $friend)
        {
            $friend_location = $this->getUserLocation($friend);
            if($friend_location) {
                //add location of this friend in friends_location array
                $tmp = $friend_location;
                $tmp["_id"]= $friend;
                array_push($friends_locations, $tmp);
                //if this friend is in black list do nothing,
                //otherwise check distance between user location and friend location
                if (!$this->checkForObjectInBlackList($user_id, $friend)) {
                    //if friend location is in range with this user add his id in friends_in_nearby array
                    if ($this->calculateDistance($user_current_location, $friend_location, $range)) {
                        //set this friend of this user in blacklist for awhile;
                        $this->setObjectInBlackList($user_id, $friend);
                        array_push($friends_in_nearby, $friend);
                    }
                }
            }
        }

        //TODO: for now I reading question list from mongo db this can be speedup by using redis sets and remeber

        //here we have all questions ids
        $questionsIDs = $this->getQuestionsIDs();

        $questions_in_nearby = array();
        if($questionsIDs){
            foreach($questionsIDs as $questionID){
                //if this question is not on user's blacklist check for range
                $questionLocation = $this->getQuestionLocation($questionID);
                if(!$this->checkForObjectInBlackList($user_id,$questionID)){
                    if($this->calculateDistance($user_current_location,$questionLocation , $range)){
                        $this->setObjectInBlackList($user_id, $questionID);
                        array_push($questions_in_nearby,$questionID);
                    }
                }
            }
        }

        $response_data = array("friends_location"=>$friends_locations,"friends_in_nearby"=>$friends_in_nearby,"questions_in_nearby"=>$questions_in_nearby);
        return Message::SuccessMessage($response_data);
    }
    /**
     * <p>Set question location in redis database</p>
     * @param $questionID "3213211"
     * @param $questionLocation array("lat"=>12.223,"lng"=>52.04554)
     */
    public function setQuestionLocation($questionID,$questionLocation){
        $this->client->set("question:".$questionID,json_encode($questionLocation));
        $this->client->sadd("questionsIDs",$questionID);
    }

    /**
     * <p>Return all questions ids</p>
     * @return array
     * 0 => string '57794f3434721e0c0900002a' (length=24)
     * 1 => string '57794f2934721e0c09000029' (length=24)
     * ....
     */
    private function getQuestionsIDs(){
        return $this->client->smembers("questionsIDs");
    }
    /**
     * <p>Delete question with given id in redis database</p>
     * @param string "26655454"
     */
    public function deleteQuestionLocation($questionId){
        //delete information about question location
        $this->client->del("question:".$questionId);
        //remove questionID from questionsIDs set
        $this->client->srem("questionsIDs",$questionId);
    }
    /**
     * <p>Return question location in array.</p>
     * @param string "26655454"
     * @return array("lat"=>12.223,"lng"=>52.04554)
     */
    private function getQuestionLocation($questionId){
        return (array)json_decode($this->client->get("question:".$questionId));
    }
    /**
     * <p>Return user location in array.</p>
     * @param $user_id string
     * @return array (lat => 23.21,lng => 22.22)
     */
    private function getUserLocation($user_id){
        return (array)json_decode($this->client->get("user:".$user_id));
    }
    /**
     *<p> Set some object in user black list and after some time this key will be deleted.
     * So user will receive only nearby object only once, in some period of time.(1h)3600</p>
     * @param $user_id string
     * @param $object_id string
     * */
    private function setObjectInBlackList($user_id,$object_id){
        $this->client->setex("user:$user_id:blackListObject:$object_id",20,true);
    }
    /**
     * Check for user black list.
     * Return true if object is on black list or null if is not.
     * @param $user_id string
     * @param $object_id string
     * @return bool
     * */
    private function checkForObjectInBlackList($user_id,$object_id){
        return $this->client->get("user:$user_id:blackListObject:$object_id");
    }
    /**
     * This is user block list, when user try to answer on some question and if he gives wrong answer then it will block him
     * for some time (1day)for example.
     * Return true if object is on black list or null if is not.
     * @param $user_id string
     * @param $question_id string
     * */
    public function setUserBlockList($user_id,$question_id){
        $this->client->setex("user:$user_id:blockListQuestion:$question_id",3600,true);
    }
    /**
     * Check for user block list.
     * Return true if user is in block list or null if is not.
     * @param $user_id string
     * @param $question_id string
     * @return bool
     * */
    public function checkUserBlockList($user_id,$question_id){
        return $this->client->get("user:$user_id:blockListQuestion:$question_id");
    }
    /**
     * Euclidean algorithm for distance.
     * <p>Return true if points are in range,or false if is not.</p>
     * $latLng array(lat => 23.21,lng => 22.22)
     * @param $latLng array
     * @param $latLng1 array
     * @param $range int
     * @return bool
     */
    private function calculateDistance(array $latLng,array $latLng1,$range){
        $delegan = 110.25;
        $lat = $latLng["lat"];
        $lng = $latLng["lng"];
        $lat1 = $latLng1["lat"];
        $lng1 = $latLng1["lng"];

        $x = $lat - $lat1;
        $y = ($lng - $lng1)*cos($lat);

        $distance = ($delegan*sqrt($x*$x + $y*$y))*1000;
        if($distance<=$range)
            return true;
        else
            return false;
    }

}