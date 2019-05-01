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
 * Evaluate an expression of PHP code embedded in a doc block.
 *
 * Yes, I know it's eval. But it's developer input, not user input
 * and it's run in a testing context only.
 *
 * <pre>
 * use function Datashaman\Logic\evalWithArgs;
 * use function Datashaman\Logic\repr;
 *
 * print repr(evalWithArgs('strtoupper("Hi $name!")', ['name' => 'Bob'])) . PHP_EOL;
 * </pre>
 *
 * @param string $expression PHP string expression to be evaluated. Must not include semi-colon.
 * @param array  $args       local arguments defined while the expression is evaluated
 * @nodocs
 *
 */
function evalWithArgs(string $expression, $args = [])
{
    \extract($args);
    $expression = "namespace Datashaman\Logic; return $expression;";

    return eval($expression);
}

/**
 * Return a simple string representation of the value for display and logging.
 *
 * <pre>
 * use function Datashaman\PHPCheck\repr;
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
