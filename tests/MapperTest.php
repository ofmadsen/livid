<?php
namespace Test;

use Livid\Config\SQLite3Config;
use Livid\Database;
use Livid\Entity;
use Livid\Exception\DatabaseQueryFailed;
use Livid\Exception\InvalidFetchedEntityCount;
use Livid\Exception\UndefinedDatabase;
use Livid\Mapper;
use PHPUnit_Framework_TestCase as TestCase;
use Test\App\Horse;
use Test\App\HorseMapper;
use Test\App\HorseStable;
use Test\App\StableMapper;

class MapperTest extends TestCase
{
    private static $database;

    public static function setUpBeforeClass()
    {
        $config = new SQLite3Config(':memory:');
        self::$database = new Database($config);

        $sqls = [
            'CREATE TABLE horses (id INTEGER, stable_id INTEGER, name TEXT)',
            'INSERT INTO horses ("id", "stable_id", "name") VALUES (1, 1, "James")',
            'INSERT INTO horses ("id", "stable_id", "name") VALUES (2, 1, "Harry")',
            'INSERT INTO horses ("id", "stable_id", "name") VALUES (3, 2, "Carol")',

            'CREATE TABLE stables (id INTEGER, name TEXT)',
            'INSERT INTO stables ("id", "name") VALUES (1, "Los Santos Stable")',
            'INSERT INTO stables ("id", "name") VALUES (2, "New Jersey Stable")'
        ];

        foreach ($sqls as $sql) {
            self::$database->execute($sql);
        }

        Mapper::setDatabase(self::$database);
    }

    public function testGetWithDefinedEntity()
    {
        $mapper = new HorseMapper();
        $entity = $mapper->getWithDefinedEntity(1);

        $this->assertInstanceOf(Horse::class, $entity);
        $this->assertSame('James', $entity->getName());
    }

    public function testGetWithUndefinedEntity()
    {
        $mapper = new HorseMapper();
        $entity = $mapper->getWithUndefinedEntity();

        $this->assertInstanceOf(Entity::class, $entity);
    }

    public function testGetMoreThanOneThrowException()
    {
        $this->expectException(InvalidFetchedEntityCount::class);

        $mapper = new HorseMapper();
        $mapper->getMoreThanOne();
    }

    public function testAllWithJoinedEntity()
    {
        $mapper = new HorseMapper();
        $entities = $mapper->allWithJoinedEntity();

        $this->assertCount(3, $entities);
        $this->assertContainsOnlyInstancesOf(HorseStable::class, $entities);
        $this->assertSame('James', $entities[0]->getName());
    }

    public function testScalarValue()
    {
        $mapper = new HorseMapper();
        $value = $mapper->scalarValue();

        $this->assertEquals(3, $value);
    }

    public function testExecute()
    {
        $mapper = new HorseMapper();
        $result = $mapper->addColumnColor();

        $this->assertSame(1, $result);

        $entity = $mapper->getWithDefinedEntity(1);
        $this->assertObjectHasAttribute('color', $entity);
    }

    public function testExecutePreparedStatement()
    {
        $mapper = new HorseMapper();
        $result = $mapper->executePrepared();

        $this->assertTrue($result);
    }

    public function testSqlQuerySyntaxFailureThrowException()
    {
        $this->expectException(DatabaseQueryFailed::class);

        $mapper = new HorseMapper();
        $mapper->sqlQuerySyntaxFailure();
    }

    public function testSqlExecuteSyntaxFailureThrowException()
    {
        $this->expectException(DatabaseQueryFailed::class);

        $mapper = new HorseMapper();
        $mapper->sqlExecuteSyntaxFailure();
    }

    public function testIllegalParameterThrowException()
    {
        $this->expectException(DatabaseQueryFailed::class);

        $mapper = new HorseMapper();
        $mapper->illegalParameterFailure();
    }

    public function testAttemptToUseNotSetDatabaseThrowException()
    {
        $this->expectException(UndefinedDatabase::class);

        $repository = new StableMapper();
        $repository->getWithUndefinedEntity();
    }

    public function testUseSetDatabase()
    {
        Mapper::setDatabase(self::$database, StableMapper::DATABASE);

        $repository = new StableMapper();
        $entity = $repository->getWithUndefinedEntity();

        $this->assertInstanceOf(Entity::class, $entity);
    }
}
