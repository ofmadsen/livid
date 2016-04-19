<?php
namespace Test\App;

class Horse
{
    public $id;
    protected $stableId;
    private $name;

    public function getStableId()
    {
        return $this->stableId;
    }

    public function getName()
    {
        return $this->name;
    }
}
