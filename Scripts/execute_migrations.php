<?php

require_once(__DIR__ . '/../Config/Database.php');
require_once(__DIR__ . '/../Migrations/CreateUserTable.php');

$database = Database::getInstance();
$migrations = [
    new CreateUserTable($database)
];

foreach ($migrations as $migration) {
    echo($migration->down() . '</br>');
    echo($migration->up() . '</br>');
};