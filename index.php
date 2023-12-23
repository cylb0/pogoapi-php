<?php 
require('./config/Database.php');

try {
    $database = Database::getInstance();
    $pdo = $database->getPdo();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}