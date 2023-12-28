<?php

require_once(__DIR__ . '/../Config/Database.php');

final class UserManager {

    private $pdo;

    public function __construct(Database $database) {
        $this->pdo = $database->getPdo();
    }

    // Verify username requirements with regex.
    public function verifyUsername($username): void {
        if (!preg_match('/^[a-zA-z0-9]{3,}$/', $username)) {
            throw new Exception('Username must be 3 characters long and can only contains letters and numbers.');
        }
    }

    // Verify password requirements with strlen and regex.
    public function verifyPassword($password): void {
        if (strlen($password) < 12) {
            throw new Exception('Password should be at least 12 characters long.');
        }

        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[a-zA-Z\d@$!%*?&]+$/', $password)) {
            throw new Exception('Password must contain at least one lowercase character, one uppercase character, one digit and one special character from @$!%*?&.');
        }
    }

    // Verify email with FILTER.
    public function verifyEmail($email): void {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email is not valid.');
        }
    }

    // Validates user
    public function verifyUser($username, $password, $email): void {
        $this->verifyUsername($username);
        $this->verifyPassword($password);
        $this->verifyEmail($email);
    }

    // Insert user in database
    public function insertUser($username, $hashed_password, $email): User {
        $statement = $this->pdo->prepare('INSERT INTO users (username, password, email) VALUES (:username, :password, :email)');

        $statement->bindParam(':username', $username);
        $statement->bindParam(':password', $hashed_password);
        $statement->bindParam(':email', $email);

        $statement->execute();
        $id = $this->pdo->lastInsertId();

        return new User($id, $username, $hashed_password, $email);
    }

    // Validates User, hashes password and adds User to database.
    public function addUser($username, $password, $email) {
        try {
            $this->verifyUser($username, $password, $email);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            return $this->insertUser($username, $hashed_password, $email);
        } catch (PDOException $e) {
            throw new Exception('Database error : ' . $e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }
    }
    
}