<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\Stream;

require_once("../DataLayer/UserHandler.php");

//This route return user with specific id
$app->get("/api/user",function (Request $request, Response $response) {

    $userHandler = new UserHandler();

    $message = $userHandler->getAllUsers();

    return $response->withJSON($message);
});
//This route return user with specific id
$app->get("/api/user/{id}",function (Request $request, Response $response) {

    $id = $request->getAttribute("id");

    $userHandler = new UserHandler();

    $message = $userHandler->getUserWithSpecificID($id);

    return $response->withJSON($message);
});
//This route return all user friends
$app->get("/api/user/friends/{id}",function (Request $request, Response $response) {

    $id = $request->getAttribute("id");

    $userHandler = new UserHandler();
    $message = $userHandler->getUserFriends($id);

    return $response->withJSON($message);
});

//Route for new user expect json data with user
$app->post("/api/user",function (Request $request, Response $response) {

    $user =(array) json_decode($request->getBody());
    if(!empty($user)) {
        $userHandler = new UserHandler();

        $message = $userHandler->saveUser($user);

        if ($message["Success"])
            return $response->withJSON($message, 201);
        else
            return $response->withJSON($message);
    }else{
        return $response->withJSON(Message::ErrorMessage("Check request body."));
    }
});

//This route update existing user if is id correct
$app->put("/api/user",function (Request $request, Response $response) {

    $user = (array)json_decode($request->getBody());
    if(!empty($user)){
        $userHandler = new UserHandler();

        //saving image on server file sistem
        if(isset($user["image_base64"]))
        {

            $base64String = $user["image_base64"];
            $decodedImage = base64_decode("$base64String");

            $userId = $user["_id"];

            $root = $_SERVER['DOCUMENT_ROOT'];
            $imagePath = $root . "/images/users/". $userId .".jpg";

            //Save image on file system
            file_put_contents($imagePath,$decodedImage);
            //remove field
            unset($user["image_base64"]);
        }

        $message = $userHandler->updateUser($user);
        return $response->withJSON($message);

    }else{
        return $response->withJSON(Message::ErrorMessage("Check request body."));
    }

});


$app->post('/api/test', function (Request $request, Response $response) {

    //$body = $request->getBody();
    //$response->getBody()->write("hello, " . $id);

    //$newResponse =  $response->withStatus(201);//change status code

    // if(isset($_POST["JSONData"]))
    // 	echo var_dump($_POST["JSONData"]);
    //$data = array('name' => 'Rob', 'age' => 40);
    //$newResponse = $response->withJson($data, 201);//this automaticly change Header-Content-Type to :Content-Type →application/json;charset=utf-8
    //return $newResponse;
//=========================================================================


    $body = $request->getBody();
    $body = json_decode($body);

//    echo json_encode($body);
    $message = array("Success"=> true ,"Error"=>"No error.");

    $newResponse = $response->withJSON($body,201);

//    echo extension_loaded("mongo") ? "loaded\n" : "not loaded\n";

    $userHandler = new UserHandler();
    //$user = new User("nikola","Nikolic","nikolan92hotmail.com");
    //$user = new User.php("nikola","Nikolic","nikolan92hotmail.com");
    //$userHandler->saveUser(new User.php());

    $user = User::newUser("Nikola Nikolic","nikolan92@hotmail.com");


    //$user = $userHandler->saveUser($user);

    //$newResponse = $response->withJSON(array("user"=>$user,"_id"=>$user["_id"]),201);
    //var_dump($user);
//    echo extension_loaded("mongodb") ? "loaded\n" : "not loaded\n";
    return $newResponse;
    //var_dump($body);
});
