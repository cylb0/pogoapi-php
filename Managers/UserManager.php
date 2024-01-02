<?php

require_once(__DIR__ . '/../Models/User.php');

class UserManager {

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

    // Validates user.
    public function verifyUser($username, $password, $email): void {
        $this->verifyUsername($username);
        $this->verifyPassword($password);
        $this->verifyEmail($email);
    }

    // Hashes password and register a user in database.
    public function registerUser($username, $password, $email, UserRepository $user_repository): User {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        return $user_repository->addUser($username, $hashed_password, $email);
    }

    // Login function
    public function loginUser($username, $password, $user_repository): ?User {
        $user = $user_repository->getUserByUsername($username);

        if (!$user) {
            throw new Exception('No user has been found for this username.');
        }

        if (!password_verify($password, $user->getPassword())) {
            throw new Exception('Password doesn\'t match.');
        }

        if ($user && password_verify($password, $user->getPassword())) {
            return $user;
        }

        return null;
    }

}