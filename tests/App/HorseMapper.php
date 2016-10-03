<?php
namespace Test\App;

use Livid\Mapper;
use Test\App\Horse;
use Test\App\HorseStable;

class HorseMapper extends Mapper
{
    public function getWithDefinedEntity($id)
    {
        $sql = 'SELECT *
                FROM horses
                WHERE id = :id';

        $parameters = [
            'id' => $id
        ];

        return $this->query($sql, $parameters)->get(Horse::class);
    }

    public function getWithUndefinedEntity()
    {
        $sql = 'SELECT *
                FROM horses
                LIMIT 0, 1';

        return $this->query($sql)->get();
    }

    public function getMoreThanOne()
    {
        $sql = 'SELECT *
                FROM horses';

        return $this->query($sql)->get();
    }

    public function allWithJoinedEntity()
    {
        $sql = 'SELECT
                    h.id,
                    h.name,
                    s.name AS stable_name
                FROM horses AS h
                JOIN stables AS s ON h.stable_id = s.id';

        return $this->query($sql)->all(HorseStable::class);
    }

    public function scalarValue()
    {
        $sql = 'SELECT COUNT(id)
                FROM horses';

        return $this->query($sql)->scalar();
    }

    public function addColumnColor()
    {
        $sql = 'ALTER TABLE horses
                ADD COLUMN color TEXT';

        return $this->execute($sql);
    }

    public function executePrepared()
    {
        $sql = 'INSERT INTO horses (
                    "id",
                    "stable_id",
                    "name"
                ) VALUES (
                    :id,
                    :stableId,
                    :name
                )';

        $parameters = [
            'id' => 4,
            'stableId' => 1,
            'name' => 'Frank'
        ];

        return $this->query($sql, $parameters)->execute();
    }

    public function sqlQuerySyntaxFailure()
    {
        return $this->query('SQL')->get();
    }

    public function sqlExecuteSyntaxFailure()
    {
        return $this->execute('SQL');
    }

    public function illegalParameterFailure()
    {
        $sql = 'SELECT *
                FROM horses';

        $parameters = [
            'horse' => true
        ];

        return $this->query($sql, $parameters)->get(Horse::class);
    }
}
