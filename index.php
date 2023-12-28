<?php 
require(__DIR__ . '/Managers/UserManager.php');
require_once(__DIR__ . '/Config/Database.php');
require_once(__DIR__ . '/Migrations/CreateUserTable.php');

try {
    $database = Database::getInstance();
    $migration = new CreateUserTable($database);
    echo($migration->down());
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}