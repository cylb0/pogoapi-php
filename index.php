<?php 

require_once(__DIR__ . '/Controllers/LoginController.php');
require_once(__DIR__ . '/Managers/UserManager.php');
require_once(__DIR__ . '/Repositories/UserRepository.php');
require_once(__DIR__ . '/Config/Database.php');

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', $url);
$uri = '/' . end($segments);

switch (strtolower($uri)) {
    case '/login':

        $user_manager = new UserManager();
        $login_controller = new LoginController(new UserRepository(Database::getInstance()), $user_manager);
        $login_controller->login();
        break;
}