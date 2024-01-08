<?php

require_once(__DIR__ . '/../Config/Database.php');
require_once(__DIR__ . '/../Models/Type.php');

class TypeRepository {

    private $pdo;

    public function __construct(Database $database) {
        $this->pdo = $database->getPdo();
    }

    //Insert Type in Database
    public function addType($name_en, $name_fr = ''): Type {
        try {
            $statement = $this->pdo->prepare('INSERT INTO types (name_en, name_fr) VALUES (:name_en, :name_fr)');
            $statement->bindParam(':name_en', $name_en);
            $statement->bindParam(':name_fr', $name_fr);
            $statement->execute();
            $id = $this->pdo->lastInsertId();
    
            return new Type($id, $name_en, $name_fr);
        } catch (PDOException $e) {
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }

    // Get a type by its ID
    public function getTypeById($id): ?Type {
        try {
            if (!is_integer($id) || $id < 1) {
                throw new Exception('Type ID must be a positive integer.');
            }
            $query = "SELECT * FROM types WHERE id = :id";
            $statement = $this->pdo->prepare($query);
            $statement->bindParam(':id', $id);
            $statement->execute();

            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if ($result == false) {
                return null;
            }

            return new Type($result['id'], $result['name_en'], $result['name_fr']);
            
        } catch (PDOException $e) {
            throw new Exception('Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }
    }
}