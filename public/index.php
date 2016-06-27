<?php

// use \Psr\Http\Message\ServerRequestInterface as Request;
// use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

//Custom errorHandler for all internal error
$c['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        //var_dump($exception);

        return $c['response']->withStatus(500)->withJSON(Message::ErrorMessage("Something went wrong,try again later."));
    };
};

$app = new \Slim\App($c);

//
require '../routes/user.php';
require '../routes/login.php';
require '../routes/updateLocation.php';

$app->run();

