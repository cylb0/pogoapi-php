<?php

require_once(__DIR__ . '/../Config/Database.php');
require_once(__DIR__ . '/../Migrations/CreateUserTable.php');
require_once(__DIR__ . '/../Fixtures/Fixtures.php');
require_once(__DIR__ . '/../Models/User.php');

try {
    $database = Database::getInstance();
    $migrations = [
        new CreateUserTable($database)
    ];
    
    foreach ($migrations as $migration) {
        echo($migration->down() . '</br>');
        echo($migration->up() . '</br>');
    };
    
    $fixtures = new Fixtures();
    
    // Insertion des fixtures users
    foreach ($fixtures->usersFixtures() as $fixture) {
        $database->getPdo()->exec("INSERT INTO users (username, password, email) VALUES ('{$fixture['username']}', '{$fixture['password']}', '{$fixture['email']}')");
        $id = $database->getPdo()->lastInsertId();
        $user = new User($id, $fixture['username'], $fixture['password'], $fixture['email']);
        $username = $user->getUsername();
        echo ("Record for user $username has been created." . "</br>");
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}