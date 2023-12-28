<?php

final class CreateUserTable {
    
    private $pdo;

    public function __construct(Database $database) {
        $this->pdo = $database->getPdo();
    }

    public function up() {

        $check_query = "SHOW TABLES LIKE 'users'";
        $results = $this->pdo->query($check_query);
        if ($results->rowCount() > 0) {
            return('Users table already exists.');
        }

        $query = "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(32) NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        $this->pdo->exec($query);

        return('Table Users has been created.');
    }

    public function down() {
        $query = "DROP TABLE IF EXISTS users";
        $this->pdo->exec($query);

        return('Table Users has been deleted.');
    }

}