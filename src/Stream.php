<?php
namespace itermethods;

class Stream implements \Iterator {
    private $data;

    /**
     * Alias for new Stream($data)
     *
     * @param mixed $data An array or Iterable
     * @return Stream
     */
    public static function from($data) {
        return new static($data);
    }

    /**
     * Creates a Stream in which every value is NULL.
     *
     * @param int $length The number of items in the stream
     * @return Stream
     */
    public static function nulls($length) {
        return new static(new NullIterator($length));
    }

    /**
     * Creates a Stream in which the values are a series of numbers.
     *
     * @param int $start The start of the series (inclusive)
     * @param int $end The end of the series (exclusive)
     * @return Stream
     */
    public static function range($start = null, $end = null) {
        if ($start !== null && $end === null) {
            $end = $start;
            $start = 0;
        } elseif ($start === null && $end === null) {
            $start = 0;
            $end = PHP_INT_MAX;
        }

        return new static(new RangeIterator($start, $end));
    }

    /**
     * Creates a new Stream instance from an array or Iterable object.
     *
     * @param mixed $data An array or Iterable
     */
    public function __construct($data) {
        if ($data instanceof \Iterator) {
            $this->data = $data;
        } elseif (is_array($data)) {
            $this->data = new \ArrayIterator($data);
        } else {
            throw new \TypeError("\$data must be an Iterator");
        }
    }

    /**
     * Alias for collect.
     *
     * @return array
     */
    public function toArray() {
        $result = [];

        foreach ($this->data as $value) {
            $result[] = $value;
        }

        return $result;
    }

    /**
     * Takes all data in the stream and puts it in an array.
     *
     * @return array
     */
    public function collect() {
        return $this->toArray();
    }

    // Data operations

    /**
     * Returns a new Stream that caches data in memory.
     *
     * @return Stream
     */
    public function cache() {
        return new Stream(new IteratorCache($this->data));
    }

    /**
     * Creates a new Stream that is the result of the concatenation of the current Stream and an Iterable.
     *
     * @param Iterable $other
     * @return Stream
     */
    public function concat($other) {
        return new Stream(new ConcatIterator([$this->data, $other]));
    }

    /**
     * Creates a new Stream that returns key/value pairs of the current Stream.
     *
     * @return Stream
     */
    public function entries() {
        return new Stream(new EntriesIterator($this->data));
    }

    /**
     * Checks whether all items in the Stream pass a given test.
     *
     * @param callable $fn A test function that runs for every item
     * @return bool
     */
    public function every($fn) {
        if (!is_callable($fn)) throw new \TypeError("\$fn must be callable");

        foreach ($this->data as $key => $value) {
            if (!$fn($value, $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Fills a given range of the Stream with a fixed value.
     *
     * @param mixed $value The fill value
     * @param int $start Start of the replaced range (inclusive)
     * @param int $end End of the replaced range (exclusive)
     * @return Stream
     */
    public function fill($value, $start = 0, $end = PHP_INT_MAX) {
        return new Stream(new FillIterator($this->data, $value, $start, $end));
    }

    /**
     * Filters the stream so that only items that pass a given test remain.
     *
     * @param callable $filter Test function
     * @return Stream
     */
    public function filter($filter) {
        return new Stream(new FilterIterator($this->data, $filter));
    }

    /**
     * Finds the first item that passes a given test and return it.
     *
     * @param callable $filter Test function
     * @return mixed
     */
    public function find($filter) {
        foreach ($this->data as $key => $value) {
            if ($filter($value, $key)) {
                return $value;
            }
        }
    }

    /**
     * Finds the first item that passes a given test and return its index.
     *
     * @param callable $filter Test function
     * @return int
     */
    public function findIndex($filter) {
        foreach ($this->data as $key => $value) {
            if ($filter($value, $key)) {
                return $key;
            }
        }

        return -1;
    }

    /**
     * Finds out whether a given item exists in the stream.
     *
     * @param mixed $search The item to search for
     * @param int $fromIndex When specified, the search begins at this index
     * @return bool
     */
    public function includes($search, $fromIndex = 0) {
        foreach ($this->data as $key => $value) {
            if ($key >= $fromIndex && $value === $search) {
                return true;
            }
        }

        return false;
    }

    /**
     * Finds the first occurence of an item in the stream and returns its index.
     *
     * @param mixed $search The item to search for
     * @param int $fromIndex When specified, the search begins at this index
     * @return int
     */
    public function indexOf($search, $fromIndex = 0) {
        foreach ($this->data as $key => $value) {
            if ($key >= $fromIndex && $value === $search) {
                return $key;
            }
        }

        return -1;
    }

    /**
     * Converts all items to strings and joins them into a single string.
     *
     * @param string $separator A string that appears between every two items
     * @return string
     */
    public function join($separator = "") {
        $result = "";

        $first = true;
        foreach ($this->data as $item) {
            if ($first) {
                $first = false;
            } else {
                $result .= $separator;
            }

            $result .= $item;
        }

        return $result;
    }

    /**
     * Creates a new Stream object with the keys of the current one as its values.
     *
     * @return Stream
     */
    public function keys() {
        return new Stream(new KeysIterator($this->data));
    }

    /**
     * Applies a function to every item and creates a Stream object that uses the results of this function as its data source.
     *
     * @param callable $fn A function that takes an item and returns a new item
     * @return Stream
     */
    public function map($fn) {
        return new Stream(new MapIterator($this->data, $fn));
    }

    /**
     * Calls a reducer function for every item to turn the Stream into a single value.
     *
     * @param callable $fn A function taking an accumulator and an item as parameters
     * @param mixed $initial The starting value of the reduction
     * @param bool $nullInitial When true and $initial is null, null will be used as the starting value
     * @return mixed
     */
    public function reduce($fn, $initial = null, $nullInitial = false) {
        $this->data->rewind();
        if ($initial === null && !$nullInitial) {
            $initial = $this->data->current();
            $this->data->next();
        }

        $acc = $initial;
        while ($this->data->valid()) {
            $acc = $fn($acc, $this->data->current(), $this->data->key());
            $this->data->next();
        }

        return $acc;
    }

    /**
     * If $filter is a number, skips this number of items at the start of the stream.
     *
     * If $filter is a test function, skip all items at the beginning of the stream that pass it.
     *
     * @param mixed $filter A number or test function
     * @return Stream
     */
    public function skip($filter) {
        return new Stream(new SkipIterator($this->data, $filter));
    }

    /**
     * Creates a new Stream with a given range of the data.
     *
     * @param int $begin The start of the new range (inclusive)
     * @param int $end The end of the new range (exclusive)
     * @return Stream
     */
    public function slice($begin = 0, $end = PHP_INT_MAX) {
        return new Stream(new SliceIterator($this->data, $begin, $end));
    }

    /**
     * Checks whether at least one item passes a given test.
     *
     * @param callable $filter A test function
     * @return bool
     */
    public function some($filter) {
        foreach ($this->data as $key => $value) {
            if ($filter($value, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * When $filter is a number, use only this number of items from the start of the stream.
     *
     * When $filter is a test function, use only items from the start that pass it.
     *
     * @param mixed $filter A number or test function
     * @return Stream
     */
    public function take($filter) {
        return new Stream(new TakeIterator($this->data, $filter));
    }

    // Iterator methods

    public function rewind() {
        $this->data->rewind();
    }

    public function current() {
        return $this->data->current();
    }

    public function key() {
        return $this->data->key();
    }

    public function valid() {
        return $this->data->valid();
    }

    public function next() {
        $this->data->next();
    }
}
