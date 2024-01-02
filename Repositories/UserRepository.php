<?php

final class UserRepository {

    private $pdo;

    public function __construct(Database $database) {
        $this->pdo = $database->getPdo();
    }

    // Insert user in database
    public function addUser($username, $hashed_password, $email): User {
        try {
            $statement = $this->pdo->prepare('INSERT INTO users (username, password, email) VALUES (:username, :password, :email)');

            $statement->bindParam(':username', $username);
            $statement->bindParam(':password', $hashed_password);
            $statement->bindParam(':email', $email);
    
            $statement->execute();
            $id = $this->pdo->lastInsertId();
    
            return new User($id, $username, $hashed_password, $email);
        } catch (PDOException $e) {
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }
    
    // Retrieves a user by it's ID
    public function getUserById($userId): ?User {
        try {
            if (!is_integer($userId) || $userId < 1) {
                throw new Exception('User ID must be a positive integer.');
            }
            // Retrieves the user
            $query = "SELECT * FROM users WHERE id = :userId";
            $statement = $this->pdo->prepare($query);
            $statement->bindParam(':userId', $userId);
            $statement->execute();
    
            $result = $statement->fetch(PDO::FETCH_ASSOC);
    
            if ($result == false) {
                return null;
            }
    
            return new User($result['id'], $result['username'], $result['password'], $result['email']);
        } catch (PDOException $e) {
            throw new Exception('Database error : ' . $e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }        
    }

    // Get a user by it's Username
    public function getUserByUsername($username): ?User {
        try {
            $query = "SELECT * FROM users WHERE username = :username";
            $statement = $this->pdo->prepare($query);
            $statement->bindParam(':username', $username);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
    
            if ($result == false) {
                return null;
            }

            return new User($result['id'], $result['username'], $result['password'], $result['email']);

        } catch (PDOException $e) {
            throw new Exception('Database error : ' . $e->getMessage());
        }
    }

}