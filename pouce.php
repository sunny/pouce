<?php
/*
 * Pouce
 * Cute directory listing
 * (As opposed to the index)
 *
 * By Sunny Ripert - <sunny@sunfox.org>
 * Thanks to webs & tslh for their awesomeness
 * 
 * Licenced under the GNU General Public License
 * http://www.gnu.org/copyleft/gpl.html
 *
 * This file includes the Pouce, Inode, File, Dir classes and a few helpers
 * see dir-generator.php for an example.
 */

require 'geshi.php';

if (!defined('POUCE_LIB_URI'))   define('POUCE_LIB_URI', '/pouce');
if (!defined('POUCE_ROOT_NAME')) define('POUCE_ROOT_NAME', $_SERVER['HTTP_HOST']);

class Pouce {
  function __construct($uri) {
    $this->uri = $uri;
    if (realpath($_SERVER['DOCUMENT_ROOT'] . $this->uri) == realpath($_SERVER['SCRIPT_FILENAME']))
      $this->uri = implode('/', $this->uri_folders());
    $this->inode = Inode::repr($this->path());
  }
  
  // Current page
  function page() {
    return basename($this->uri);
  }
  
  // Current page starts with a question mark
  function wants_file() {
    $page = $this->page();
    return $page[0] == '?';
  }
  
  // Good looking name of current page
  function name() {
    $name = ($this->wants_file()) ? substr($this->page(), 1) : $this->page();
    return $name ? $name : POUCE_ROOT_NAME;
  }

  // File path towards current page
  function path() {
    $path = $this->wants_file() ? implode('/', $this->uri_folders()) . '/' . $this->name() : $this->uri;
    return $_SERVER['DOCUMENT_ROOT'] . '/' . $path;
  }

  // HTML links to upper level folders
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
  
  // Array of upper level folders
  function uri_folders() {
    $folders = $this->uri_parts();
    array_pop($folders);
    return $folders;
  }
  
  // Array of not-empty parts of the URI
  function uri_parts() {
    $parts = explode('/', $this->uri);
    $folders = array();
    foreach ($parts as $name)
      if ($name)
        $folders[] = $name;
    return $folders;
  }

  // Shortcuts to underlying inode
  function is_file() {
    return $this->inode !== false ? $this->inode->is_file() : false;
  }

  function is_dir() {
    return $this->inode !== false ? $this->inode->is_dir() : false;
  }

  function not_found() {
    return !$this->is_file() and !$this->is_dir();
  }
  
  function type() {
    return $this->inode === false ? '' : $this->inode->type();
  }
  
  function files() {
    return $this->is_dir() ? $this->inode->files() : array();
  }
  function icon() {
    $inode = ($this->inode) ? $this->inode : new Inode('.');
    return $inode->icon();
  }
  
  // Return text from README file in the directory
  function readme() {
    foreach ($this->files() as $file)
      if ($file->name() == 'README')
        return '<p>'.nl2br(h($file->contents())).'</p>';
    return '';
  }
  
  // Geshi syntax highlighting
  function geshi() {
    if ($this->is_file() === false) return false;
    if (isset($this->geshi)) return $this->geshi;
    $this->geshi = new GeSHi($this->inode->contents(), $this->inode->language());
    $this->geshi->enable_classes();
    $this->geshi->enable_keyword_links(false);
    return $this->geshi;
  }  
  function style() {
    return $this->geshi() ? $this->geshi()->get_stylesheet() : '';
  }
  function text() {
    if ($this->geshi())
      return $this->geshi()->parse_code();
    return $this->readme();
  }
}

class Inode {
  function __construct($path) {
    $this->path = $path;
  }
  function name() {
    return basename($this->path);
  }
  function uri() {
    return $this->name();
  }
  function is_file() {
    return is_file($this->path);
  }
  function is_dir() {
    return is_dir($this->path);
  }
  function size() {
    return '-';
  }
  function type() {
    return 'inode';
  }
  function extension() {
    return $this->name();
  }
  function language() {
    return '';
  }
  function text() {
    return '';
  }
  function icon() {
    return POUCE_LIB_URI . '/images/' . $this->type() . '.png';
  }  

  static
  function repr($path) {
    if (is_dir($path))
      return new Dir($path);
    if (is_file($path))
      return new File($path);
    return false;
  }
}

class File extends Inode {
  function contents() {
    return file_get_contents($this->path);
  }
  function extension() {
    $ext = substr(strrchr($this->name(), '.'), 1);
    return $ext ? $ext : $this->name();
  }

  // File-type from extension in Tango naming style
  function type() {
    if ($this->name() == 'README')
      return 'text-x-generic';

    $extensions = array(
      'png' => 'image-x-generic',
      'gif' => 'image-x-generic',
      'jpeg' => 'image-x-generic',
      'jpg' => 'image-x-generic',
      'bmp' => 'image-x-generic',
      'tiff' => 'image-x-generic',
      'psd' => 'image-x-generic',
      'psp' => 'image-x-generic',
      'svg' => 'image-x-generic',
      'xcf' => 'image-x-generic',
      'ico' => 'image-x-generic',

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

      'htm' => 'text-html',
      'html' => 'text-html',
      'shtml' => 'text-html',
      'dhtml' => 'text-html',
      'rhtml' => 'text-html',
      'erb' => 'text-html', # TODO support double extension of use regexp

      'php' => 'text-x-script',
      'php3' => 'text-x-script',
      'rb' => 'text-x-script',
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

      'gz' => 'package-x-generic',
      'tar' => 'package-x-generic',
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
      
      'ttf' => 'font',
      
      'txt' => 'text-x-generic',
      'log' => 'text-x-generic',
    );
    if (!array_key_exists($this->extension(), $extensions))
      return 'application-x-executable';
    return $extensions[$this->extension()];
  }
  
  // Can be read as code or plain text ?
  function is_text() {
    return startswith($this->type(), 'text-');
  }
  
  // Code languages, useful for syntax highlighting like GeSHi
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
      'Rakefile' => 'ruby',
      'rhtml' => 'rails',
      'sh' => 'bash',
    );
    if (!array_key_exists($this->extension(), $languages))
      return $this->extension();
    return $languages[$this->extension()];
  }
  
  // String representation of file size
  function size() {
    $size = filesize($this->path);
    $symbols = array(1 => "k", "M", "G", "T", "P", "E", "Z", "Y");
    for ($i = 0; $i < count($symbols) - 1 && $size >= 1000; $i++)
      $size /= 1000;
    $p = strpos($val, '.');
    if ($p !== false)
      $size = $p > 3 ? round($size) : round($size, 3 - $p);
    return round($size, 3) . ' ' . $symbols[$i] . 'B';
  }
}

class Dir extends Inode {
  function name() {
    return basename($this->path).'/';
  }
  function type() {
    return 'folder';
  }
  
  // Return sorted array of Dir and File objects
  function files() {
    $files = array();
    foreach ($this->filenames() as $file) {
      $files[] = Inode::repr($this->path . '/' . $file);
    }
    return $files;
  }
  
  // Return sorted array of foldernames and filenames
  function filenames() {
    $filenames = $foldernames = array();
    $handle = opendir($this->path);
    while (false !== ($file = readdir($handle))) {
      if ($file[0] != '.') {
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


// Helpers
function h($t) {
	return htmlspecialchars($t);
}
function startswith($hay, $needle) {
	return $needle === $hay or strpos($hay, $needle) === 0;
}
function endswith($hay, $needle) {
    return $needle === $hay or strpos(strrev($hay), strrev($needle)) === 0;
}


