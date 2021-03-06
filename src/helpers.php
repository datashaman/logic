<?php declare(strict_types=1);
/*
 * This file is part of the phpcheck package.
 *
 * ©Marlin Forbes <marlinf@datashaman.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Datashaman\Logic;

use Ds\Map;
use Generator;
use Icecave\Repr\Repr;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Curry a callable.
 *
 * <pre>
 * use function Datashaman\Logic\curry;
 *
 * $times = function ($x, $y) {
 *     return $x * $y;
 * };
 *
 * $double = curry($times, 2);
 * $triple = curry($times, 3);
 *
 * echo $double(4) . PHP_EOL;
 * echo $triple(4) . PHP_EOL;
 * </pre>
 */
function curry(callable $f, ...$args) {
    return function (...$rest) use ($f, $args) {
        $merged = array_merge($args, $rest);

        return $f(...$merged);
    };
}

/**
 * Return a simple string representation of the value for display and logging.
 *
 * <pre>
 * use function Datashaman\Logic\repr;
 * use Ds\Map;
 *
 * print repr([1, 2, 3]) . PHP_EOL;
 * print repr(['a' => 'A', 'b' => 'B', 'c' => 'C']) . PHP_EOL;
 * print repr(new Ds\Map(['a' => 'A', 'b' => 'B', 'c' => 'C'])) . PHP_EOL;
 * print repr("string") . PHP_EOL;
 * print repr(100) . PHP_EOL;
 * print repr(new DateTime()) . PHP_EOL;
 * </pre>
 *
 * @param mixed $value the value to represent
 * @nodocs
 *
 * @return string
 */
function repr($value)
{
    return Repr::repr($value);

    if ($value instanceof Map) {
        return \get_class($value) . ' {#' . \spl_object_id($value) . '}';
    }

    if (\is_string($value)) {
        return '"' . $value . '"';
    }

    if ($value === \PHP_INT_MIN) {
        return 'PHP_INT_MIN';
    }

    if ($value === \PHP_INT_MAX) {
        return 'PHP_INT_MAX';
    }

    if (\is_numeric($value)) {
        return $value;
    }

    if (\is_array($value)) {
        if (\count($value)) {
            $keys = \array_keys($value);

            if (\is_int($keys[0])) {
                return '[' . \implode(', ', \array_map(
                    function ($item) {
                        return repr($item);
                    },
                    $value
                )) . ']';
            } else {
                return '[' . \implode(', ', \array_map(
                    function ($key) use ($value) {
                        return "$key=" . repr($value[$key]);
                    },
                    $keys
                )) . ']';
            }
        } else {
            return '[]';
        }
    }

    return \json_encode($value);
}
