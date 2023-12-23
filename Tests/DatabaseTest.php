<?php

require 'Config/Database.php';
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

final class DatabaseTest extends TestCase {

    #[TestDox('Database singleton works correctly.')]
    public function testGetInstance() {

        $db1 = Database::getInstance();
        $db2 = Database::getInstance();
        
        $this->assertInstanceOf(Database::class, $db1);
        $this->assertInstanceOf(Database::class, $db2);
        $this->assertSame($db1, $db2);
        
    }
    #[TestDox('getPdo() returns an instance of PDO.')]
    public function testGetPdo() {

        $pdo = Database::getInstance()->getPdo();

        $this->assertInstanceOf(PDO::class, $pdo);

    }

}