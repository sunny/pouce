<?php
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
      'png'  => 'image-x-generic',
      'gif'  => 'image-x-generic',
      'jpeg' => 'image-x-generic',
      'jpg'  => 'image-x-generic',
      'bmp'  => 'image-x-generic',
      'tif'  => 'image-x-generic',
      'tiff' => 'image-x-generic',
      'psd'  => 'image-x-generic',
      'psp'  => 'image-x-generic',
      'xcf'  => 'image-x-generic',
      'ico'  => 'image-x-generic',
      'icns' => 'image-x-generic',
      'indd' => 'image-x-generic',

      'ai'   => 'vector-x-generic',
      'eps'  => 'vector-x-generic',
      'svg'  => 'vector-x-generic',

      'mp3'  => 'audio-x-generic',
      'ogg'  => 'audio-x-generic',
      'flac' => 'audio-x-generic',
      'wav'  => 'audio-x-generic',
      'aif'  => 'audio-x-generic',
      'aiff' => 'audio-x-generic',
      'aifc' => 'audio-x-generic',
      'cda'  => 'audio-x-generic',
      'm3u'  => 'audio-x-generic',
      'm4a'  => 'audio-x-generic',
      'mid'  => 'audio-x-generic',
      'mod'  => 'audio-x-generic',
      'mp2'  => 'audio-x-generic',
      'snd'  => 'audio-x-generic',
      'voc'  => 'audio-x-generic',
      'wma'  => 'audio-x-generic',

      'avi' => 'video-x-generic',
      'wmv' => 'video-x-generic',
      'qt'  => 'video-x-generic',
      'mkv' => 'video-x-generic',
      'flv' => 'video-x-generic',
      'mpg' => 'video-x-generic',
      'ram' => 'video-x-generic',
      'mov' => 'video-x-generic',
      'mp4' => 'video-x-generic',

      'htm'   => 'text-html',
      'html'  => 'text-html',
      'shtml' => 'text-html',
      'dhtml' => 'text-html',
      'rhtml' => 'text-html',
      'erb'   => 'text-html', # TODO support double extensions .html.erb .txt.erb

      'php'  => 'text-x-script-php',
      'php3' => 'text-x-script-php',

      'rb'       => 'text-x-script-ruby',
      'ru'       => 'text-x-script-ruby',
      'Rakefile' => 'text-x-script-ruby',

      'css'  => 'text-x-script-css',
      'scss' => 'text-x-script-css',

      'sql'  => 'text-x-script-database',

      'py'    => 'text-x-script',
      'c'     => 'text-x-script',
      'h'     => 'text-x-script',
      'asp'   => 'text-x-script',
      'cgi'   => 'text-x-script',
      'fcgi'  => 'text-x-script',
      'cpp'   => 'text-x-script',
      'jav'   => 'text-x-script',
      'java'  => 'text-x-script',
      'pl'    => 'text-x-script',
      'pm'    => 'text-x-script',
      'js'    => 'text-x-script',
      'sh'    => 'text-x-script',
      'fish'  => 'text-x-script',
      'txt'   => 'text-x-script',
      'conf'  => 'text-x-script',
      'yml'   => 'text-x-script',
      'ini'   => 'text-x-script',
      'patch' => 'text-x-script',

      'gz'  => 'package-x-generic',
      'tar' => 'package-x-generic',
      'tgz' => 'package-x-generic',
      'zip' => 'package-x-generic',
      'rar' => 'package-x-generic',
      'bz'  => 'package-x-generic',
      'deb' => 'package-x-generic',
      'rpm' => 'package-x-generic',
      '7z'  => 'package-x-generic',
      'ace' => 'package-x-generic',
      'cab' => 'package-x-generic',
      'arj' => 'package-x-generic',
      'msi' => 'package-x-generic',

      'doc' => 'x-office-document',
      'odt' => 'x-office-document',
      'rtf' => 'x-office-document',

      'xls' => 'x-office-spreadsheet',

      'pdf' => 'x-pdf-document',

      'ttf'  => 'font-x-generic',
      'woff' => 'font-x-generic',
      'eot'  => 'font-x-generic',

      'example'  => 'text-x-generic',
      'txt'      => 'text-x-generic',
      'markdown' => 'text-x-generic',
      'md'       => 'text-x-generic',
      'log'      => 'text-x-generic',
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
