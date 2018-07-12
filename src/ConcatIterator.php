<?php
namespace itermethods;

class ConcatIterator implements \Iterator {
    private $sources;
    private $sourceIndex = 0;
    private $key = 0;

    public function __construct($sources) {
        $this->sources = $sources;
    }

    public function rewind() {
        $this->sourceIndex = 0;
        $this->key = 0;

        foreach ($this->sources as $source) {
            $source->rewind();
        }
    }

    public function valid() {
        return $this->sourceIndex < count($this->sources) && $this->getSource()->valid();
    }

    public function current() {
        return $this->getSource()->current();
    }

    public function next() {
        $this->key += 1;
        $this->getSource()->next();
        if (!$this->getSource()->valid()) {
            $this->sourceIndex += 1;
        }
    }

    public function key() {
        return $this->key;
    }

    private function getSource() {
        return $this->sources[$this->sourceIndex];
    }
}
