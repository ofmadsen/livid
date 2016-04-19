<?php
namespace Test;

use Livid\Config\SQLite3Config;
use Livid\Database;
use Livid\Exception\DatabaseConnectionFailed;
use PHPUnit_Framework_TestCase as TestCase;

class DatabaseTest extends TestCase
{
    public function testDatabaseConnectionFailedThrowException()
    {
        $this->expectException(DatabaseConnectionFailed::class);

        $config = new SQLite3Config('.');
        $database = new Database($config);

        $database->execute('SQL');
    }

    public function testCreateNewInstance()
    {
        $config = new SQLite3Config(':memory:');
        $database = new Database($config);

        $database->execute('CREATE TABLE test (id INTEGER)');
    }
}
