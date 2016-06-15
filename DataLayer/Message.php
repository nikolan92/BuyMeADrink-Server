<?php

class Message{

    public static function SuccessMessage($data){

        return array("Success"=>true,"Data"=>$data);
    }
    public static function ErrorMessage($error){
        return array("Success"=>false,"Error"=>$error);
    }
}