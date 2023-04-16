<?php

namespace App\Database;

use PDO;
use PDOStatement;

class DB
{

    protected $db;
    private array $config;

    public function __construct()
    {
        $this->config = require_once 'app/config/database.php';
        $this->db = new PDO("mysql:host={$this->config['host']};dbname={$this->config['dbname']};charset=utf8",
            $this->config['dbuser'],
            $this->config['dbpass']);
    }

    private function exec($query, $bindings = []): false|PDOStatement
    {
        $dbRequest = $this->db->prepare($query);
        foreach ($bindings as $name => &$bind) {
            $pdoType = match (true) {
                is_int($bind) => PDO::PARAM_INT,
                default => PDO::PARAM_STR
            };
            $dbRequest->bindParam(":$name", $bind, $pdoType);
        }
        $dbRequest->execute();
        return $dbRequest;
    }

    public function execute($query, $bindings = []): bool
    {
        $result = self::exec($query, $bindings);
        return (bool) $result;
    }

    public function fetch($query, $bindings = [])
    {
        $results = self::exec($query, $bindings, $fetchAll = true);
        return $fetchAll ? $results->fetchAll(PDO::FETCH_ASSOC) : $results->fetch();
    }


}