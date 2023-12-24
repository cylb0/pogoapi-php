<?php 
require('./Managers/UserManager.php');

try {
    $user_manager = new UserManager();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}