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
        $fixtures = $this->fixtures->usersFixtures();
        foreach ($fixtures as $user) {
            $this->pdo->exec("INSERT INTO users (username, password, email) VALUES ('{$user['username']}', '{$user['password']}', '{$user['email']}')");
        }        
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
        $user = $this->user_repository->addUser(
            'testUsername', 
            '$2y$10$ZkGUrwh8E3NrF/jYYnPmdeexfdA/a.cmaL5x4iX/mZZGBJhkldW.O', 
            'test@email.com'
        );

        $statement = $this->pdo->prepare('SELECT * FROM users WHERE username = :username');
        $statement->bindParam(':username', $user->getUsername());
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals($user->getUsername(), $results['username']);
        $this->assertInstanceOf(User::class, $user);
    }

    #[TestDox('Returns the user it finds by its ID.')]
    public function testGetUserByIdExistingUserId(): void {
        $user_retrieved = $this->user_repository->getUserById(1);

        $this->assertInstanceOf(User::class, $user_retrieved);
        $this->assertEquals($user_retrieved->getId(), 1);
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
        $user_retrieved = $this->user_repository->getUserByUsername('test1');

        $this->assertInstanceOf(User::class, $user_retrieved);
        $this->assertEquals('test1', $user_retrieved->getUsername());
    }    

    #[TestDox('Returns null when username doesn\'t exists.')]
    public function testGetUserByUsernameNonExistingUser(): void {
        $user_retrieved = $this->user_repository->getUserByUsername('test3');

        $this->assertNull($user_retrieved);
    }
    
    #[TestDox('Returns an array of Users.')]
    public function testGetAllUsers(): void {
        $users_retrieved = $this->user_repository->getAllUsers();

        $this->assertCount(count($this->fixtures->usersFixtures()), $users_retrieved);
        foreach($users_retrieved as $user) {
            $this->assertInstanceOf(User::class, $user);
        }
    }

    #[TestDox('Modifies a user\'s username.')]
    public function testUpdateUserValidUser(): void {
        $old_user = $this->fixtures->usersFixtures()[0];
        $user = $this->user_repository->updateUser(1, 'newtest1', $old_user['password'], $old_user['email']);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('newtest1', $user->getUsername());
        $this->assertEquals($old_user['password'], $user->getPassword());
        $this->assertEquals($old_user['email'], $user->getEmail());
    }

    #[TestDox('Returns null when trying to update a non-existing user.')]
    public function testUpdateUserNonExistingUser(): void {
        $user = $this->user_repository->updateUser(3, 'newtest3', 'Password123!', 'test@email.com');
        
        $this->assertNull($user);
    }

}





    

