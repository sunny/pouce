<?php
/*
 * Inode class
 *
 * Inode objects represents the underlying file or directory used in Pouce,
 * is inherited by File and Dir classes.
 */
class Inode {
  // Constructor takes a full path
  // Example: new Inode('/var/www/things')
  function __construct($path) {
    $this->path = preg_replace('#//+#', '/', $path); // remove multiple slashes
  }

  // Returns the Inode's last part
  // Example: $inode->name() #=> "things"
  function name() {
    return basename($this->path);
  }

  // Alias towards name()
  function uri() {
    return $this->name();
  }

  // Returns true if the Inode is a file
  function is_file() {
    return is_file($this->path);
  }

  // Returns true if the Inode is a directory
  function is_dir() {
    return is_dir($this->path);
  }

  // Returns the file size in a string or "-" if not applicable
  function size() {
    return '-';
  }

  // Returns the file type
  function type() {
    return 'inode';
  }

  // Returns a URI towards the image depending on its type
  function icon() {
    return POUCE_LIB_URI . '/images/' . POUCE_IMG_SET . '/' . $this->type() . '.png';
  }

  // Returns a File or Dir object (both inherit from Inode) from the given path
  // Example:
  //  Inode::repr('/var/www/stuff/') # => <Dir>
  //  Inode::repr('/var/www/file.txt') #=> <File>
  static
  function repr($path) {
    if (is_dir($path))
      return new Dir($path);
    if (is_file($path))
      return new File($path);
    return false;
  }
}
