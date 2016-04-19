<?php
namespace Livid;

use Livid\Entity;
use Livid\EntityFactory;
use Livid\Exception\DatabaseQueryFailed;
use Livid\Exception\InvalidFetchedEntityCount;
use PDO;
use PDOException;
use PDOStatement;

/**
 * Wrapper class for a PDOStatement.
 *
 * The Query class is used to connect the PDOStatement and the fetched result, where the result can range from a single
 * entity class instance, an array of entities or simple scalar value.
 *
 * @author Oliver Finn Madsen <mail@ofmadsen.com>
 */
class Query
{
    /** @var PDOStatement */
    private $statement;

    /** @var mixed[] */
    private $parameters;

    /**
     * Constructor.
     *
     * @param PDOStatement $statement
     * @param mixed[] $parameters
     */
    public function __construct(PDOStatement $statement, $parameters)
    {
        $this->statement = $statement;
        $this->parameters = $parameters;
    }

    /**
     * Get a single entity.
     *
     * If the result contains more than one entity an exception is thrown.
     *
     * @param string $entity
     *
     * @throws InvalidFetchedEntityCount
     *
     * @return mixed
     */
    public function get($entity = Entity::class)
    {
        $entities = $this->fetch($entity);

        if (count($entities) > 1) {
            throw new InvalidFetchedEntityCount();
        }

        return reset($entities);
    }

    /**
     * Get multiple entities.
     *
     * @param string $entity
     *
     * @return mixed[]
     */
    public function all($entity = Entity::class)
    {
        return $this->fetch($entity);
    }

    /**
     * Get a scalar value.
     *
     * @return mixed
     */
    public function scalar()
    {
        return $this->execute()->fetchColumn();
    }

    /**
     * Bind parameters and execute the statement.
     *
     * @throws DatabaseQueryFailed
     *
     * @return PDOStatement
     */
    private function execute()
    {
        foreach ($this->parameters as $parameter => $value) {
            $this->statement->bindValue(":{$parameter}", $value);
        }

        try {
            $this->statement->execute();
        } catch (PDOException $exception) {
            throw new DatabaseQueryFailed($exception->getMessage());
        }

        return $this->statement;
    }

    /**
     * Create entities with fetched data.
     *
     * @param string $entity
     *
     * @return mixed[]
     */
    private function fetch($entity)
    {
        $this->execute();
        $factory = new EntityFactory($entity);

        $entities = [];
        while ($set = $this->statement->fetch(PDO::FETCH_ASSOC)) {
            $entities[] = $factory->create($set);
        }

        return $entities;
    }
}
