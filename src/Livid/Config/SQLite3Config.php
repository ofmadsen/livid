<?php
namespace Livid\Config;

use Livid\Config\ConfigInterface;
use Livid\Config\ConfigTrait;

/**
 * Config for SQLite3 connections.
 *
 * All settings are optional and will use an in-memory database if no filename is supplied.
 *
 * @author Oliver Finn Madsen <mail@ofmadsen.com>
 */
class SQLite3Config implements ConfigInterface
{
    use ConfigTrait;

    /**
     * Constructor.
     *
     * Set the data source name prefix to indicate that a connection to a SQLite3 database is requested.
     *
     * @param string $filename
     * @param string $username
     * @param string $password
     * @param string[] $options
     */
    public function __construct($filename = ':memory:', $username = '', $password = '', array $options = [])
    {
        $this->dsn = "sqlite:{$filename}";

        $this->username = $username;
        $this->password = $password;

        $this->options = $options;
    }
}
