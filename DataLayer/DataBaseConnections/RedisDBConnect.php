<?php
require_once("RedisDBConnect.php");

require '../vendor/predis/predis/autoload.php';

Predis\Autoloader::register();

class RedisDBConnect
{
    private $client;

    public function __construct()
    {
        $this->client = new Predis\Client();
    }
    public function getClient(){
        return $this->client;
    }
}