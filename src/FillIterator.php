<?php
namespace itermethods;

class FillIterator implements \Iterator {
    private $data;
    private $value;
    private $start;
    private $end;

    public function __construct($data, $value, $start, $end) {
        $this->data = $data;
        $this->value = $value;
        $this->start = intval($start);
        $this->end = intval($end);
    }

    public function rewind() {
        $this->data->rewind();
    }

    public function valid() {
        return $this->data->valid();
    }

    public function current() {
        $key = $this->key();

        if ($key >= $this->start && $key < $this->end) {
            return $this->value;
        } else {
            return $this->data->current();
        }
    }

    public function next() {
        $this->data->next();
    }

    public function key() {
        return $this->data->key();
    }
}
