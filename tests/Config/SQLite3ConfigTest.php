<?php
namespace Test\Config;

use Livid\Config\SQLite3Config;
use PHPUnit_Framework_TestCase as TestCase;

class SQLite3ConfigTest extends TestCase
{
    public function testDSNIsValid()
    {
        $config = new SQLite3Config('filename.sqlite3', 'username', 'password', ['options']);

        $this->assertSame('sqlite:filename.sqlite3', $config->getDSN());
        $this->assertSame('username', $config->getUsername());
        $this->assertSame('password', $config->getPassword());
        $this->assertSame(['options'], $config->getOptions());
    }
}
