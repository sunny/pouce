<?php

if (is_readable(dirname(__FILE__).'/config.php'))
  include dirname(__FILE__).'/config.php';

if (!defined('POUCE_LIB_URI'))     define('POUCE_LIB_URI', '/pouce');
if (!defined('POUCE_ROOT_NAME'))   define('POUCE_ROOT_NAME', $_SERVER['HTTP_HOST']);
if (!defined('IGNORED_FILENAMES')) define('IGNORED_FILENAMES', '/^(\.|\.\.|\.DS_Store|Icon.)$/');
if (!defined('PLAIN_FILES'))       define('PLAIN_FILES', '/^(README|\.htaccess|\.gitignore)$/');

require 'geshi.php';

/*
 * Pouce class
 *
 * A Pouce object represents a page to show, either a directory or the contents
 * of a file.
 */
class Pouce {
  // Constructer takes a URI
  // Example: new Pouce('/foo/bar')
  function __construct($uri) {
    $this->uri = $uri;
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
      $string .= '/<a href="'.h($link).'">'.h($folder).'</a>';
    }
    $string .= '/' . $this->name();
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
    return POUCE_LIB_URI . '/images/' . $this->type() . '.png';
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

/*
 * File class
 *
 * A more specific Inode for files
 */
class File extends Inode {
  // Returns the file contents as a string
  function contents() {
    return file_get_contents($this->path);
  }

  // Returns the file's extension
  // Example: new File('/var/www/foo.php')->extension() # => 'php'
  function extension() {
    $ext = substr(strrchr($this->name(), '.'), 1);
    return $ext ? $ext : $this->name();
  }

  // Returns the file-type from it's extension (using the Tango naming style)
  // Example: new File('/var/www/foo.php')->type() # => 'text-x-script'
  function type() {
    if (preg_match(PLAIN_FILES, $this->name()))
      return 'text-x-generic';

    $extensions = array(
      'png' => 'image-x-generic',
      'gif' => 'image-x-generic',
      'jpeg' => 'image-x-generic',
      'jpg' => 'image-x-generic',
      'bmp' => 'image-x-generic',
      'tif' => 'image-x-generic',
      'tiff' => 'image-x-generic',
      'psd' => 'image-x-generic',
      'psp' => 'image-x-generic',
      'svg' => 'image-x-generic',
      'xcf' => 'image-x-generic',
      'ico' => 'image-x-generic',
      'icns' => 'image-x-generic',
      'ai' => 'image-x-generic',
      'indd' => 'image-x-generic',
      'eps' => 'image-x-generic',

      'mp3' => 'audio-x-generic',
      'ogg' => 'audio-x-generic',
      'flac' => 'audio-x-generic',
      'wav' => 'audio-x-generic',
      'aif' => 'audio-x-generic',
      'aiff' => 'audio-x-generic',
      'aifc' => 'audio-x-generic',
      'cda' => 'audio-x-generic',
      'm3u' => 'audio-x-generic',
      'mid' => 'audio-x-generic',
      'mod' => 'audio-x-generic',
      'mp2' => 'audio-x-generic',
      'snd' => 'audio-x-generic',
      'voc' => 'audio-x-generic',
      'wma' => 'audio-x-generic',

      'avi' => 'video-x-generic',
      'wmv' => 'video-x-generic',
      'qt' => 'video-x-generic',
      'mkv' => 'video-x-generic',
      'flv' => 'video-x-generic',
      'mpg' => 'video-x-generic',
      'ram' => 'video-x-generic',
      'mov' => 'video-x-generic',
      'mp4' => 'video-x-generic',

      'htm' => 'text-html',
      'html' => 'text-html',
      'shtml' => 'text-html',
      'dhtml' => 'text-html',
      'rhtml' => 'text-html',
      'erb' => 'text-html', # TODO support double extensions .html.erb .txt.erb

      'php' => 'text-x-script',
      'php3' => 'text-x-script',
      'rb' => 'text-x-script',
      'ru' => 'text-x-script',
      'Rakefile' => 'text-x-script',
      'py' => 'text-x-script',
      'c' => 'text-x-script',
      'h' => 'text-x-script',
      'asp' => 'text-x-script',
      'cgi' => 'text-x-script',
      'fcgi' => 'text-x-script',
      'cpp' => 'text-x-script',
      'css' => 'text-x-script',
      'jav' => 'text-x-script',
      'java' => 'text-x-script',
      'pl' => 'text-x-script',
      'pm' => 'text-x-script',
      'sql' => 'text-x-script',
      'js' => 'text-x-script',
      'sh' => 'text-x-script',
      'fish' => 'text-x-script',
      'txt' => 'text-x-script',
      'conf' => 'text-x-script',
      'yml' => 'text-x-script',
      'ini' => 'text-x-script',
      'patch' => 'text-x-script',

      'gz' => 'package-x-generic',
      'tar' => 'package-x-generic',
      'tgz' => 'package-x-generic',
      'zip' => 'package-x-generic',
      'rar' => 'package-x-generic',
      'bz' => 'package-x-generic',
      'deb' => 'package-x-generic',
      'rpm' => 'package-x-generic',
      '7z' => 'package-x-generic',
      'ace' => 'package-x-generic',
      'cab' => 'package-x-generic',
      'arj' => 'package-x-generic',
      'msi' => 'package-x-generic',

      'doc' => 'x-office-document',
      'odt' => 'x-office-document',
      'pdf' => 'x-office-document',
      'rtf' => 'x-office-document',
      'xls' => 'x-office-spreadsheet',

      // TODO add icon for fonts
      // 'ttf' => 'font',

      'txt' => 'text-x-generic',
      'markdown' => 'text-x-generic',
      'log' => 'text-x-generic',
    );

    if (!array_key_exists(strtolower($this->extension()), $extensions))
      return 'application-x-executable';
    return $extensions[strtolower($this->extension())];
  }

  // Returns true if the file can be read as plain text
  function is_text() {
    return preg_match('/^text-/', $this->type());
  }

  // Return fullname of the language for the code
  // Useful for GeSHi's syntax highlighting
  function language() {
    if ($this->is_dir()) return '';
    $languages = array(
      'as' => 'actionscript',
      'cs' => 'csharp',
      'erb' => 'xml',
      'fish' => 'bash',
      'htaccess' => 'apache',
      'html' => 'html4strict',
      'js' => 'javascript',
      'pl' => 'perl',
      'py' => 'python',
      'rb' => 'ruby',
      'ru' => 'ruby',
      'Rakefile' => 'ruby',
      'rhtml' => 'rails',
      'sh' => 'bash',
    );
    if (!array_key_exists($this->extension(), $languages))
      return $this->extension();
    return $languages[$this->extension()];
  }

  // Returns a string representation of the file size if bytes
  function size() {
    $size = filesize($this->path);
    $symbols = array("", "k", "M", "G", "T", "P", "E", "Z", "Y");
    for ($i = 0; $i < count($symbols) - 1 && $size >= 1000; $i++)
      $size /= 1000;
    $p = strpos($size, '.');
    if ($p !== false)
      $size = $p > 3 ? round($size) : round($size, 3 - $p);
    return round($size, 3) . ' ' . $symbols[$i] . 'B';
  }
}

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
    foreach ($this->filenames() as $file)
      $files[] = Inode::repr($this->path . '/' . $file);
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


// Helper Alias to escape HTML entities in the view
// Example: h("42>0") # => "42&gt;0"
function h($t) {
  return htmlspecialchars($t);
}

