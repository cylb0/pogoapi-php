<?php

final class CreateTypeTable {

    private $pdo;

    public function __construct(Database $database) {
        $this->pdo = $database->getPdo();
    }

    public function up() {
        try {
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
        } catch (PDOException $e) {
            return 'Database error while creating Types table: ' . $e->getMessage();
        }
    }

    public function down() {
        try {
            $query = "DROP TABLE IF EXISTS types"; 
            $this->pdo->exec($query);
            return('Table Types has been deleted.');
        } catch (PDOException $e) {
            return 'Database error while dropping Types table: ' . $e->getMessage();
        }
    }

}