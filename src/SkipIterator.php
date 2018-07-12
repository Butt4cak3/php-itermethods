<?php
namespace itermethods;

class SkipIterator implements \Iterator {
    private $data;
    private $filter;
    private $key = 0;

    public function __construct($data, $filter) {
        $this->data = $data;
        $this->filter = $filter;
    }

    public function rewind() {
        $fn = $this->filter;
        $this->data->rewind();
        $this->key = 0;

        if (is_callable($this->filter)) {
            while ($this->data->valid() && $fn($this->data->current(), $this->data->key())) {
                $this->data->next();
            }
        } elseif (is_int($this->filter)) {
            while ($this->data->valid() && $this->data->key() < $this->filter) {
                $this->data->next();
            }
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
        $this->key += 1;
        $this->data->next();
    }
}
