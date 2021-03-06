<?php
namespace Livid;

use Exception;
use Livid\Config\ConfigInterface;
use Livid\Exception\DatabaseConnectionFailed;
use Livid\Exception\DatabaseQueryFailed;
use Livid\Query;
use PDO;
use PDOException;

/**
 * Database class containing the database connection.
 *
 * This wrapper class must be injected with a config satisfying the ConfigInterface. The connection will only be
 * establish if or when needed.
 *
 * @author Oliver Finn Madsen <mail@ofmadsen.com>
 */
class Database
{
    /** @var ConfigInterface */
    private $config;

    /** @var PDO */
    private $database;

    /**
     * Set the config for the database.
     *
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Use exec command on the database.
     *
     * @param string $sql
     *
     * @throws DatabaseQueryFailed
     *
     * @return int
     */
    public function execute($sql)
    {
        $this->connect();

        try {
            return $this->database->exec($sql);
        } catch (PDOException $exception) {
            throw new DatabaseQueryFailed($exception->getMessage());
        }
    }

    /**
     * Prepares an SQL statement on the database.
     *
     * @param string $sql
     * @param mixed[] $parameters
     *
     * @throws DatabaseQueryFailed
     *
     * @return Query
     */
    public function prepare($sql, array $parameters = [])
    {
        $this->connect();

        try {
            return new Query($this->database->prepare($sql), $parameters);
        } catch (PDOException $exception) {
            throw new DatabaseQueryFailed($exception->getMessage());
        }
    }

    /**
     * Initialize the connection to the database.
     *
     * The dsn, username, password and options are defined in the required config. If not set an exception will be
     * thrown. The error mode will be overwritten if defined in the options - this library uses the exception error
     * mode.
     *
     * @throws DatabaseConnectionFailed
     */
    private function connect()
    {
        if ($this->database) {
            return;
        }

        try {
            $this->database = new PDO(
                $this->config->getDSN(),
                $this->config->getUsername(),
                $this->config->getPassword(),
                $this->config->getOptions()
            );
        } catch (PDOException $exception) {
            throw new DatabaseConnectionFailed($exception->getMessage());
        }

        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}
