<?php

require_once('Managers/UserManager.php');
require_once('Models/User.php');
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

final class UserManagerTest extends TestCase{

    public function createDatabaseMock() {
        $database_mock = $this->createMock(Database::class);
        $pdo_mock = $this->createMock(PDO::class);
        $database_mock->method('getPdo')->willReturn($pdo_mock);
        return $database_mock;
    }

    public function createUserManager($database_mock) {
        $user_manager = new UserManager($database_mock);
        $pdo_mock = $database_mock->getPdo();
        $pdo_mock->method('prepare')->willReturn($this->createMock(PDOStatement::class));
        $pdo_mock->method('lastInsertId')->willReturn('1');
        return $user_manager;
    }

    #[TestDox('Returns a user when email is valid.')]
    public function testAddUserValidEmail() {
        $database_mock = $this->createDatabaseMock();
        $user_manager = $this->createUserManager($database_mock);
        $user = $user_manager->addUser('testUsername', 'testPassword', 'username@test.com');

        $this->assertInstanceOf(User::class, $user);
    }

    #[TestDox('Throws an exception when email is invalid.')]
    public function testAddUserInvalidEmail() {
        $database_mock = $this->createDatabaseMock();
        $user_manager = $this->createUserManager($database_mock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Email is not valid.');

        $user = $user_manager->addUser('testUsername', 'testPassword', 'invalid_email');
    }
}