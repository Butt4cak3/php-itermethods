<?php
namespace itermethods;

class MapIterator implements \Iterator {
    private $data;
    private $fn;

    public function __construct($data, $fn) {
        $this->data = $data;
        $this->fn = $fn;
    }

    public function rewind() {
        $this->data->rewind();
    }

    public function valid() {
        return $this->data->valid();
    }

    public function current() {
        $fn = $this->fn;
        return $fn($this->data->current(), $this->data->key());
    }

    public function key() {
        return $this->data->key();
    }

    public function next() {
        $this->data->next();
    }
}
