<?php

namespace Datashaman\Logic;

use Exception;

/**
 * Just here for testing documentation generation.
interface MonadInterface
{
    public function bind(callable $f);
}

trait MonadTrait
{
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
 */

abstract class Monad
{
    /**
     * The value that is wrapped inside the monad.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Create a new Monad instance.
     *
     * @return void
     */
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
