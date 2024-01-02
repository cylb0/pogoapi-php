<?php

require_once (__DIR__ . '/../Config/Database.php');
require_once (__DIR__ . '/../Models/User.php');
require_once (__DIR__ . '/../Repositories/UserRepository.php');
require_once (__DIR__ . '/../Fixtures/Fixtures.php');
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

final class UserRepositoryTest extends TestCase {

    private $pdo;
    private $user_repository;
    private $fixtures;

    protected function setUp(): void {
        $database_mock = $this->createMock(Database::class);
        $this->pdo = new PDO('mysql:host=localhost;dbname=test_pogoapiphp', 'root', '');
        $this->pdo->exec("DROP TABLE IF EXISTS users");
        $this->pdo->exec(
            'CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(32) NOT NULL,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )'
        );
        $database_mock->method('getPdo')->willReturn($this->pdo);
        $this->user_repository = new UserRepository($database_mock);
        $this->fixtures = new Fixtures();
    }

    protected function tearDown(): void {
        $query = "DROP TABLE users";
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        $this->pdo = null;
        $this->user_repository = null;
    }

    #[TestDox('Saves a user in database.')]
    public function testInsertUserValidData(): void {
        $user_to_insert = $this->fixtures->usersFixtures()[0];
        $user = $this->user_repository->addUser(
            $user_to_insert['username'], 
            $user_to_insert['password'], 
            $user_to_insert['email']
        );

        $statement = $this->pdo->prepare('SELECT * FROM users WHERE username = :username');
        $statement->bindParam(':username', $user_to_insert['username']);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals($user_to_insert['username'], $results['username']);
        $this->assertInstanceOf(User::class, $user);
    }

    #[TestDox('Returns the user it finds by its ID.')]
    public function testGetUserByIdExistingUserId(): void {
        $user_to_insert = $this->fixtures->usersFixtures()[0];
        $user = $this->user_repository->addUser(
            $user_to_insert['username'],
            $user_to_insert['password'],
            $user_to_insert['email']);

        $user_retrieved = $this->user_repository->getUserById($user->getId());

        $this->assertEquals($user->getId(), $user_retrieved->getId());
    }

    #[TestDox('Returns null when it doesn\'t find a user ID.')]
    public function testGetUserByIdNonExistingUserId(): void {
        $user_retrieved = $this->user_repository->getUserById(999);

        $this->assertNull($user_retrieved);
    }
    
    #[TestDox('Throws an Exception when getUserByID is provided invalid ID.')]
    public function testGetUserByIdInvalidUserId(): void {

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('User ID must be a positive integer.');
        $user_retrieved = $this->user_repository->getUserById('a');
    }

    #[TestDox('Returns a User when username exists.')]
    public function testGetUserByUsernameExistingUser(): void {
        $user_to_insert = $this->fixtures->usersFixtures()[0];
        $user = $this->user_repository->addUser(
            $user_to_insert['username'],
            $user_to_insert['password'],
            $user_to_insert['email']);

        $user_retrieved = $this->user_repository->getUserByUsername('test1');

        $this->assertEquals($user->getUsername(), $user_retrieved->getUsername());
    }    

    #[TestDox('Returns null when username doesn\'t exists.')]
    public function testGetUserByUsernameNonExistingUser(): void {
        $user_retrieved = $this->user_repository->getUserByUsername('test2');

        $this->assertNull($user_retrieved);
    }    
    
}





    

