<?php
namespace Livid;

use Livid\Database;
use Livid\Exception\UndefinedDatabase;

/**
 * Abstract mapper class.
 *
 * Mappers should extend this abstract class for convenience. It is necessary to set the database to be used. If
 * multiple databases will be used then each must be set with a unique name. The mappers using the non-default named
 * database must overwrite the DATABASE constant. Note that no warning is triggered if a database is overwritten.
 *
 * @author Oliver Finn Madsen <mail@ofmadsen.com>
 */
abstract class Mapper
{
    /** @var string */
    const DATABASE = 'default';

    /** @var Database[] */
    private static $databases = [];

    /**
     * Set database with key.
     *
     * @param Database $database
     * @param string $name
     */
    public static function setDatabase(Database $database, $name = self::DATABASE)
    {
        self::$databases[$name] = $database;
    }

    /**
     * Query the database.
     *
     * The database is queried using a prepared statement and will return a Query object for data retrieval.
     *
     * @param string $sql
     * @param mixed[] $parameters
     *
     * @return \Livid\Query
     */
    public function query($sql, array $parameters = [])
    {
        return $this->getDatabase()->prepare($sql, $parameters);
    }

    /**
     * Execute a sql query on the database.
     *
     * @param string $sql
     *
     * @return int
     */
    public function execute($sql)
    {
        return $this->getDatabase()->execute($sql);
    }

    /**
     * Get the database by the constant name.
     *
     * @throws UndefinedDatabase
     *
     * @return Database
     */
    private function getDatabase()
    {
        if (!isset(self::$databases[static::DATABASE])) {
            throw new UndefinedDatabase(static::DATABASE);
        }

        return self::$databases[static::DATABASE];
    }
}
