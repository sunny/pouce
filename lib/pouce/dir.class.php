<?php
/*
 * Dir class
 *
 * A more specific Inode for directories
 */
class Dir extends Inode {
  // Returns the name of the directory ending with a slash
  function name() {
    return basename($this->path).'/';
  }

  // Returns "folder"
  function type() {
    return 'folder';
  }

  // Returns a sorted array of its Dir and File children
  function files() {
    $files = array();
    foreach ($this->filenames() as $file) {
      if ($repr = Inode::repr($this->path . '/' . $file))
        $files[] = $repr;
    }
    return $files;
  }

  // Returns a sorted array of the children folders followed by the children
  // files as strings
  function filenames() {
    $filenames = $foldernames = array();
    $handle = opendir($this->path);
    while (false !== ($file = readdir($handle))) {
      if (!preg_match(IGNORED_FILENAMES, $file)) {
        if (is_dir($this->path . '/' . $file))
          $foldernames[] = $file;
        else
          $filenames[] = $file;
      }
    }
    closedir($handle);
    natcasesort($foldernames);
    natcasesort($filenames);
    return array_merge($foldernames, $filenames);
  }
}
