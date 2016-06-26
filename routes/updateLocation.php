<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once("../DataLayer/LocationHelper.php");


$app->post("/api/updateLocation",function (Request $request, Response $response) {

    $data =(array) json_decode($request->getBody());

    if(!empty($data)) {

        $locationHelper = new LocationHelper();
        //$locationHelper->updateUserLocationAndReturnNearbyObjects($data);

        return $response->withJSON($locationHelper->updateUserLocationAndReturnNearbyObjects($data));

    }else{
        return $response->withJSON(Message::ErrorMessage("Check request body."));
    }
});