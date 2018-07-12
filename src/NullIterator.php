<?php
namespace itermethods;

class NullIterator implements \Iterator {
    private $pos = 0;
    private $length;

    public function __construct($length) {
        $this->length = intval($length);
    }

    public function rewind() {
        $this->pos = 0;
    }

    public function current() {
        return null;
    }

    public function key() {
        return $this->pos;
    }

    public function next() {
        $this->pos += 1;
    }

    public function valid() {
        return $this->pos < $this->length;
    }
}
