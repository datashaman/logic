<?php

namespace Datashaman\Logic;

use Icecave\Repr\Generator;

class Chain extends Either
{
    public function stringRepresentation(Generator $generator, $currentDepth = 0)
    {
        return '<Chain ' . $generator->generate($this->value) . '>';
    }

    public function then(
        callable $done = null,
        callable $fail = null
    ) {
        return either(
            $fail,
            $done,
            $this
        );
    }

    public function done(callable $done)
    {
        return $this->then($done);
    }

    public function fail(callable $fail)
    {
        return $this->then(null, $fail);
    }

    public function always(callable $always)
    {
        return $this->then($always, $always);
    }
}

function mkChain($value): Chain
{
    return $value instanceof Chain ? $value : new Chain($value);
}
