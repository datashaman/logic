<?php

namespace Datashaman\Logic;

function isClass(string $class, $x): bool
{
    return $x instanceof $class;
}

function isType(string $type, $x): bool
{
    return gettype($x) === $type;
}

function mkPredicate(...$args): callable
{
    $list = mkList($args);

    return function ($x) use ($list): bool {
        [$p, $args] = fromJust($list->uncons());

        return $p($x, ...$args);
    };
}
