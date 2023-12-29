<?php

require_once (__DIR__ . '/../Managers/UserManager.php');
require_once (__DIR__ . '/../Models/User.php');
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

final class UserManagerTest extends TestCase{

    private $user_manager;
    
    protected function setUp(): void {
        $this->user_manager = new UserManager();
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
}