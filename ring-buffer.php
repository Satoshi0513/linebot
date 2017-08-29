<?php
class RingBuffer {
  private $buf = array();
  private $top;
  private $bottom;
  private $size;

//constructor
  public function __construct($size) {
    $this->size = $size;
    $this->buf = array_fill(0,$size,null);
    $this->top = 0;
    $this->bottom = -1;
  }
//get data
  public function get($index) {
    $i = ($this->top + $index) % $this->size;
    return $this->buf[$i];
  }

  //set data to index
  public function set($index, $v) {
    $i = ($this->top + $index) % $this->size;
    $this->buf[$i] = $v;
  }

  //put data on last
  public function append($v) {
     $file = FALSE;
    if (($this->bottom - 1) >= $this->size) {// keep oldest file before delete
      $key = ($this->bottom + 1) % $this->size;
      $file = $this->get($key);
    }
    $this->bottom = ($this->bottom + 1) % $this->size;
    $this->buf[$this->bottom] = $v;
    if ($this->top >= $this->bottom) {
      $this->top = $this->bottom + 1;
    }
    return $file;
  }

  public function search($item) {
    $i = array_search($item,$this->buf);
    return $i;
  }


}
