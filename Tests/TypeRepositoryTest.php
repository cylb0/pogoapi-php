<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
require_once(__DIR__ . '/../Repositories/TypeRepository.php');
require_once(__DIR__ . '/../Fixtures/Fixtures.php');

final class TypeRepositoryTest extends TestCase {

    private $pdo;
    private $type_repository;
    private $fixtures;

    protected function setUp(): void {
        $this->pdo = new PDO('mysql:host=localhost;dbname=test_pogoapiphp', 'root', '');
        $database = $this->createMock(Database::class);
        $database->method('getPdo')->willReturn($this->pdo);

        $this->pdo->exec("DROP TABLE IF EXISTS types");
        $this->pdo->exec(
            'CREATE TABLE types (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name_en VARCHAR(255) NOT NULL UNIQUE,
                name_fr VARCHAR(255) UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )'
        );

        $this->type_repository = new TypeRepository($database);

        $this->fixtures = new Fixtures();
        foreach($this->fixtures->typesFixtures() as $fixture) {
            $this->pdo->exec("INSERT INTO types (name_en, name_fr) VALUES ('{$fixture['name_en']}', '{$fixture['name_fr']}')");
        }

    }

    protected function tearDown(): void
    {
        $query = "DROP TABLE IF EXISTS types";
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        $this->pdo = null;
        $this->type_repository = null;
        $this->fixtures = null;
    }

    #[TestDox('addType() saves a type in database.')]
    public function testAddTypeValidData(): void {
        $type = $this->type_repository->addType('Electric', 'Électrik');

        $statement = $this->pdo->prepare("SELECT * FROM types WHERE name_en = :name_en");
        $statement->bindParam(':name_en', $type->getNameEn());
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals($type->getNameEn(), $results['name_en']);
        $this->assertEquals($type->getNameFr(), $results['name_fr']);
        $this->assertInstanceOf(Type::class, $type);
    }

    #[TestDox('getTypeById() throws an error when provided an invalid ID.')]
    public function testGetTypeByIdInvalidId(): void {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Type ID must be a positive integer.');
        $type = $this->type_repository->getTypeById('a');
    }

    #[TestDox('getTypeById() retrieves a Type when provided a valid and existing ID.')]
    public function testGetTypByIdValidId(): void {
        $type_searched = $this->fixtures->typesFixtures()[0];
        $type = $this->type_repository->getTypeById(1);

        $this->assertInstanceOf(Type::class, $type);
        $this->assertEquals($type->getNameEn(), $type_searched['name_en']);
    }

    #[TestDox('getAllTypes() returns an array of Types.')]
    public function testGetAllTypes(): void {
        $types = $this->type_repository->getAllTypes();
        var_dump($types);
        $this->assertIsArray($types);
        $this->assertCount(3, $types);
        
        foreach ($types as $type) {
            $this->assertInstanceOf(Type::class, $type);
            $this->assertNotEmpty($type->getId());
            $this->assertNotEmpty($type->getNameEn());
        }
    }
}