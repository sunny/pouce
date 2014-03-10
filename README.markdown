Pouce
=====

Cute PHP directory lister. Like an "index-of".

Licenced under the GNU General Public License
(http://www.gnu.org/copyleft/gpl.html)

Install
-------

1. [Download Pouce](https://github.com/sunny/pouce/archive/master.zip)

2. Place it on your webserver, for example under `/pouce`.

3. Point your empty directories to Pouce's `index.php`.

    This configuration varies depending on your webserver.

    - With Apache you can add this line inside an `.htaccess`:

            DirectoryIndex index.php index.html /pouce/index.php

    - With Nginx, you can add this line to your configuration:

            index index.html index.htm index.php /pouce/index.php;

Configuration
-------------

If you intend to host it elsewhere than `/pouce` or change [any option](https://github.com/sunny/pouce/blob/master/config.example.php), you can copy `config.example.php` to `config.php` and edit the configuration at your liking.
