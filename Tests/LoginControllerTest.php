<?php

use Firebase\JWT\JWT;
use Firebase\JWT\KEY;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
require_once(__DIR__ . '/../Config/Database.php');
require_once(__DIR__ . '/../Fixtures/Fixtures.php');
require_once(__DIR__ . '/../Repositories/UserRepository.php');
require_once(__DIR__ . '/../Managers/UserManager.php');
require_once(__DIR__ . '/../Models/User.php');
require_once(__DIR__ . '/../Controllers/LoginController.php');

class LoginControllerTest extends TestCase {

    private $pdo;
    private $user_repository;
    private $user_manager;
    private $fixtures;
    private $login_controller;

    public function setUp(): void {
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

        $this->login_controller = new LoginController($this->user_repository, $this->user_manager);

        // Add test user in database
        $user_to_insert = $this->fixtures->usersFixtures()[0];
        $user = $this->user_repository->addUser(
            $user_to_insert['username'], 
            $user_to_insert['password'], 
            $user_to_insert['email']
        );
    }

    public function tearDown(): void {

    }

    #[TestDox('Returns a 200 success response that contains message & user data.')]
    public function testLoginSuccess(): void {
        $user = $this->fixtures->usersFixtures()[0];

        $client = new Client();
        $response = $client->request('POST', 'http://localhost/pogoapi/index.php/login', [
            'json' => [
                'username' => $user['username'],
                'password' => 'Password123!'
            ]
        ]);

        $status = $response->getStatusCode();
        $output = $response->getBody()->getContents();
        $response_data = json_decode($output, true);

        $this->assertEquals(200, $status);
        $this->assertArrayHasKey('message', $response_data);
        $this->assertEquals('Login success.', $response_data['message']);
        $this->assertArrayHasKey('token', $response_data);
        $this->assertIsString($response_data['token']);
        $this->assertNotEmpty($response_data['token']);
        $this->assertArrayHasKey('data', $response_data);
        $this->assertArrayHasKey('id', $response_data['data']);
        $this->assertEquals(1, $response_data['data']['id']);
        $this->assertArrayHasKey('username', $response_data['data']);
        $this->assertEquals($user['username'], $response_data['data']['username']);
        $this->assertArrayHasKey('email', $response_data['data']);
        $this->assertEquals($user['email'], $response_data['data']['email']);
    }

    #[TestDox('Returns a 400 bad request when credentials are missing.')]
    public function testLoginFailedMissingCredentials(): void {
        $user = $this->fixtures->usersFixtures()[0];

        try {
            $client = new Client();
            $response = $client->request('POST', 'http://localhost/pogoapi/index.php/login', [
                'json' => [
                    'username' => $user['username']
                ]
            ]);
            $this->fail('Expected a 400 error for missing credentials but nothing happened.');
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $status = $e->getResponse()->getStatusCode();
            $output = $e->getResponse()->getBody()->getContents();
            $response_data = json_decode($output, true);
    
            $this->assertEquals(400, $status);
            $this->assertArrayHasKey('message', $response_data);
            $this->assertEquals('Credentials are missing from request body.', $response_data['message']);
        }
    }

    #[TestDox('Generates a Json Web Token that can be decoded.')]
    public function testGenerateJWT(): void {
        $user_data = $this->fixtures->usersFixtures()[0];
        $user = new User(1, $user_data['username'], $user_data['password'], $user_data['email']);
        $jwt = $this->login_controller->generateJWT($user);

        $this->assertIsString($jwt);
        $this->assertNotEmpty($jwt);

        $decoded = JWT::decode($jwt, new Key(getenv('JWT_SECRET_KEY'), getenv('JWT_ALGORITHM')));

        $this->assertObjectHasProperty('iat', $decoded);
        $this->assertObjectHasProperty('exp', $decoded);
        $this->assertObjectHasProperty('id', $decoded);
        $this->assertObjectHasProperty('username', $decoded);   
    }

}