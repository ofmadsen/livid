<?php
namespace Test\App;

use Livid\Mapper;

class StableMapper extends Mapper
{
    const DATABASE = 'undefined';

    public function getWithUndefinedEntity()
    {
        $sql = 'SELECT *
                FROM stables
                LIMIT 0, 1';

        return $this->query($sql)->get();
    }
}
