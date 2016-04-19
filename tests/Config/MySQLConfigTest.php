<?php
namespace Test\Config;

use Livid\Config\MySQLConfig;
use PHPUnit_Framework_TestCase as TestCase;

class MySQLConfigTest extends TestCase
{
    private $config;

    public function setUp()
    {
        $this->config = new MySQLConfig();
    }

    public function testConstructorParameters()
    {
        $config = new MySQLConfig('username', 'password', ['options']);

        $this->assertSame('username', $config->getUsername());
        $this->assertSame('password', $config->getPassword());
        $this->assertSame(['options'], $config->getOptions());
    }

    public function testDSNWithHostAndPort()
    {
        $this->config->setHost('127.0.0.1');
        $this->config->setPort(3306);

        $dsn = $this->config->getDSN();
        $this->assertSame('mysql:host=127.0.0.1;port=3306', $dsn);
    }

    public function testDSNWithUnixSocket()
    {
        $this->config->setUnixSocket('/tmp/mysql.sock');

        $dsn = $this->config->getDSN();
        $this->assertSame('mysql:unix_socket=/tmp/mysql.sock', $dsn);
    }

    public function testDSNPrioritizesHostBeforeUnixSocet()
    {
        $this->config->setHost('127.0.0.1');
        $this->config->setUnixSocket('/tmp/mysql.sock');

        $dsn = $this->config->getDSN();
        $this->assertSame('mysql:host=127.0.0.1', $dsn);
    }

    public function testDSNWithDatabaseAndCharset()
    {
        $this->config->setDatabase('database');
        $this->config->setCharset('utf8');

        $dsn = $this->config->getDSN();
        $this->assertSame('mysql:dbname=database;charset=utf8', $dsn);
    }
}
