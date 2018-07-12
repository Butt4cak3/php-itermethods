<?php
namespace itermethods;

class TakeIterator implements \Iterator {
    private $data;
    private $filter;
    private $key;

    public function __construct($data, $filter) {
        $this->data = $data;
        $this->filter = $filter;
    }

    public function rewind() {
        $this->data->rewind();
        $this->key = 0;
    }

    public function valid() {
        return $this->data->valid() && $this->applyFilter();
    }

    public function key() {
        return $this->key;
    }

    public function current() {
        return $this->data->current();
    }

    public function next() {
        $this->data->next();
        $this->key += 1;
    }

    private function applyFilter() {
        $fn = $this->filter;

        if (is_callable($this->filter)) {
            return $fn($this->data->current(), $this->key());
        } elseif (is_int($this->filter)) {
            return $this->key < $this->filter;
        }
    }
}
