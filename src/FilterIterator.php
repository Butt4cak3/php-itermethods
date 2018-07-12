<?php
namespace itermethods;

class FilterIterator implements \Iterator {
    private $data;
    private $filter;
    private $key = 0;

    public function __construct($data, $filter) {
        $this->data = $data;
        $this->filter = $filter;
    }

    public function rewind() {
        $this->data->rewind();
        $this->key = 0;
        $fn = $this->filter;

        while ($this->data->valid() && !$fn($this->data->current(), $this->data->key())) {
            $this->data->next();
        }
    }

    public function valid() {
        return $this->data->valid();
    }

    public function key() {
        return $this->key;
    }

    public function current() {
        return $this->data->current();
    }

    public function next() {
        $fn = $this->filter;
        $this->key += 1;

        do {
            $this->data->next();
        } while ($this->data->valid() && !$fn($this->data->current(), $this->data->key()));
    }
}
