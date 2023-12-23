<?php

require("EnvManager.php");

final class Database {

    private $pdo;
    private static $instance = null;

    public function connect() {
        try {
            $dsn = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME');;
            $this->pdo = new PDO(
                $dsn, 
                getenv('DB_USER'), 
                getenv('DB_PASSWORD')
            );
        } catch (PDOException $error) {
            throw new Exception("Connection failed : " . $error->getMessage());
        }
    }
    
    // Singleton
    public static function getInstance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
            self::$instance->connect();
        }
        return self::$instance;
    }

    public function getPdo() {
        return $this->pdo;
    }
}