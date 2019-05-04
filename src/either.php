<?php

namespace Datashaman\Logic;

use function Datashaman\Logic\I;
use Datashaman\Logic\Monad;
use Exception;
use Icecave\Repr\Generator;
use Icecave\Repr\RepresentableInterface;

abstract class Either extends Monad implements
    RepresentableInterface
{
}

class Left extends Either
{
    public function bind(callable $_)
    {
        return $this;
    }

    public function stringRepresentation(Generator $generator, $currentDepth = 0)
    {
        return '<Left ' . $generator->generate($this->value) . '>';
    }
}

class Right extends Either
{
    public function stringRepresentation(Generator $generator, $currentDepth = 0)
    {
        return '<Right ' . $generator->generate($this->value) . '>';
    }
}

/**
 * If the either monad is a Left, call the first callable with the monad value.
 * If the either monad is a right, call the second callable with the monad value.
 *
 * <pre>
 * use function Datashaman\Logic\curry;
 * use function Datashaman\Logic\either;
 * use function Datashaman\Logic\mkLeft;
 * use function Datashaman\Logic\mkRight;
 *
 * $s = mkLeft('foo');
 * $n = mkRight(3);
 *
 * echo either(
 *     function ($value) {
 *         return strlen($value);
 *     },
 *     function ($value) {
 *         return $value * 2;
 *     },
 *     $s
 * ) . PHP_EOL;
 *
 * echo either(
 *     function ($value) {
 *         return strlen($value);
 *     },
 *     function ($value) {
 *         return $value * 2;
 *     },
 *     $n
 * ) . PHP_EOL;
 *
 * // Or use currying
 *
 * $f = curry(
 *     function (...$args) {
 *         return either(...$args);
 *     },
 *     function ($value) {
 *         return strlen($value);
 *     },
 *     function ($value) {
 *         return $value * 2;
 *     }
 * );
 *
 * echo $f($s) . PHP_EOL;
 * echo $f($n) . PHP_EOL;
 * </pre>
 */
function either(callable $f = null, callable $g = null, Either $e)
{
    $f = $f ?: I();
    $g = $g ?: I();

    if (isLeft($e)) {
        return $f($e());
    }

    if (isRight($e)) {
        return $g($e());
    }

    throw new Exception('This should not be possible');
}

/**
 * Make a Left value. If no arguments are supplied, return a function which creates Left values.
 *
 * <pre>
 * use function Datashaman\Logic\mkLeft;
 * use function Datashaman\Logic\repr;
 *
 * $err = mkLeft('You are not authorized');
 *
 * echo repr($err) . PHP_EOL;
 *
 * $f = mkLeft();
 *
 * echo repr($f('You are not authorized')) . PHP_EOL;
 * </pre>
 */
function mkLeft(...$args)
{
    $f = function ($value): Left {
        return $value instanceof Left ? $value : new Left($value);
    };

    return $args ? $f($args[0]) : $f;
}

/**
 * Make a Right value. If no arguments are supplied, return a function which creates Right values.
 *
 * <pre>
 * use function Datashaman\Logic\mkRight;
 * use function Datashaman\Logic\repr;
 *
 * $ok = mkRight('You are authorized');
 *
 * echo repr($ok) . PHP_EOL;
 *
 * $f = mkRight();
 *
 * echo repr($f('You are authorized')) . PHP_EOL;
 * </pre>
 */
function mkRight(...$args)
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

function lefts($es): List_
{
    return mkList($es)->filter('isLeft');
}

function rights($es): List_
{
    return mkList($es)->filter('isRight');
}

function partitionEithers($es): List_
{
    $partitions = mkList($es)
        ->reduce(
            function ($acc, Either $e) {
                $acc[isLeft($e) ? 0 : 1][] = $e;

                return $acc;
            },
            [[], []]
        );

    return mkList($partitions);
}
