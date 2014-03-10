<?php

if (is_readable(dirname(__FILE__).'/../config.php'))
  include dirname(__FILE__).'/../config.php';

if (!defined('POUCE_LIB_URI'))     define('POUCE_LIB_URI', '/pouce');
if (!defined('POUCE_ROOT_NAME'))   define('POUCE_ROOT_NAME', $_SERVER['HTTP_HOST']);
if (!defined('IGNORED_FILENAMES')) define('IGNORED_FILENAMES', '/^(\.|\.\.|\.DS_Store|Icon.)$/');
if (!defined('PLAIN_FILES'))       define('PLAIN_FILES', '/^(README|\.htaccess|\.gitignore)$/');

require dirname(__FILE__).'/geshi.php';
require dirname(__FILE__).'/pouce/pouce.class.php';
require dirname(__FILE__).'/pouce/inode.class.php';
require dirname(__FILE__).'/pouce/file.class.php';
require dirname(__FILE__).'/pouce/dir.class.php';
require dirname(__FILE__).'/pouce/helpers.php';
