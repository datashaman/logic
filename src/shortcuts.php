<?php

namespace Datashaman\Logic;

/**
 * Creates a predicate function for checking the class of a value
 *
 * @param string $class
 *
 * @return callable
 */
function C(string $class): callable
{
    return P('isClass', $class);
}

/**
 * Creates a function that calls a function on a Just (it should return the value)
 * and returns a default on a Nothing value.
 *
 * @param mixed $d
 * @param null|callable $f
 *
 * @return callable
 */
function D($d, callable $f = null): callable
{
    if (is_null($f)) {
        $f = V();
    }

    return function (Maybe $x) use ($d, $f) {
        return maybe($d, $f, $x);
    };
}

/**
 * Creates an identity function (returns its value unchanged).
 *
 * @return callable
 */
function I(): callable
{
    return function ($arg) {
        return $arg;
    };
}

/**
 * Creates a function which returns the value of the monad.
 *
 * @return callable
 */
function V(): callable
{
    return function ($x) {
        return $x();
    };
}

/**
 * Creates a constant function (returns the same value regardless of argument).
 *
 * @param mixed $arg
 *
 * @return callable
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
 * Creates a predicate function for checking the type of a value.
 *
 * @param string $type
 *
 * @return callable
 */
function T(string $type): callable
{
    return P('isType', $type);
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
 * @return callable
 */
function M(...$conditions)
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
