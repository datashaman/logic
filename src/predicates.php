<?php

function isClass($x, string $class): bool
{
    return $x instanceof $class;
}

function isType($x, string $type): bool
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
