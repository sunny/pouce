<?php
/*
 * Pouce class
 *
 * A Pouce object represents a page to show, either a directory or the contents
 * of a file.
 */
class Pouce {
  // Constructer takes a URI
  // Example: new Pouce('/foo/bar')
  // Example: new Pouce('/foo/?bar')
  function __construct($uri) {
    $this->uri = preg_replace('/\/\?/', '/', $uri); // remove '?' in url
    if (realpath($_SERVER['DOCUMENT_ROOT'] . $this->uri) == realpath($_SERVER['SCRIPT_FILENAME']))
      $this->uri = implode('/', $this->uri_folders());
    $this->inode = Inode::repr($this->path());
  }

  // Returns the current page name
  function page() {
    return basename($this->uri);
  }

  // Returns true if the current page starts with a question mark
  // For example "/files/?file.txt" means the user want to see file.txt's source
  function wants_file() {
    $page = $this->page();
    return isset($page[0]) and $page[0] == '?';
  }

  // Returns a good looking name of the current page, without a question mark
  // Returns the root name if the pagename is empty
  function name() {
    $name = ($this->wants_file()) ? substr($this->page(), 1) : $this->page();
    return $name ? $name : POUCE_ROOT_NAME;
  }

  // Returns the full file path towards the current file or directory
  function path() {
    $path = $this->wants_file() ? implode('/', $this->uri_folders()) . '/' . $this->name() : $this->uri;
    return $_SERVER['DOCUMENT_ROOT'] . '/' . $path;
  }

  // Returns HTML links towards the upper level folders seperated by "/"
  function fancy_path() {
    if (count($this->uri_parts()) == 0)
      return h(POUCE_ROOT_NAME);

    $link = '/';
    $string = '<a href="/">'.h(POUCE_ROOT_NAME).'</a>';
    foreach ($this->uri_folders() as $folder) {
      $link .= $folder . '/';
      $string .= ' / <a href="'.h($link).'">'.h($folder).'</a>';
    }
    $string .= ' / ' . $this->name();
    return $string;
  }

  // Returns an array of upper level folder names
  function uri_folders() {
    $folders = $this->uri_parts();
    array_pop($folders);
    return $folders;
  }

  // Returns an array of the different parts of the URI
  function uri_parts() {
    $parts = explode('/', $this->uri);
    $folders = array();
    foreach ($parts as $name)
      if ($name)
        $folders[] = $name;
    return $folders;
  }

  /* Here come shortcuts to the underlying Inode object */

  // Returns wether the page is a file
  function is_file() {
    return $this->inode !== false ? $this->inode->is_file() : false;
  }

  // Returns wether the page is a directory
  function is_dir() {
    return $this->inode !== false ? $this->inode->is_dir() : false;
  }

  // Returns wether the page exists
  function not_found() {
    return !$this->is_file() and !$this->is_dir();
  }

  // Returns a string with the page's file type or "directory"
  function type() {
    return $this->inode === false ? '' : $this->inode->type();
  }

  // Returns array of children files for directories
  function files() {
    return $this->is_dir() ? $this->inode->files() : array();
  }

  // Returns the file's icon URI
  function icon() {
    // FIXME why is this necessary?
    $inode = ($this->inode) ? $this->inode : new Inode('.');
    return $inode->icon();
  }


  // Returns HTML from the contents of the README file if it exists in the directory
  function readme() {
    foreach ($this->files() as $file)
      if ($file->name() == 'README')
        return '<p>'.nl2br(h($file->contents())).'</p>';
    return '';
  }

  // Applies the GeSHi syntax highlighter around the file's contents
  // and returns the Geshi object
  function geshi() {
    if ($this->is_file() === false) return false;
    if (isset($this->geshi)) return $this->geshi;
    $this->geshi = new GeSHi($this->inode->contents(), $this->inode->language());
    $this->geshi->enable_classes();
    $this->geshi->enable_line_numbers(true);
    $this->geshi->enable_keyword_links(false);
    return $this->geshi;
  }

  // Returns the geshi
  function style() {
    return $this->geshi() ? $this->geshi()->get_stylesheet() : '';
  }

  // Returns HTML for the highlighted text from the page or the README file
  function text() {
    if ($this->geshi())
      return $this->geshi()->parse_code();
    return $this->readme();
  }
}
