<?php

final class CreateTypeTable {

    private $pdo;

    public function __construct(Database $database) {
        $this->pdo = $database->getPdo();
    }

    public function up() {
        $check_query = "SHOW TABLES LIKE 'types'";
        $results = $this->pdo->query($check_query);
        if ($results->rowCount() > 0) {
            return('Types table already exists.');
        }

        $query = "CREATE TABLE types (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name_en VARCHAR(255) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        $this->pdo->exec($query);
        
        return('Table Types has been created.');
    }

    public function down() {
        $query = "DROP TABLE IF EXISTS types"; 
        $this->pdo->exec($query);
    }

}