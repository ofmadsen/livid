<?php
namespace Livid;

use ReflectionClass;
use ReflectionProperty;

/**
 * Factory for creating entities.
 *
 * Constructed with entity class name of the entities that should be produced given sets of data. A range of metadata
 * is cached locally when creating the first entity as neither the properties nor the columns of the sets will change.
 *
 * @author Oliver Finn Madsen <mail@ofmadsen.com>
 */
class EntityFactory
{
    /** @var string */
    private $entityClass;

    /** @var int */
    private $entityClassLength;

    /** @var bool */
    private $initialized = false;

    /** @var string[] */
    private $protectedEntityProperties = [];

    /** @var string[] */
    private $privateEntityProperties = [];

    /** @var string[] */
    private $columnMap = [];

    /**
     * Constructor.
     *
     * @param string $entityClass
     */
    public function __construct($entityClass)
    {
        $this->entityClass = $entityClass;
        $this->entityClassLength = strlen($entityClass);
    }

    /**
     * Create entity from set.
     *
     * @param string[] $set
     *
     * @return mixed
     */
    public function create(array $set)
    {
        if (!$this->initialized) {
            $this->storeEntityProperties();
            $this->createColumnMap($set);

            $this->initialized = true;
        }

        $data = [];
        foreach ($this->columnMap as $column => $renamed) {
            $data[$renamed] = $set[$column];
        }

        return $this->getInstance($data);
    }

    /**
     * Store entity properties.
     *
     * Will store all protected and private property names using reflection on the entity.
     */
    private function storeEntityProperties()
    {
        $class = new ReflectionClass($this->entityClass);

        foreach ($class->getProperties(ReflectionProperty::IS_PROTECTED) as $property) {
            $this->protectedEntityProperties[] = $property->getName();
        }

        foreach ($class->getProperties(ReflectionProperty::IS_PRIVATE) as $property) {
            $this->privateEntityProperties[] = $property->getName();
        }
    }

    /**
     * Create column map of set.
     *
     * Convert to camel case and use serialize format to describe protected and private properties.
     *
     * @param string[] $set
     */
    private function createColumnMap(array $set)
    {
        foreach (array_keys($set) as $column) {
            $renamed = $this->toCamelCase($column);

            if (in_array($renamed, $this->protectedEntityProperties)) {
                $renamed = "\0*\0{$renamed}";
            }

            if (in_array($renamed, $this->privateEntityProperties)) {
                $renamed = "\0{$this->entityClass}\0{$renamed}";
            }

            $this->columnMap[$column] = $renamed;
        }
    }

    /**
     * Convert snake case to camel case.
     *
     * @param string $string
     *
     * @return string
     */
    private function toCamelCase($string)
    {
        if (strpos($string, '_') === false) {
            return $string;
        }

        $string = ucwords($string, '_');
        return str_replace('_', '', lcfirst($string));
    }

    /**
     * Get instance of entity.
     *
     * The set data is serialized, the string is manipulated to use the entity and then unserialized into an object
     * instance.
     *
     * @param string[]
     *
     * @return mixed
     */
    private function getInstance(array $set)
    {
        $array = serialize($set);
        $undefined = substr($array, 1);
        $object = "O:{$this->entityClassLength}:\"{$this->entityClass}\"{$undefined}";

        return unserialize($object);
    }
}
