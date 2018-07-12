<?php
namespace itermethods;

class KeyIterator implements \Iterator {
    private $data;
    private $key = 0;

    public function __construct($data) {
        $this->data = $data;
    }

    public function rewind() {
        $this->data->rewind();
        $this->key = 0;
    }

    public function valid() {
        return $this->data->valid();
    }

    public function current() {
        return $this->data->key();
    }

    public function key() {
        return $this->key;
    }

    public function next() {
        $this->data->next();
        $this->key += 1;
    }
}
