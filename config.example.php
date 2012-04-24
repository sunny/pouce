<?php

/**
 * So that Pouce can find the images, Choose the URI where Pouce can
 * find its home path
 */
define('POUCE_LIB_URI', '/pouce');

/**
 * Choose the images set to use for icons.
 * Don't forget to include favicon.png, viewsource.png and type MIME png's.
 */
#define('POUCE_IMG_SET', 'images_folder_name');

/**
 * Choose the title of your pages (defaults to the hostname)
 */
#define('POUCE_ROOT_NAME', 'Files');

/**
 * Add to this regular expression files and directories which you don't
 * want to see in the listing
 */
#define('IGNORED_FILENAMES', '/^(\.|\.\.|\.DS_Store|Icon.)$/');

/**
 * Add to this regular expression text files that aren't reckognised
 * by their file extension.
 */
#define('PLAIN_FILES', '/^(README|\.htaccess|\.gitignore)$/');
