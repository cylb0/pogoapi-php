    <?php

    class LoginController {

        private $user_repository;
        private $user_manager;

        public function __construct($user_repository, $user_manager) {
            $this->user_repository = $user_repository;
            $this->user_manager = $user_manager;
        }

        public function login() {

            require_once(__DIR__ . '/../Repositories/UserRepository.php');
            require_once(__DIR__ . '/../Config/Database.php');
            require_once(__DIR__ . '/../Managers/UserManager.php');

            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($data['username']) && isset($data['password'])) {

                try {
                    $user = $this->user_manager->loginUser($data['username'], $data['password'], $this->user_repository);
                    
                    header('Content-type: application/json');
                    http_response_code(200);
                    echo json_encode(['message' => 'Login success.', 'data' => [
                        'id' => $user->getId(),
                        'username' => $user->getUsername(),
                        'email' => $user->getEmail()
                    ]]);

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
    }