<?php

namespace Datashaman\Logic\Either;

use function Datashaman\Logic\I;
use Datashaman\Logic\Monad;
use Exception;

abstract class Either extends Monad
{
}

class Left extends Either
{
    public function bind(callable $_)
    {
        return $this;
    }
}

class Right extends Either
{
}

function either(callable $f = null, callable $g = null, Either $e)
{
    if (is_null($f)) {
        $f = I();
    }

    if (is_null($g)) {
        $g = I();
    }

    if ($e->isLeft()) {
        return $f($e);
    }

    if ($e->isRight()) {
        return $g($e);
    }

    throw new Exception('This should not be possible');
}

function mkLeft(...$args): Left
{
    $f = function ($value): Left {
        return $value instanceof Left ? $value : new Left($value);
    };

    return $args ? $f($args[0]) : $f;
}

function mkRight(...$args): Right
{
    $f = function ($value): Right {
        return $value instanceof Right ? $value : new Right($value);
    };

    return $args ? $f($args[0]) : $f;
}

function fromLeft($d, Either $e)
{
    if (isLeft($e)) {
        return $e();
    }

    return $d;
}

function fromRight($d, Either $e)
{
    if (isRight($e)) {
        return $e();
    }

    return $d;
}

function isLeft(Either $e)
{
    return $e instanceof Left;
}

function isRight(Either $e)
{
    return $e instanceof Right;
}

function lefts(array $es)
{
    return array_filter(
        $es,
        'isLeft'
    );
}

function rights(array $es)
{
    return array_filter(
        $es,
        'isRight'
    );
}

function partitionEithers(array $es)
{
    return array_reduce(
        $es,
        function (array $acc, Either $e) {
            $acc[isLeft($e) ? 0 : 1][] = $e;

            return $acc;
        },
        [[], []]
    );
}
