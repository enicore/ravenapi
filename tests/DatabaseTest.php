<?php

use PHPUnit\Framework\TestCase;
use Enicore\RavenApi\Database;
use PDO;
use PDOStatement;
use ReflectionClass;

class DatabaseTest extends TestCase
{
    protected Database $database;
    protected $pdoMock;
    protected $statementMock;

    protected function setUp(): void
    {
        // Create mocks for PDO and PDOStatement
        $this->pdoMock = $this->createMock(PDO::class);
        $this->statementMock = $this->createMock(PDOStatement::class);

        // Use reflection to create a Database instance without calling the constructor
        $reflection = new ReflectionClass(Database::class);
        $this->database = $reflection->newInstanceWithoutConstructor();

        // Inject the mocked PDO into the Database instance
        $pdoProperty = $reflection->getProperty('pdo');
        $pdoProperty->setAccessible(true);
        $pdoProperty->setValue($this->database, $this->pdoMock);
    }

    public function testQuerySuccess()
    {
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM table')
            ->willReturn($this->statementMock);

        $this->statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([])
            ->willReturn(true);

        $this->assertSame($this->statementMock, $this->database->query('SELECT * FROM table'));
    }

    public function testQueryFailure()
    {
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM table')
            ->willReturn(false);

        $this->assertFalse($this->database->query('SELECT * FROM table'));
    }

    public function testAll()
    {
        $result = [['id' => 1, 'name' => 'John Doe']];

        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM table')
            ->willReturn($this->statementMock);

        $this->statementMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->statementMock
            ->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($result);

        $this->assertEquals($result, $this->database->all('SELECT * FROM table'));
    }

    public function testRow()
    {
        $result = ['id' => 1, 'name' => 'John Doe'];

        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM table WHERE id = :id')
            ->willReturn($this->statementMock);

        $this->statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':id' => 1])
            ->willReturn(true);

        $this->statementMock
            ->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($result);

        $this->assertEquals($result, $this->database->row('SELECT * FROM table WHERE id = :id', [':id' => 1]));
    }

    public function testGetLastInsertId()
    {
        $this->pdoMock
            ->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('42');

        $this->assertEquals('42', $this->database->getLastInsertId());
    }

    public function testGetLastError()
    {
        $this->pdoMock
            ->expects($this->once())
            ->method('errorInfo')
            ->willReturn([null, null, 'Error message']);

        $this->assertEquals('Error message', $this->database->getLastError());
    }

    public function testExists()
    {
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `table` WHERE id = :id')
            ->willReturn($this->statementMock);

        $this->statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':id' => 1])
            ->willReturn(true);

        $this->statementMock
            ->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_COLUMN)
            ->willReturn(1);

        $this->assertTrue($this->database->exists('table', 'id = :id', [':id' => 1]));
    }

    public function testCount()
    {
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT COUNT(*) FROM `table` WHERE id = :id')
            ->willReturn($this->statementMock);

        $this->statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':id' => 1])
            ->willReturn(true);

        $this->statementMock
            ->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_COLUMN)
            ->willReturn(5);

        $this->assertEquals(5, $this->database->count('table', 'id = :id', [':id' => 1]));
    }

    public function testInsert()
    {
        $data = ['name' => 'John Doe', 'age' => 30];

        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO `table` (name,age) VALUES (:name,:age)')
            ->willReturn($this->statementMock);

        $this->statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':name' => 'John Doe', ':age' => 30])
            ->willReturn(true);

        $this->assertSame($this->statementMock, $this->database->insert('table', $data));
    }

    public function testUpdate()
    {
        $data = ['name' => 'Jane Doe'];
        $whereParams = ['id' => 1];

        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `table` SET name=:name WHERE id=:id')
            ->willReturn($this->statementMock);

        $this->statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':name' => 'Jane Doe', ':id' => 1])
            ->willReturn(true);

        $this->assertSame($this->statementMock, $this->database->update('table', $data, $whereParams));
    }

    public function testDelete()
    {
        $whereParams = ['id' => 1];

        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM `table` WHERE id=:id')
            ->willReturn($this->statementMock);

        $this->statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':id' => 1])
            ->willReturn(true);

        $this->assertSame($this->statementMock, $this->database->delete('table', $whereParams));
    }
}
