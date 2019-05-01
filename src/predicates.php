<?php

function isClass($x, string $class): bool
{
    return $x instanceof $class;
}

function isType($x, string $type): bool
{
    return gettype($x) === $type;
}

function mkPredicate(...$args)
{
    return function ($x) use ($args): bool {
        [$p, $args] = [head($args), tail($args)];

        return $p($x, ...$args);
    };
}
