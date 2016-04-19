<?php
namespace Livid\Config;

use Livid\Config\ConfigInterface;
use Livid\Config\ConfigTrait;

/**
 * Config for MySQL connections.
 *
 * @author Oliver Finn Madsen <mail@ofmadsen.com>
 */
class MySQLConfig implements ConfigInterface
{
    use ConfigTrait;

    /** @var string */
    private $host;

    /** @var string */
    private $port;

    /** @var string */
    private $unixSocket;

    /** @var string */
    private $database;

    /** @var string */
    private $charset;

    /**
     * Constructor.
     *
     * @param string $username
     * @param string $password
     * @param string[] $options
     */
    public function __construct($username = '', $password = '', array $options = [])
    {
        $this->username = $username;
        $this->password = $password;

        $this->options = $options;
    }

    /**
     * Get the dsn.
     *
     * Overwrites the method from ConfigTrait as the MySQL dsn is built on demand. Unix socket is ignored if host is
     * set.
     *
     * @return string
     */
    public function getDSN()
    {
        $parameters = [];

        if ($this->host) {
            $parameters[] = "host={$this->host}";
        }

        if ($this->port) {
            $parameters[] = "port={$this->port}";
        }

        if (!$this->host && $this->unixSocket) {
            $parameters[] = "unix_socket={$this->unixSocket}";
        }

        if ($this->database) {
            $parameters[] = "dbname={$this->database}";
        }

        if ($this->charset) {
            $parameters[] = "charset={$this->charset}";
        }

        $parameters = implode(';', $parameters);
        return "mysql:{$parameters}";
    }

    /**
     * Set the database.
     *
     * @param string $database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * Set the host.
     *
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * Set the port.
     *
     * @param string $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * Set the unix socket.
     *
     * @param string $unixSocket
     */
    public function setUnixSocket($unixSocket)
    {
        $this->unixSocket = $unixSocket;
    }

    /**
     * Set the charset.
     *
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }
}
