<?php

use Palmo\Core\service\AbstractClass;

class ChildClass extends AbstractClass
{
    public $name;
    public $color;

    protected function someMethod1()
    {
        return "Method1";
    }
    public function someMethod2($name, $color)
    {
        return "Method2";
    }
}
