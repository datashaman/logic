<?php

namespace Datashaman\Logic;

class List_ extends Monad
{
    public function __construct($value = [])
    {
        return parent::__construct((array) $value);
    }

    public function bind(callable $f)
    {
        return mkList(array_map($this->value, $f));
    }

    public function chunk(int $size): List_
    {
        if ($size <= 0) {
            return mkList();
        }

        $chunks = [];
        foreach (array_chunk($this->value, $size, true) as $chunk) {
            $chunks[] = mkList($chunk);
        }

        return mkList($chunks);
    }

    public function combine($values): List_
    {
        $values = mkList($values);

        return mkList(array_combine($this->value, $values()));
    }

    public function contains($value): bool
    {
        return in_array($value, $this->value);
    }

    public function count(): int
    {
        return count($this->value);
    }

    public function diff($items): List_
    {
        $items = mkList($items);

        return mkList(array_diff($this->value, $items()));
    }

    public function filter(callable $p): List_
    {
        return mkList(array_filter($this->value, $p));
    }

    public function first(callable $p = null, $default = null): Maybe
    {
        if (count($this->value) === 0) {
            return $default;
        }

        if (is_null($p)) {
            $p = K(true);
        }

        $key = $this->search($p);

        if ($key === false) {
            return mkMaybe($default);
        }

        return mkMaybe($this->value[$key]);
    }

    public function flip(): List_
    {
        return mkList(array_flip($this->value));
    }

    public function head()
    {
        if (count($this->value) === 0) {
            throw new Exception('List must not be empty');
        }

        return $this->first();
    }

    public function hasKey($key): bool
    {
        return array_key_exists($key, $this->value);
    }

    public function implode($delimiter = null)
    {
        return implode($delimiter, $this->value);
    }

    public function init()
    {
        if (count($this->value) === 0) {
            throw new Exception('List must not be empty');
        }

        return $this->slice(0, count($this->value) - 1);
    }

    public function intersect($items): List_
    {
        $items = mkList($items);

        return mkList(array_intersect($this->value, $items()));
    }

    public function intersectKey($items): List_
    {
        $items = mkList($items);

        return mkList(array_intersect_key($this->value, $items()));
    }

    public function isEmpty(): bool
    {
        return count($this->value) === 0;
    }

    public function keys(): List_
    {
        return mkList(array_keys($this->value));
    }

    public function last()
    {
        if (count($this->value) === 0) {
            throw new Exception('List must not be empty');
        }

        return $this->value[count($this->value) - 1];
    }

    public function map(callable $f): List_
    {
        return $this->bind($f);
    }

    public function merge($items): List_
    {
        $items = mkList($items);

        return mkList(array_merge($this->value, $items()));
    }

    public function pad(int $size, $value): List_
    {
        return mkList(array_pad($this->value, $size, $value));
    }

    public function pop()
    {
        return array_pop($this->value);
    }

    public function push($value): List_
    {
        $this->value[] = $value;

        return $this;
    }

    public function reverse(): List_
    {
        return mkList(array_reverse($this->value, true));
    }

    public function reduce(callable $f, $initial = null)
    {
        return array_reduce($this->value, $f, $initial);
    }

    public function search($value, $strict = false)
    {
        if (!is_callable($value)) {
            return array_search($value, $this->value, $strict);
        }

        foreach ($this->value as $key => $item) {
            if ($value($item, $key)) {
                return $key;
            }
        }

        return false;
    }

    public function shift()
    {
        return array_shift($this->value);
    }

    public function shuffle()
    {
        return mkList(array_shuffle($this->values));
    }

    public function slice($offset, int $length)
    {
        return mkList(array_slice($this->value, $offset, $length, true));
    }

    public function splice($offset, int $length = null, $replacement = [])
    {
        if (func_num_args() === 1) {
            return mkList(array_splice($this->value, $offset));
        }

        return mkList(array_splice($this->value, $offset, $length, $replacement));
    }

    public function sum(callable $f = null)
    {
        if (is_null($f)) {
            return array_sum($this->value);
        }

        return $this->reduce(
            function ($acc, $item) use ($f) {
                return $acc + $f($item);
            },
            0
        );
    }

    public function tail(): List_
    {
        if (count($this->value) === 0) {
            throw new Exception('List must not be empty');
        }

        return $this->slice(1);
    }

    public function uncons(): Maybe
    {
        if (count($this->value) === 0) {
            return mkNothing();
        }

        return mkJust([$this->head(), $this->tail()]);
    }

    public function values(): List_
    {
        return mkList(array_values($this->value));
    }
}

function mkList($value = []): List_
{
    return $value instanceof List_ ? $value : new List_($value);
}
