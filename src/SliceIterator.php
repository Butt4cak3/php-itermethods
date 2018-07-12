<?php
namespace itermethods;

class SliceIterator implements \Iterator {
    private $data;
    private $begin;
    private $end;
    private $key;

    public function __construct($data, $begin = 0, $end = PHP_INT_MAX) {
        $this->data = $data;
        $this->begin = $begin;
        $this->end = $end;
    }

    public function rewind() {
        $this->data->rewind();
        $this->key = 0;

        while ($this->data->valid() && $this->data->key() < $this->begin) {
            $this->data->next();
        }
    }

    public function valid() {
        return $this->data->valid() && $this->data->key() < $this->end;
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
}
