<?php

namespace Palmo\Core\service;

use PDO;

class Db{
    private PDO $handler;

    public function __construct(){
        $config = new Config();
        $dbConfig = $config->get('db');

        $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']}";

        $this->handler = new PDO($dsn, $dbConfig['user'], $dbConfig['password']);
    }

    public function getHandler(): PDO
    {
        return $this->handler;
    }
}
