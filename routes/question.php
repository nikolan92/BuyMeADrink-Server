<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once("../DataLayer/QuestionHandler.php");

//This route return all questions in database or message "No questions in database."
$app->get("/api/question",function (Request $request, Response $response) {

    $questionHandler = new QuestionHandler();

    $message = $questionHandler->getAllQuestions();

    return $response->withJSON($message);
});
//This route return question with specific id
$app->get("/api/question/{id}",function (Request $request, Response $response) {

    $id = $request->getAttribute("id");

    $questionHandler = new QuestionHandler();
    $message = $questionHandler->getQuestionWithSpecificID($id);

    return $response->withJSON($message);
});
//This route add new question in database
$app->post('/api/question', function (Request $request, Response $response) {

    $question = (array)json_decode($request->getBody());

    if(!empty($question)) {
        $questionHandler = new QuestionHandler();

        $message = $questionHandler->saveQuestion($question);

        return $response->withJson($message, 201);//this automatically change Header-Content-Type to :Content-Type →application/json;charset=utf-8
    }else{
        return $response->withJson(Message::ErrorMessage("Check request body."));
    }
});
//This route will delete question with specific id
$app->delete("/api/question/{questionID}/{userID}/{answerNum}",function (Request $request, Response $response) {

    $questionID = $request->getAttribute("questionID");
    $userID = $request->getAttribute("userID");
    $answerNum = $request->getAttribute("answerNum");

    $questionHandler = new QuestionHandler();

    $message = $questionHandler->tryToAnswerTheQuestion($questionID,$userID,$answerNum);

    return $response->withJSON($message);
});


