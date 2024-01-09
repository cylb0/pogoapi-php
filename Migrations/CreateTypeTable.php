<?php

/**
 * Represents a database migration responsible for creating and dropping tables "types" and "type_effective".
 * 
 * It is responsible for setting up "types" table as well as the many to many relationships related to types effectiveness. 
 * 
 * @property PDO $pdo The instance of PDO used for database operations.
 */
final class CreateTypeTable {

    private $pdo;
    
    /**
     * __construct
     *
     * @param  Database $database The Database instance that provides the PDO connection.
     * @return void
     */
    public function __construct(Database $database) {
        $this->pdo = $database->getPdo();
    }
    
    /**
     * Executes a migration that creates tables "types" and "type_effective".
     *
     * @return string A message that indicates the result of the migration.
     * @return PDOException If an error occurs while creating tables.
     */
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
                name_fr VARCHAR(255) UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            $this->pdo->exec($query);

            $query_type_effective = "CREATE TABLE type_effective (
                id INT AUTO_INCREMENT PRIMARY KEY,
                type_id INT NOT NULL,
                strong_against_id INT NOT NULL,
                FOREIGN KEY (type_id) REFERENCES types(id),
                FOREIGN KEY (strong_against_id) REFERENCES types(id)
            )";
            $this->pdo->exec($query_type_effective);
    
            return('Tables Types & Type_effective have been created.');
        } catch (PDOException $e) {
            return 'Database error while creating Types table: ' . $e->getMessage();
        }
    }
    
    /**
     * Executes a migration to delete tables "type_effective" and "types".
     *
     * @return string A message indicating the result of the migration.
     * @throws PDOException If an error occurs while deleting tables.
     */
    public function down() {
        try {
            $query = "DROP TABLE IF EXISTS type_effective"; 
            $this->pdo->exec($query);
            $query = "DROP TABLE IF EXISTS types";
            $this->pdo->exec($query);
            return('Tables Types & Type_effective have been deleted.');
        } catch (PDOException $e) {
            return 'Database error while dropping Types table: ' . $e->getMessage();
        }
    }

}