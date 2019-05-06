<?php

namespace Datashaman\Logic;

use Exception;
use Icecave\Repr\Generator;
use Icecave\Repr\RepresentableInterface;

/**
 * Abstract base class for Maybe monads.
 */
abstract class Maybe extends Monad implements
    RepresentableInterface
{
}

class Just extends Maybe
{
    public function stringRepresentation(Generator $generator, $currentDepth = 0)
    {
        return '<Just ' . $generator->generate($this->value) . '>';
    }
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

    public function stringRepresentation(Generator $generator, $currentDepth = 0)
    {
        return '<Nothing>';
    }
}

function maybe($b, callable $f, Maybe $a)
{
    return isNothing($a) ? $b : $f($a());
}

/**
 * The fromJust function extracts the element out of a Just and throws an error if its argument is Nothing.
 *
 * <pre>
 * use function Datashaman\Logic\fromJust;
 * use function Datashaman\Logic\mkMaybe;
 * use function Datashaman\Logic\repr;
 *
 * $s = mkMaybe('hello');
 * $n = mkMaybe(null);
 *
 * echo repr(fromJust($s)) . PHP_EOL;
 *
 * fromJust($n);
 * </pre>
 */
function fromJust(Maybe $m)
{
    if (isJust($m)) {
        return $m();
    }

    throw new Exception('Maybe.fromJust: Nothing');
}

function isJust(Maybe $m): bool
{
    return $m instanceof Just;
}

function isNothing(Maybe $m): bool
{
    return $m instanceof Nothing;
}

/**
 * The fromMaybe function takes a default value and and Maybe value. If the Maybe is Nothing, it returns the default values; otherwise, it returns the value contained in the Maybe.
 *
 * <pre>
 * use function Datashaman\Logic\fromMaybe;
 * use function Datashaman\Logic\mkMaybe;
 * use function Datashaman\Logic\repr;
 *
 * echo repr(fromMaybe("", mkMaybe('Hello World'))) . PHP_EOL;
 * echo repr(fromMaybe("", mkMaybe(null))) . PHP_EOL;
 * </pre>
 */
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

/**
 * The catMaybes function takes a list of Maybes and returns a List_ of all the Just values.
 *
 * <pre>
 * use function Datashaman\Logic\catMaybes;
 * use function Datashaman\Logic\mkMaybe;
 * use function Datashaman\Logic\repr;
 *
 * $ms = [mkMaybe(null), mkMaybe(12), mkMaybe(23), mkMaybe(null), mkMaybe(null)];
 *
 * echo repr(catMaybes($ms)) . PHP_EOL;
 * </pre>
 */
function catMaybes(array $ms): List_
{
    return mkList($ms)
        ->filter(isJust::class)
        ->values();
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

function mkJust(...$args)
{
    $f = function ($value): Just {
        return $value instanceof Just ? $value : new Just($value);
    };

    return $args ? $f($args[0]) : $f;
}

function mkMaybe(...$args)
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

function mkNothing(...$args)
{
    $f = function ($value): Nothing {
        return $value instanceof Nothing ? $value : new Nothing();
    };

    return $args ? $f($args[0]) : $f;
}
