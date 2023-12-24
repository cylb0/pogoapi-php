<?php

require_once(__DIR__ . '/../Config/Database.php');

final class UserManager {

    private $pdo;

    public function __construct(Database $database) {
        $this->pdo = $database->getPdo();
    }

    public function addUser($username, $password, $email) {
        try {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email is not valid.');
            }
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $statement = $this->pdo->prepare('INSERT INTO users (username, password, email) VALUES (:username, :password, :email)');
    
            $statement->bindParam(':username', $username);
            $statement->bindParam(':password', $hashed_password);
            $statement->bindParam(':email', $email);
    
            $statement->execute();
            $id = $this->pdo->lastInsertId();
    
            return new User($id, $username, $hashed_password, $email);
        } catch (PDOException $e) {
            throw new Exception('Database error : ' . $e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }
    }
    
}