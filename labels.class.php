<?php
/*
 * Returns the OS-X Label for a file by looking at a cache file in the format:
 *   5 /Users/you/path
 *   0 /Users/you/otherpath
 *
 * Label index is one of:
 *   0 => transparent
 *   1 => red
 *   2 => orange
 *   3 => yellow
 *   4 => green
 *   5 => blue
 *   6 => magenta
 *   7 => grey
 *
 * To use this you need to fill the cache/labels.txt file.
 **/

define('LABEL_CACHE_FILE', dirname(__FILE__) . '/cache/labels.txt');

class Labels {

  function __construct() {
    $this->paths = array();
    $this->load_cache();
  }

  function load_cache() {
    if (!file_exists(LABEL_CACHE_FILE))
      return;
    $cache = file_get_contents(LABEL_CACHE_FILE);
    $paths = explode("\n", $cache);
    foreach ($paths as $data) {
      list($index, $path) = explode(' ', $data, 2);
      $this->paths[$path] = $index;
    }
  }

  function index_for($path) {
    return isset($this->paths[$path]) ? $this->paths[$path] : 0;
  }

  function color_for($path) {
    global $LABELS_COLORS;
    $index = $this->index_for($path);
    return $LABELS_COLORS[$index];
  }

}

