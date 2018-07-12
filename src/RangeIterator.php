<?php
namespace itermethods;

class RangeIterator implements \Iterator {
    private $key;
    private $value;
    private $start;
    private $end;

    public function __construct($start = 0, $end = PHP_INT_MAX) {
        $this->start = $start;
        $this->end = $end;
    }

    public function rewind() {
        $this->key = 0;
        $this->value = $this->start;
    }

    public function valid() {
        return $this->value >= $this->start && $this->value < $this->end;
    }

    public function key() {
        return $this->value;
    }

    public function current() {
        return $this->value;
    }

    public function next() {
        $this->key += 1;
        $this->value += 1;
    }
}
