<?php
namespace Test\Config;

use Livid\Config\SQLite3Config;
use PHPUnit_Framework_TestCase as TestCase;

class SQLite3ConfigTest extends TestCase
{
    public function testDSNIsValid()
    {
        $config = new SQLite3Config('filename.sqlite3');

        $dsn = $config->getDSN();
        $this->assertSame('sqlite:filename.sqlite3', $dsn);
    }
}
