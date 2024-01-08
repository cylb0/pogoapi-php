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
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )'
        );
    }

    protected function tearDown(): void {
        $this->dropTypesTable();
        $this->pdo = null;
        $this->create_type_table = null;
    }

    public function dropTypesTable(): void {
        $query = "DROP TABLE IF EXISTS types";
        $this->pdo->exec($query);
    }

    public function doesTableExist(): bool {
        $query = "SHOW TABLES LIKE 'types'";
        $statement = $this->pdo->query($query);

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            if (in_array('types', $row)) {
                return true;
            }
        }

        return false;
    }

    #[TestDox('Doesn\'t do anything if table already exists.')]
    public function testUpTableAlreadyExists(): void {
        $does_table_exists = $this->doesTableExist();
        $this->assertTrue($does_table_exists);
        $result = $this->create_type_table->up();
        $this->assertEquals($result, 'Types table already exists.');
    }

    #[TestDox('Creates table types if it doesnt exist already.')]
    public function testUpTableDoesntExist(): void {
        $this->dropTypesTable();
        $does_table_exists = $this->doesTableExist();
        $this->assertFalse($does_table_exists);

        $result = $this->create_type_table->up();
        $does_table_exists = $this->doesTableExist();
        $this->assertTrue($does_table_exists);
        $this->assertEquals($result, 'Table Types has been created.');

    }

    #[TestDox('Drops table types if it exists.')]
    public function testDownTableExists(): void {
        $does_table_exists = $this->doesTableExist();
        $this->assertTrue($does_table_exists);

        $result = $this->create_type_table->down();
        $does_table_exists = $this->doesTableExist();
        $this->assertFalse($does_table_exists);
        $this->assertEquals($result, 'Table Types has been deleted.');
    }

}