<?php
namespace itermethods;

class EntriesIterator implements \Iterator {
    public function __construct($data) {
        $this->data = $data;
    }

    public function rewind() {
        $this->data->rewind();
    }

    public function valid() {
        return $this->data->valid();
    }

    public function current() {
        return [$this->data->key(), $this->data->current()];
    }

    public function next() {
        $this->data->next();
    }

    public function key() {
        return $this->data->key();
    }
}
