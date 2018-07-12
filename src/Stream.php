<?php
namespace itermethods;

class Stream implements \Iterator {
    private $data;

    public static function from($data) {
        return new static($data);
    }

    public static function nulls($length) {
        return new static(new NullIterator($length));
    }

    public static function range($start = null, $end = null) {
        if ($start !== null && $end === null) {
            $end = $start;
            $start = 0;
        } elseif ($start === null && $end === null) {
            $start = 0;
            $end = PHP_INT_MAX;
        }

        return new static(new RangeIterator($start, $end));
    }

    public function __construct($data) {
        if ($data instanceof \Iterator) {
            $this->data = $data;
        } elseif (is_array($data)) {
            $this->data = new \ArrayIterator($data);
        } else {
            throw new \TypeError("\$data must be an Iterator");
        }
    }

    public function toArray() {
        $result = [];

        foreach ($this->data as $value) {
            $result[] = $value;
        }

        return $result;
    }

    public function collect() {
        return $this->toArray();
    }

    // Data operations

    public function concat($other) {
        return new Stream(new ConcatIterator([$this->data, $other]));
    }

    public function entries() {
        return new Stream(new EntriesIterator($this->data));
    }

    public function every($fn) {
        if (!is_callable($fn)) throw new \TypeError("\$fn must be callable");

        foreach ($this->data as $key => $value) {
            if (!$fn($value, $key)) {
                return false;
            }
        }

        return true;
    }

    public function fill($value, $start = 0, $end = PHP_INT_MAX) {
        return new Stream(new FillIterator($this->data, $value, $start, $end));
    }

    public function filter($filter) {
        return new Stream(new FilterIterator($this->data, $filter));
    }

    public function find($filter) {
        foreach ($this->data as $key => $value) {
            if ($filter($value, $key)) {
                return $value;
            }
        }
    }

    public function findIndex($filter) {
        foreach ($this->data as $key => $value) {
            if ($filter($value, $key)) {
                return $key;
            }
        }

        return -1;
    }

    public function includes($search, $fromIndex = 0) {
        foreach ($this->data as $key => $value) {
            if ($key >= $fromIndex && $value === $search) {
                return true;
            }
        }

        return false;
    }

    public function indexOf($search, $fromIndex = 0) {
        foreach ($this->data as $key => $value) {
            if ($key >= $fromIndex && $value === $search) {
                return $key;
            }
        }

        return -1;
    }

    public function join($separator = "") {
        return implode($separator, $this->collect());
    }

    public function keys() {
        return new Stream(new KeysIterator($this->data));
    }

    public function map($fn) {
        return new Stream(new MapIterator($this->data, $fn));
    }

    public function reduce($fn, $initial = null, $nullInitial = false) {
        $this->data->rewind();
        if ($initial === null && !$nullInitial) {
            $initial = $this->data->current();
            $this->data->next();
        }

        $acc = $initial;
        while ($this->data->valid()) {
            $acc = $fn($acc, $this->data->current(), $this->data->key());
            $this->data->next();
        }

        return $acc;
    }

    public function skip($filter) {
        return new Stream(new SkipIterator($this->data, $filter));
    }

    public function slice($begin = 0, $end = PHP_INT_MAX) {
        return new Stream(new SliceIterator($this->data, $begin, $end));
    }

    public function some($filter) {
        foreach ($this->data as $key => $value) {
            if ($filter($value, $key)) {
                return true;
            }
        }

        return false;
    }

    public function take($filter) {
        return new Stream(new TakeIterator($this->data, $filter));
    }

    // Iterator methods

    public function rewind() {
        $this->data->rewind();
    }

    public function current() {
        return $this->data->current();
    }

    public function key() {
        return $this->data->key();
    }

    public function valid() {
        return $this->data->valid();
    }

    public function next() {
        $this->data->next();
    }
}
