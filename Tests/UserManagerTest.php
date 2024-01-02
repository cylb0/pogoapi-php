<?php

require_once (__DIR__ . '/../Config/Database.php');
require_once (__DIR__ . '/../Managers/UserManager.php');
require_once (__DIR__ . '/../Repositories/UserRepository.php');
require_once (__DIR__ . '/../Models/User.php');
require_once (__DIR__ . '/../Fixtures/Fixtures.php');
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

final class UserManagerTest extends TestCase{

    private $pdo;
    private $user_manager;
    private $user_repository;
    private $fixtures;
    
    protected function setUp(): void {
        $database_mock = $this->createMock(Database::class);
        $this->pdo = new PDO('mysql:host=localhost;dbname=test_pogoapiphp', 'root', '');
        $this->pdo->exec("DROP TABLE IF EXISTS users");
        $this->pdo->exec(
            'CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(32) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )'
        );

        $database_mock->method('getPdo')->willReturn($this->pdo);
        $this->user_repository = new UserRepository($database_mock);
        $this->fixtures = new Fixtures();
        $this->user_manager = new UserManager();

        // Add test user in database
        $user_to_insert = $this->fixtures->usersFixtures()[0];
        $user = $this->user_repository->addUser(
            $user_to_insert['username'], 
            $user_to_insert['password'], 
            $user_to_insert['email']
        );
    }

    protected function tearDown(): void {
        $this->user_manager = null;
    }

    #[TestDox('Throws an exception when username is too short.')] 
    public function testVerifyUsernameTooShortUsername(): void {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Username must be 3 characters long and can only contains letters and numbers.');
        $this->user_manager->verifyUsername('te');
    }

    #[TestDox('Throws an exception when username contains invalid characters.')] 
    public function testVerifyUsernameInvalidCharactersInUsername(): void {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Username must be 3 characters long and can only contains letters and numbers.');
        $this->user_manager->verifyUsername('test!');
    }

    #[TestDox('Doesn\'t throw any exception when username is valid.')]
    public function testVerifyUsernameValidUsername(): void {
        $this->expectNotToPerformAssertions();
        $this->user_manager->verifyUsername('test1');
    }

    #[TestDox('Throws an exception when password is too short.')]
    public function testVerifyPasswordTooShortPassword(): void {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Password should be at least 12 characters long.');
        $this->user_manager->verifyPassword('Password1!');
    }
    
    #[TestDox('Throws an exception when password doesn\'t meet requirements.')]
    public function testVerifyPasswordInvalidPassword(): void {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Password must contain at least one lowercase character, one uppercase character, one digit and one special character from @$!%*?&.');
        $this->user_manager->verifyPassword('Password1234');
    }

    #[TestDox('Doesn\'t throw any exception when password is valid.')]
    public function testVerifyPassWordValidPassword(): void {
        $this->expectNotToPerformAssertions();
        $this->user_manager->verifyPassword('Password123!');
    }

    #[TestDox('Throws an exception when email is invalid.')]
    public function testVerifyEmailInvalidEmail(): void {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Email is not valid.');
        $this->user_manager->verifyEmail('email@example');
    }

    #[TestDox('Doesn\'t throw any exception when email is valid.')]
    public function testVerifyEmailValidEmail(): void {
        $this->expectNotToPerformAssertions();
        $this->user_manager->verifyEmail('email@example.com');
    }

    #[TestDox('Throws an exception when user data is invalid.')]
    public function testVerifyUserInvalidData(): void {
        $this->expectException(Exception::class);
        $this->user_manager->verifyUser('username1!', 'Password123!', 'email@example.com');
    }

    #[TestDox('Doesn\'t throw any exception when user data is valid.')]
    public function testVerifyUserValidData(): void {
        $this->expectNotToPerformAssertions();
        $this->user_manager->verifyUser('username1', 'Password123!', 'email@example.com');
    }

    #[TestDox('Returns a user when credentials are correct.')]
    public function testLoginUserValidCredentials(): void {
        $user = $this->user_manager->loginUser('test1', 'Password123!', $this->user_repository);
        $this->assertInstanceOf(User::class, $user);
    }

    #[TestDox('Throws an error when username is invalid.')]
    public function testLoginUserInvalidUsername(): void {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No user has been found for this username.');
        $user = $this->user_manager->loginUser('test2', 'Password123!', $this->user_repository);
    }

    #[TestDox('Throws an error when password doesn\'t match.')]
    public function testLoginUserInvalidPassword(): void {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Password doesn\'t match.');
        $user = $this->user_manager->loginUser('test1', 'Password123?', $this->user_repository);
    }
}