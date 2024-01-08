<?php

require_once(__DIR__ . '/../Config/Database.php');
require_once(__DIR__ . '/../Migrations/CreateUserTable.php');
require_once(__DIR__ . '/../Migrations/CreateTypeTable.php');
require_once(__DIR__ . '/../Fixtures/Fixtures.php');
require_once(__DIR__ . '/../Models/User.php');
require_once(__DIR__ . '/../Models/Type.php');

try {
    $database = Database::getInstance();
    $migrations = [
        new CreateUserTable($database),
        new CreateTypeTable($database)
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

    //Insertion des fixtures type
    foreach($fixtures->typesFixtures() as $fixture) {
        $database->getPdo()->exec("INSERT INTO types (name_en, name_fr) VALUES ('{$fixture['name_en']}', '{$fixture['name_fr']}')");
        $id = $database->getPdo()->lastInsertId();
        $type = new Type($id, $fixture['name_en'], $fixture['name_fr']);
        $name_en = $type->getNameEn();
        echo ("Record for type $name_en has been created.<br>");
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}