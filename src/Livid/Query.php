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
    public function __construct(PDOStatement $statement, array $parameters = [])
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
        $this->execute();
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
        $this->execute();
        return $this->fetch($entity);
    }

    /**
     * Get a scalar value.
     *
     * @return mixed
     */
    public function scalar()
    {
        $this->execute();
        return $this->statement->fetchColumn();
    }

    /**
     * Bind parameters and execute the statement.
     *
     * @throws DatabaseQueryFailed
     *
     * @return bool
     */
    public function execute()
    {
        foreach ($this->parameters as $parameter => $value) {
            $this->statement->bindValue(":{$parameter}", $value);
        }

        try {
            return $this->statement->execute();
        } catch (PDOException $exception) {
            throw new DatabaseQueryFailed($exception->getMessage());
        }
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
        $factory = new EntityFactory($entity);

        $entities = [];
        while ($set = $this->statement->fetch(PDO::FETCH_ASSOC)) {
            $entities[] = $factory->create($set);
        }

        return $entities;
    }
}
