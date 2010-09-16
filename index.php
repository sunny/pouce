<?php
require dirname(__FILE__).'/lib/pouce.php';
$page = new Pouce(urldecode($_SERVER['REQUEST_URI']));
?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <title><?php echo h($page->name()) ?></title>
  <link rel="shortcut icon" href="<?php echo h($page->icon()) ?>" />
  <style>
body {font: 1.1em Helvetica, sans-serif; padding:1em; background:white; color:black}
h1 {color: #fff;background:black;padding:.3ex .53ex;display:inline}
h1 a {text-decoration:none;color:#ddd;}
h1 a:hover {text-decoration:underline}
table {color: #333;margin-top:1em}
table a {text-decoration:none;background:crimson;color:white;padding:.1ex .5ex;margin-right:1.15em}
table a:hover{background:darkred;margin-right:0}
table a:hover::after{content:' ➭';width:1.15em}
table a:focus{outline:2px solid red}
table a:active{background:pink}
:focus::after{content:' ⌨'}
table a:visited{background:darkred}
a.viewsource {text-decoration:none; background:transparent; color:crimson;margin-right:0}
a.viewsource:hover {text-decoration:underline;background:transparent;margin-right:0}
a.viewsource:hover::after{content:'';width:auto}
a.viewsource:visited{background:transparent;color:darkred}
a.viewsource:active{background:transparent}
.size{color:#666}
<?php echo $page->style(); ?>

  </style>
</head>
<body>

<h1><?php echo $page->fancy_path() ?></h1>

<?php if ($page->not_found() or $page->text()) : ?>
<div class="text">
  <?php if ($page->not_found()) : ?>

  <p>Fichier inconnu.</p>
  <?php endif; ?>

  <?php echo $page->text(); ?>

</div>
<? endif; ?>

<?php if ($page->is_dir()) : ?>

<table>
  <tbody>
<?php foreach ($page->files() as $file) : ?>

    <tr>
      <td><img src="<?php echo h($file->icon()) ?>" alt="<?php echo h($file->type()) ?>"
             title="<?php echo h($file->type()) ?>" /></td>
      <td>
        <a href="<?php echo h($file->uri()) ?>"><?php echo h($file->name()) ?></a>
        <?php if ($file->is_file() and $file->is_text()) : ?><a class="viewsource" title="View source" href="?<?php echo h($file->uri()) ?>">:&gt;</a><?php endif; ?>
      </td>
      <td class="size"><?php echo h($size); ?></td>
    </tr>
<?php endforeach; ?>

  </tbody>
</table>

<?php endif; ?>

</body>
</html>
