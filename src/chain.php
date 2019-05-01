<?php

namespace Datashaman\Logic;

class Chain extends Either\Either
{
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
}

function mkChain($value): Chain
{
    return $value instanceof Chain ? $value : new Chain($value);
}
