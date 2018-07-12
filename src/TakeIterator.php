<?php
namespace itermethods;

class TakeIterator implements \Iterator {
    private $data;
    private $filter;

    public function __construct($data, $filter) {
        $this->data = $data;
        $this->filter = $filter;
    }

    public function rewind() {
        $this->data->rewind();
    }

    public function valid() {
        return $this->data->valid() && $this->applyFilter();
    }

    public function key() {
        return $this->data->key();
    }

    public function current() {
        return $this->data->current();
    }

    public function next() {
        $this->data->next();
    }

    private function applyFilter() {
        $fn = $this->filter;

        if (is_callable($this->filter)) {
            return $fn($this->data->current(), $this->data->key());
        } elseif (is_int($this->filter)) {
            return $this->data->key() < $this->filter;
        }
    }
}
