<?php
require_once("DataBaseConnections/RedisDBConnect.php");
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
                $tmp = (array)$friend_location;
                $tmp["_id"]= $friend;
                array_push($friends_locations, $tmp);
                //if this friend is in black list do nothing,
                //otherwise check distance between user location and friend location
                if (!$this->checkForObjectInBlackList($user_id, $friend)) {
                    //if friend location is in range with this user add his id in friends_in_nearby array
                    if ($this->calculateDistance($user_current_location, (array)$friend_location, $range)) {
                        //set this friend of this user in blacklist for awhile;
                        $this->setObjectInBlackList($user_id, $friend);
                        array_push($friends_in_nearby, $friend);
                    }
                }
            }
        }

        //TODO:Check for questions in nearby
        $response_data = array("friends_location"=>$friends_locations,"friends_in_nearby"=>$friends_in_nearby,"questions_in_nearby"=>[]);
        return Message::SuccessMessage($response_data);
    }
    /**
     * <p>Return user location in array.</p>
     * return array(lat => 23.21,lng => 22.22)
     */
    private function getUserLocation($user_id){
        return json_decode($this->client->get("user:".$user_id));
    }
    /**
     * Set some object in user black list and after some time this key will be deleted.
     * So user will receive only nearby object only once, in some period of time.
     * */
    private function setObjectInBlackList($user_id,$object_id){
        $this->client->setex("user:$user_id:blackListObject:$object_id",3600,true);
    }
    /**
     * Check for user black list.
     * Return true if object is on black list or null if is not.
     * */
    private function checkForObjectInBlackList($user_id,$object_id){
        return $this->client->get("user:$user_id:blackListObject:$object_id");
    }
    /**
     * Euclidean algorithm for distance.
     * <p>Return true if points are in range,or false if is not.</p>
     * $latLng array(lat => 23.21,lng => 22.22)
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