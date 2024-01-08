<?php

require_once(__DIR__ . '/../Config/Database.php');
require_once(__DIR__ . '/../Models/Type.php');

class TypeRepository {

    private $pdo;

    public function __construct(Database $database) {
        $this->pdo = $database->getPdo();
    }

    //Insert Type in Database
    public function addType($name_en): Type {
        try {
            $statement = $this->pdo->prepare('INSERT INTO types (name_en) VALUES (:name_en)');
            $statement->bindParam(':name_en', $name_en);
            $statement->execute();
            $id = $this->pdo->lastInsertId();
    
            return new Type($id, $name_en);
        } catch (PDOException $e) {
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }
}