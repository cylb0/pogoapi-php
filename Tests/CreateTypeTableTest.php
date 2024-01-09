<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
require_once(__DIR__ . '/../Config/Database.php');
require_once(__DIR__ . '/../Migrations/CreateTypeTable.php');

final class CreateTypeTableTest extends TestCase {

    private $create_type_table;
    private $pdo;

    protected function setUp(): void {
        // Creates a test database connection
        $this->pdo = new PDO('mysql:host=localhost;dbname=test_pogoapiphp', 'root', '');
        $database_mock = $this->createMock(Database::class);
        $database_mock->method('getPdo')->willReturn($this->pdo);
        $this->create_type_table = new CreateTypeTable($database_mock);

        // Creates types table
        $this->pdo->exec(
            'CREATE TABLE types (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name_en VARCHAR(255) NOT NULL UNIQUE,
                name_fr VARCHAR(255) UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )'
        );

        // Create type_effective table for many to many relation on effectiveness
        $this->pdo->exec(
            'CREATE TABLE type_effective (
                id INT AUTO_INCREMENT PRIMARY KEY,
                type_id INT NOT NULL,
                strong_against_id INT NOT NULL,
                FOREIGN KEY (type_id) REFERENCES types(id),
                FOREIGN KEY (strong_against_id) REFERENCES types(id)
            )'
        );
    }

    protected function tearDown(): void {
        $this->dropTypesTable(['type_effective', 'types']);
        $this->pdo = null;
        $this->create_type_table = null;
    }

    public function dropTypesTable(array $table_names): void {
        foreach ($table_names as $table) {
            $query = "DROP TABLE IF EXISTS $table";
            $statement = $this->pdo->prepare($query);
            $statement->execute();
        }
    }

    public function doTablesExist(array $table_names): bool {
        $query = "SHOW TABLES";
        $statement = $this->pdo->query($query);

        $existing_tables = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $db_name = getenv('DB_NAME_TEST');
            $existing_tables[] = $row["Tables_in_$db_name"];
        }

        foreach ($table_names as $table) {
            if (!in_array($table, $existing_tables)) {
                return false;
            }
        }

        return true;
    }

    #[TestDox('Doesn\'t do anything if tables already exist.')]
    public function testUpTableAlreadyExists(): void {
        $do_tables_exist = $this->doTablesExist(['type_effective', 'types']);
        $this->assertTrue($do_tables_exist);
        $result = $this->create_type_table->up();
        $this->assertEquals($result, 'Types table already exists.');
    }

    #[TestDox('Creates table types and type_effective if they don\'t exist already.')]
    public function testUpTableDoesntExist(): void {
        $this->dropTypesTable(['type_effective', 'types']);
        $do_tables_exist = $this->doTablesExist(['type_effective', 'types']);
        $this->assertFalse($do_tables_exist);

        $result = $this->create_type_table->up();
        $do_tables_exist = $this->doTablesExist(['type_effective', 'types']);
        $this->assertTrue($do_tables_exist);
        $this->assertEquals($result, 'Tables Types & Type_effective have been created.');

    }

    #[TestDox('Drops tables types and type_effective if they exist.')]
    public function testDownTableExists(): void {
        $do_tables_exist = $this->doTablesExist(['type_effective', 'types']);
        $this->assertTrue($do_tables_exist);

        $result = $this->create_type_table->down();
        $do_tables_exist = $this->doTablesExist(['type_effective', 'types']);
        $this->assertFalse($do_tables_exist);
        $this->assertEquals($result, 'Tables Types & Type_effective have been deleted.');
    }

}