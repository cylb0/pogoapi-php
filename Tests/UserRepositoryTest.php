<?php


use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

final class UserRepositoryTest extends TestCase {

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
        $this->user_manager = new UserManager($database_mock);
    }

    protected function tearDown(): void {
        $query = "DROP TABLE users";
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        $this->pdo = null;
        $this->user_manager = null;
    }

    

    // #[TestDox('Saves a user in database.')]
    // public function testInsertUserValidData(): void {
    //     $username = 'test1';
    //     $password = password_hash('Password123!', PASSWORD_DEFAULT);
    //     $email = 'email@example.com';
    //     $user = $this->user_manager->insertUser($username, $password, $email);

    //     $statement = $this->pdo->prepare('SELECT * FROM users WHERE username = :username');
    //     $statement->bindParam(':username', $username);
    //     $statement->execute();
    //     $results = $statement->fetch(PDO::FETCH_ASSOC);

    //     $this->assertEquals($username, $results['username']);
    //     $this->assertInstanceOf(User::class, $user);
    // }

    // #[TestDox('Returns the user it finds by its ID.')]
    // public function testGetUserByIdExistingUserId(): void {
    //     $user_to_insert = $this->fixtures->usersFixtures()[0];
    //     $user = $this->user_manager->addUser(
    //         $user_to_insert['username'],
    //         $user_to_insert['password'],
    //         $user_to_insert['email']);
    //     $user_retrieved = $this->user_manager->getUserById($user->getId());

    //     $this->assertEquals($user->getId(), $user_retrieved->getId());
    // }

    // #[TestDox('Returns null when it doesn\'t find a user ID.')]
    // public function testGetUserByIdNonExistingUserId(): void {
    //     $user_retrieved = $this->user_manager->getUserById(999);

    //     $this->assertNull($user_retrieved);
    // }

    // #[TestDox('Throws an Exception when getUserByID is provided invalid ID.')]
    // public function testGetUserByIdInvalidUserId(): void {

    //     $this->expectException(Exception::class);
    //     $this->expectExceptionMessage('User ID must be a positive integer.');
    //     $user_retrieved = $this->user_manager->getUserById('a');
    // }

}