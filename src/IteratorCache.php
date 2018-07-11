<?php
namespace itermethods;

class IteratorCache implements \Iterator {
    private $data;
    private $cache = [];
    private $cacheSize = 0;
    private $key;

    public function __construct($data) {
        $this->data = $data;
    }

    private function cacheCurrent() {
        if ($this->data->valid()) {
            $this->cache[] = $this->data->current();
            $this->cacheSize += 1;
        }
    }

    public function rewind() {
        if ($this->key === null) {
            $this->data->rewind();
        }

        $this->key = 0;
        $this->cacheCurrent();
    }

    public function valid() {
        if ($this->key < $this->cacheSize) {
            return true;
        } else {
            return $this->data->valid();
        }
    }

    public function key() {
        return $this->key;
    }

    public function current() {
        return $this->cache[$this->key];
    }

    public function next() {
        $this->key += 1;

        if ($this->key >= $this->cacheSize) {
            $this->data->next();
            $this->cacheCurrent();
        }
    }
}
