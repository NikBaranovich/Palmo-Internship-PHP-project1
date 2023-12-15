<?php

namespace Palmo\Core\service;

abstract class AbstractClass
{
    public $name;
    public $color;

    abstract protected function someMethod1();
    abstract public function someMethod2($name, $color);

    public function someMethod3()
    {
        return "Method3";
    }
}
