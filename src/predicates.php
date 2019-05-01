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
    $list = mkList($args);

    return function ($x) use ($list): bool {
        $maybe = $list->uncons();

        return $p($x, ...$args);
    };
}
