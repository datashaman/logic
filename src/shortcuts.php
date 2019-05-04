<?php

namespace Datashaman\Logic;

/**
 * Creates a predicate function for checking the class of a value
 *
 * <pre>
 * use function Datashaman\Logic\C;
 *
 * $now = new DateTime();
 *
 * $p = C('DateTime');
 *
 * var_dump($p($now));
 * var_dump($p(12));
 * </pre>
 */
function C(string $class): callable
{
    return P(
        function ($value) use ($class) {
            return isClass($value, $class);
        },
        $class
    );
}

/**
 * Creates a function that calls a function on a Just (it should return the value)
 * and returns a default on a Nothing value.
 *
 * <pre>
 * use function Datashaman\Logic\D;
 * use function Datashaman\Logic\mkMaybe;
 *
 * $f = D(0);
 * echo $f(mkMaybe(null)) . PHP_EOL;
 * echo $f(mkMaybe(12)) . PHP_EOL;
 *
 * $f = D(0, function ($value) {
 *     return $value * 2;
 * });
 * echo $f(mkMaybe(null)) . PHP_EOL;
 * echo $f(mkMaybe(12)) . PHP_EOL;
 * </pre>
 */
function D($d, callable $f = null): callable
{
    if (is_null($f)) {
        $f = I();
    }

    return function (Maybe $x) use ($d, $f) {
        return maybe($d, $f, $x);
    };
}

/**
 * Creates an identity function (returns its value unchanged).
 *
 * <pre>
 * use function Datashaman\Logic\I;
 *
 * $f = I();
 *
 * var_dump($f(null));
 * var_dump($f(12));
 * var_dump($f('hello world'));
 * </pre>
 */
function I(): callable
{
    return function ($arg) {
        return $arg;
    };
}

/**
 * Creates a function which wraps a value with Just.
 *
 * <pre>
 * use function Datashaman\Logic\J;
 * use function Datashaman\Logic\repr;
 *
 * $f = J();
 *
 * echo repr($f(12)) . PHP_EOL;
 * </pre>
 */
function J(): callable
{
    return function ($arg) {
        return mkJust($arg);
    };
}

/**
 * Creates a function which returns Nothing
 *
 * <pre>
 * use function Datashaman\Logic\N;
 * use function Datashaman\Logic\repr;
 *
 * $f = N();
 *
 * echo repr($f(12)) . PHP_EOL;
 * </pre>
 */
function N(): callable
{
    return function ($arg = null) {
        return mkMaybe(null);
    };
}

/**
 * Creates a function which returns the unwrapped monad value.
 *
 * <pre>
 * use function Datashaman\Logic\mkJust;
 * use function Datashaman\Logic\V;
 *
 * $f = V();
 * $j = mkJust(12);
 *
 * var_dump($j);
 * var_dump($f($j));
 * </pre>
 */
function V(): callable
{
    return function (Monad $x) {
        return $x();
    };
}

/**
 * Creates a constant function (returns the same value regardless of argument).
 *
 * <pre>
 * use function Datashaman\Logic\K;
 *
 * $f = K(12);
 *
 * var_dump($f(null));
 * var_dump($f(0));
 * var_dump($f('hello world'));
 * </pre>
 */
function K($arg): callable
{
    return function ($_) use ($arg) {
        return $arg;
    };
}

/**
 * An alias for creating predicate functions.
 *
 * @return callable
 */
function P(...$args): callable
{
    return mkPredicate(...$args);
}

/**
 * Application (not sure what to do with this yet)
 *
 * @param callable $x
 * @param callable $y
 * @param mixed $z
 *
 * @return callable
 */
function S(callable $x, callable $y, $z): callable
{
    return function () use ($x, $y, $z) {
        return $x($z)($y($z));
    };
}

/**
 * Creates a match function which takes one parameter (the subject):
 *
 * - loops through the provided conditions
 * - calls the first array item (the predicate function) with the subject
 * - if the result is true, call the second array item (the callback function) with the subject
 * - if the result is false, go down the list of conditions
 * - all cases must be handled or an exception is thrown
 *
 * <pre>
 * use Datashaman\Logic\Just;
 * use Datashaman\Logic\Nothing;
 *
 * use function Datashaman\Logic\{K, J, M, N, T};
 * use function Datashaman\Logic\mkJust;
 * use function Datashaman\Logic\mkNothing;
 * use function Datashaman\Logic\repr;
 *
 * // If a null value is matched, return Nothing
 * // Else return a new Just value
 * $match = M(
 *     [
 *         'is_null',
 *         function () {
 *             return new Nothing();
 *         }
 *     ],
 *     [
 *         function () {
 *             return true;
 *         },
 *         function ($value) {
 *             return new Just($value);
 *         }
 *     ]
 * );
 *
 * echo repr($match(null)) . PHP_EOL;
 * echo repr($match(12)) . PHP_EOL;
 *
 * // The above can be written more succinctly using
 * // shortcuts. K makes a function that returns the supplied
 * // parameter to every function call, always returning a
 * // constant value. Here it always evaluates to true, which
 * // makes it perfect for the else branch in a conditional
 * // expression.
 *
 * // mkJust and mkNothing, when called with no parameters, return
 * // a factory function that does the same as the above.
 *
 * $match = M(
 *     ['is_null', mkNothing()],
 *     [K(true), mkJust()]
 * );
 *
 * echo repr($match(null)) . PHP_EOL;
 * echo repr($match(12)) . PHP_EOL;
 *
 * // The above can be written even MORE succinctly as follows.
 * // Whether this is wise is another question...
 * $match = M(
 *     ['is_null', N()],
 *     [T(), J()]
 * );
 *
 * echo repr($match(null)) . PHP_EOL;
 * echo repr($match(12)) . PHP_EOL;
 * </pre>
 */
function M(...$conditions): callable
{
    return function ($subject) use ($conditions) {
        foreach ($conditions as $condition) {
            [$predicate, $callback] = $condition;
            if ($predicate($subject)) {
                return $callback($subject);
            }
        }

        throw new Exception('Unhandled match condition');
    };
}

/**
 * Create a function that always returns true
 *
 * <pre>
 * use Datashaman\Logic\T;
 *
 * $f = T();
 *
 * var_dump($f(12));
 * var_dump($f(null));
 */
function T()
{
    return K(true);
}

/**
 * Create a function that always returns false
 *
 * <pre>
 * use Datashaman\Logic\F;
 *
 * $f = F();
 *
 * var_dump($f(12));
 * var_dump($f(null));
 */
function F()
{
    return K(false);
}
