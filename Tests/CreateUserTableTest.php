<?php

require_once 'Config/Database.php';
require_once 'Migrations/CreateUserTable.php';
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

final class CreateUserTableTest extends TestCase {

    private $create_user_table;
    private $pdo;

    protected function setUp(): void {
        // Creates a test database connection
        $this->pdo = new PDO('mysql:host=localhost;dbname=test_pogoapiphp', 'root', '');
        $database_mock = $this->createMock(Database::class);
        $database_mock->method('getPdo')->willReturn($this->pdo);
        $this->create_user_table = new CreateUserTable($database_mock);

        // Drop table users before each test
        $this->dropUsersTable();
    }

    protected function tearDown(): void {
        $this->pdo = null;
        $this->create_user_table = null;
    }

    protected function dropUsersTable(): void {
        $query = "DROP TABLE users";
        $this->pdo->exec($query);
    }

    public function doesTableExist(): bool {
        $query = "SHOW TABLES LIKE 'users'";
        $statement = $this->pdo->query($query);

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            if (in_array('users', $row)) {
                return true;
            }
        }
        return false;
    }

    #[TestDox('Doesn\'t do anything if table user already exists.')]
    public function testUpTableExistsAlready() {
        $result = $this->create_user_table->up();
        // Calls method up() a second time
        $result = $this->create_user_table->up();
        $this->assertEquals('Users table already exists.', $result);
    }

    #[TestDox('Creates table users if it doesn\'t exist already.')]
    public function testUpTableDoesntExist() {
        $result = $this->create_user_table->up();

        $query = "SHOW TABLES LIKE 'users'";
        $statement = $this->pdo->query($query);

        $does_table_exist = $this->doesTableExist();

        $this->assertEquals('Table Users has been created.', $result);
        $this->assertTrue($does_table_exist);
    }

    #[TestDox('Drops table users if it exists.')]
    public function testDownTableExists() {
        $up = $this->create_user_table->up();
        $down = $this->create_user_table->down();
        
        $does_table_exist = $this->doesTableExist();
        
        $this->assertEquals('Table Users has been deleted.', $down);
        $this->assertFalse($does_table_exist);
    }

}