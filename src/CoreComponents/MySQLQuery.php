<?php
namespace App\CoreComponents;

use mysqli_result as MySQLResult;
use Iterator;
use Exception;

class MySQLQuery implements Iterator {

    protected $query;
    protected $lastrec;
    protected $_index;
    protected $_row;

    public function __construct(MySQLResult $query) {
        if (!empty($query)) {
            $this->query = $query;
            $this->_row = false;
        }
    }

    public function __destruct() {
        if (!empty($this->query)) {
            $this->query->close();
        }
    }

    public function count() {
        if (!empty($this->query)) {
            return $this->query->num_rows;
        }

        return 0;
    }

    public function rewind() {
        $this->_row = $this->query->fetch_assoc();
        $this->_index = 0;
    }

    public function current() {
        return $this->_row;
    }

    public function key() {
        return $this->_index;
    }

    public function next() {
        if (!empty($this->query)) {
            $this->_row = $this->query->fetch_assoc();
            $this->_index++;
        }
    }

    public function valid() {
        return $this->_row !== false && $this->_row !== null;
    }

}
