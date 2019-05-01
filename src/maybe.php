<?php

namespace Datashaman\Logic\Maybe;

use Datashaman\Logic\Monad;
use Exception;

use function Datashaman\Logic\K;
use function Datashaman\Logic\M;

abstract class Maybe extends Monad
{
}

class Just extends Maybe
{
}

final class Nothing extends Maybe
{
    public function __construct()
    {
    }

    public function __invoke(...$_)
    {
        return $this;
    }
}

function maybe($b, callable $f, Maybe $a)
{
    return isNothing($a) ? $b : $a($f);
}

function fromJust(Maybe $m)
{
    if (isJust($m)) {
        return $m();
    }

    throw new Exception('Nothing is nothing');
}

function isJust(Maybe $m): bool
{
    return $m instanceof Just;
}

function isNothing(Maybe $m): bool
{
    return $m instanceof Nothing;
}

function fromMaybe($d, Maybe $m)
{
    if (isJust($m)) {
        return $m();
    }

    return $d;
}

function maybeToList($m): List_
{
    if (isJust($m)) {
        return mkList([$m()]);
    }

    return mkList();
}

function listToMaybe($x): Maybe
{
    return mkList($x)->first();
}

function catMaybes(array $ms): array
{
    return array_filter(
        $ms,
        function ($m) {
            return $m->isJust();
        }
    );
}

function mapMaybe(
    callable $f,
    array $a
) {
    if (!$a) {
        return [];
    }

    [$x, $xs] = fromJust(mkList($a)->uncons());

    $rs = mapMaybe($f, $xs);

    $r = $f($x);

    if (isNothing($r)) {
        return $rs;
    }

    return array_merge(
        [$r()],
        $rs
    );
}

function mkJust(...$args): Just
{
    $f = function ($value): Just {
        return $value instanceof Just ? $value : new Just($value);
    };

    return $args ? $f($args[0]) : $f;
}

function mkMaybe(...$args): Maybe
{
    $f = function ($value): Maybe {
        if ($value instanceof Maybe) {
            return $value;
        }

        return M(
            ['is_null', mkNothing()],
            [K(true), mkJust()]
        )($value);
    };

    return $args ? $f($args[0]) : $f;
}

function mkNothing(...$args): Nothing
{
    $f = function ($_): Nothing {
        return $value instanceof Nothing ? $value : new Nothing();
    };

    return $args ? $f($args[0]) : $f;
}
