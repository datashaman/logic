<?php

namespace Datashaman\Logic;

function head(array $a)
{
    if (!$a) {
        throw new Exception('Empty list');
    }

    return $a[0];
}

function tail(array $a)
{
    if (!$a) {
        throw new Exception('Empty list');
    }

    return array_slice($a, 1);
}

function uncons(array $a): Maybe\Maybe
{
    if (!$a) {
        return new Nothing();
    }

    return new Maybe\Just([head($a), tail($a)]);
}
