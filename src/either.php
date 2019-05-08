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
 *     'Datashaman\Logic\either',
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
function either(callable $f = null, callable $g = null, Either $e = null)
{
    $f = $f ?: I();
    $g = $g ?: I();

    if (is_null($e)) {
        throw new Exception('Either must not be null');
    }

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

/**
 * Return a Left value or a default if not Left.
 *
 * <pre>
 * use function Datashaman\Logic\fromLeft;
 * use function Datashaman\Logic\mkLeft;
 * use function Datashaman\Logic\mkRight;
 *
 * $err = mkLeft('You are not authorized');
 * $ok = mkRight('You are authorized');
 *
 * echo fromLeft('There was no error', $err) . PHP_EOL;
 * echo fromLeft('There was no error', $ok) . PHP_EOL;
 * </pre>
 */
function fromLeft($d, Either $e)
{
    if (isLeft($e)) {
        return $e();
    }

    return $d;
}

/**
 * Return a Right value or a default if not Right.
 *
 * <pre>
 * use function Datashaman\Logic\fromRight;
 * use function Datashaman\Logic\mkLeft;
 * use function Datashaman\Logic\mkRight;
 *
 * $err = mkLeft('You are not authorized');
 * $ok = mkRight('You are authorized');
 *
 * echo fromRight('There was an error', $err) . PHP_EOL;
 * echo fromRight('There was an error', $ok) . PHP_EOL;
 * </pre>
 */
function fromRight($d, Either $e)
{
    if (isRight($e)) {
        return $e();
    }

    return $d;
}

/**
 * Is an either value a Left value.
 *
 * <pre>
 * use function Datashaman\Logic\isLeft;
 * use function Datashaman\Logic\mkLeft;
 * use function Datashaman\Logic\mkRight;
 * use function Datashaman\Logic\repr;
 *
 * $err = mkLeft('Error');
 * $ok = mkRight('OK');
 *
 * echo repr(isLeft($err)) . PHP_EOL;
 * echo repr(isLeft($ok)) . PHP_EOL;
 * </pre>
 */
function isLeft(Either $e)
{
    return $e instanceof Left;
}

/**
 * Is an either value a Right value.
 *
 * <pre>
 * use function Datashaman\Logic\isRight;
 * use function Datashaman\Logic\mkLeft;
 * use function Datashaman\Logic\mkRight;
 * use function Datashaman\Logic\repr;
 *
 * $err = mkLeft('Error');
 * $ok = mkRight('OK');
 *
 * echo repr(isRight($err)) . PHP_EOL;
 * echo repr(isRight($ok)) . PHP_EOL;
 * </pre>
 */
function isRight(Either $e)
{
    return $e instanceof Right;
}

/**
 * Return a List_ of the lefts in a list of eithers.
 *
 * <pre>
 * use function Datashaman\Logic\lefts;
 * use function Datashaman\Logic\mkLeft;
 * use function Datashaman\Logic\mkRight;
 * use function Datashaman\Logic\repr;
 *
 * $es = [mkLeft('ERR'), mkRight('OK'), mkRight('OK Too'), mkLeft('ERR 13')];
 *
 * echo repr(lefts($es)) . PHP_EOL;
 * </pre>
 */
function lefts($es): List_
{
    return mkList($es)
        ->filter(isLeft::class)
        ->values();
}

/**
 * Return a List_ of the rights in a list of eithers.
 *
 * <pre>
 * use function Datashaman\Logic\mkLeft;
 * use function Datashaman\Logic\mkRight;
 * use function Datashaman\Logic\repr;
 * use function Datashaman\Logic\rights;
 *
 * $es = [mkLeft('ERR'), mkRight('OK'), mkRight('OK Too'), mkLeft('ERR 13')];
 *
 * echo repr(rights($es)) . PHP_EOL;
 * </pre>
 */
function rights($es): List_
{
    return mkList($es)
        ->filter(isRight::class)
        ->values();
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
