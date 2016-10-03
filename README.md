# Livid
Livid is a Data Mapper (ORM) for PHP.

[![Build Status](https://travis-ci.org/ofmadsen/livid.svg?branch=master)](https://travis-ci.org/ofmadsen/livid)
[![Codacy Badge](https://api.codacy.com/project/badge/coverage/4560148146744fbbaabe664e19ac5f80)](https://www.codacy.com/app/ofmadsen/livid)
[![Codacy Badge](https://api.codacy.com/project/badge/grade/4560148146744fbbaabe664e19ac5f80)](https://www.codacy.com/app/ofmadsen/livid)

## Installation
Install the latest version with:

```bash
$ composer require ofmadsen/livid
```

## Features
- Uses `PDO` as database driver (supporting multiple database types)
- Support connections to multiple databases (same and/or different types)
- Lazy-load connection to the database (connect when queried)
- Query result with single, all or scalar values
- Automatic conversion of column snake case naming to entity property camel case naming

## Basic usage
Bootstrapping:
```php
<?php

$config = new Livid\Config\SQLite3Config();
$database = new Livid\Database($config);

Livid\Mapper::setDatabase($database);
```

Mapper:
```php
<?php

class ExampleMapper extends Livid\Mapper
{
    public function get($id)
    {
        $sql = 'SELECT *
                FROM examples
                WHERE id = :id';

        $parameters = ['id' => $id];

        return $this->query($sql, $parameters)->get()
    }
}

// Assuming the examples table contain a row with id = 1
$mapper = new ExampleMapper();
$entity = $mapper->get(1);

// Will echo Livid\Entity as no entity object was specified in the Query::get method
echo get_class($entity);
```
Note that [`Livid\Mapper`](src/Livid/Mapper.php) do not define a constructor method and mappers are therefore free to have anything injected. Injecting mappers into other classes are relatively cheap as the connection to the database are not established until needed and reused across mappers.

## Fetch data in defined entities
Livid support entities with public, protected and private properties. The naming convention for entity properties in Livid is camel case and as such any column name that contains underscore are converted.

Example entity:
```php
<?php

class Example
{
    private $id;
    private $exampleText;

    public function getExampleText()
    {
        return $this->exampleText;
    }
}
```

Example mapper:
```php
<?php

class ExampleMapper extends Livid\Mapper
{
    public function get($id)
    {
        // Note that the database column name is in snake case form
        $sql = 'SELECT id, example_text
                FROM examples
                WHERE id = :id';

        $parameters = ['id' => $id];

        // Note that the get method on the query object is called with a reference to the Example class
        return $this->query($sql, $parameters)->get(Example::class)
    }
}

$mapper = new ExampleMapper();
$example = $mapper->get(1);

echo $example->getExampleText();
```

As entities are simple container objects they can contain whatever methods and logic that suits the needs of the application.

## Use multiple databases
Livid supports connections to multiple databases. Setting the database without a name will use the default name. Mappers that uses non-default databases must define the constant `DATABASE` containing the name that the database was set with.
```php
<?php

class ExampleMapper extends Livid\Mapper
{
    // Uses the default database - in this case the MySQL at 127.0.0.1 with database 'livid' - see below
}

class ExampleLoggerMapper extends Livid\Mapper
{
    const DATABASE = 'logger';
}
```

If multiple mappers uses the non-default database it might be easier to maintain if they extend a common class that defines the database name.

```php
<?php

// Default setup
$config = new Livid\Config\MySQLConfig();
$config->setHost('127.0.0.1');
$config->setDatabase('livid');
$defaultDatabase = new Livid\Database($config);

Livid\Mapper::setDatabase($defaultDatabase);

// Logger setup
$config = new Livid\Config\SQLite3Config();
$loggerDatabase = new Livid\Database($config);

Livid\Mapper::setDatabase($loggerDatabase, ExampleLoggerMapper::DATABASE);
```

## Supported databases
Only a subset of the `PDO` supported databases are available for this library at the moment. If your preferred database is not present on the list below, feel free to contribute to the project.

##### MySQL
For [MySQL](http://php.net/manual/ref.pdo-mysql.php) database use [`Livid\Config\MySQLConfig`](src/Livid/Config/MySQLConfig.php).

##### SQLite3
For [SQLite3](http://php.net/manual/ref.pdo-sqlite.php) database use [`Livid\Config\SQLite3Config`](src/Livid/Config/SQLite3Config.php).

## Interface
Available methods on [`Livid\Mapper`](src/Livid/Mapper.php):
```php
<?php

/**
 * Query the database.
 *
 * @param string $sql
 * @param mixed[] $parameters
 *
 * @return Livid\Query
 */
public function query($sql, array $parameters = []);

/**
 * Execute a sql query on the database.
 *
 * @param string $sql
 *
 * @return int
 */
public function execute($sql);
```

Available methods on [`Livid\Query`](src/Livid/Query.php):
```php
<?php

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
public function get($entity = Entity::class);

/**
 * Get multiple entities.
 *
 * @param string $entity
 *
 * @return mixed[]
 */
public function all($entity = Entity::class);

/**
 * Get a scalar value.
 *
 * @return mixed
 */
public function scalar();

/**
 * Bind parameters and execute the statement.
 *
 * @throws DatabaseQueryFailed
 *
 * @return bool
 */
public function execute();
```

## Contribution
All are welcome to contribute to Livid. Please get in touch before making large features and/or major refactoring. Needlessly to say the coding style must be followed and full test coverage is required.

## License
Livid is available under the MIT License - see the [`LICENSE`](LICENSE) file for details.
