<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
require_once(__DIR__ . '/../Repositories/UserRepository.php');
require_once(__DIR__ . '/../Managers/UserManager.php');

class LoginController {

    private $user_repository;
    private $user_manager;

    public function __construct($user_repository, $user_manager) {
        $this->user_repository = $user_repository;
        $this->user_manager = $user_manager;
    }

    public function login() {

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['username']) && isset($data['password'])) {

            try {
                $user = $this->user_manager->loginUser($data['username'], $data['password'], $this->user_repository);
                
                header('Content-type: application/json');
                http_response_code(200);
                echo json_encode([
                    'message' => 'Login success.',
                    'token' => $this->generateJWT($user), 
                    'data' => [
                        'id' => $user->getId(),
                        'username' => $user->getUsername(),
                        'email' => $user->getEmail()
                    ]
                ]);

            } catch (Exception $e) {
                header('Content-type: application/json');
                http_response_code(401);
                echo json_encode(['message' => $e->getMessage()]);
            }

        } else {
            header('Content-type: application/json');
            http_response_code(400);
            echo json_encode(['message' => 'Credentials are missing from request body.']);
        }
    }

    public function generateJWT(User $user): string {
        $key = getEnv('JWT_SECRET_KEY');
        $iat = time();
        $payload = [
            'iat' => $iat,
            'exp' => $iat + 3600,
            'id' => $user->getId(),
            'username' => $user->getUsername()
        ];

        $jwt = JWT::encode($payload, $key, getenv('JWT_ALGORITHM'));

        return $jwt;
    }
}