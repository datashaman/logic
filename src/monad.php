<?php

namespace Datashaman\Logic;

abstract class Monad
{
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __invoke(...$args)
    {
        if (!$args) {
            return $this->value;
        }

        $f = array_shift($args);

        return $f($this->value, ...$args);
    }

    public function bind(callable $f)
    {
        // $f must return a monad
        $result = $this($f);

        if (!$result instanceof self) {
            throw new Exception('Function must return a monad');
        }

        return $result;
    }

}
