<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once("../DataLayer/UserHandler.php");

$app->post('/api/login', function (Request $request, Response $response) {
   
    $userData = (array)json_decode($request->getBody());

    $userHandler = new UserHandler();

    if(!(isset($userData["email"]) && isset($userData["password"])))
        return $response->withJSON(Message::ErrorMessage("Wrong request check request body"));

    $message = $userHandler->logInUser($userData["email"],$userData["password"]);

	return $response->withJson($message, 201);//this automatically change Header-Content-Type to :Content-Type →application/json;charset=utf-8
});