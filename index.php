<?php
require dirname(__FILE__).'/pouce.php';
$page = new Pouce(urldecode($_SERVER['REQUEST_URI']));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <title><?php echo h($page->name()) ?></title>
  <link rel="shortcut icon" type="image/png" href="<?php echo h($page->icon()) ?>" />
  <style type="text/css">
body {font: 90% Helvetica, sans-serif; padding:.5em; background:white; color:black}
h1 {color: #ccc}
h1 a {text-decoration: none; border-bottom: 1px solid #eee; color: #666}
table {color: #333}
.viewsource {text-decoration:none;}
<?php echo $page->style(); ?>
  </style>
</head>
<body>

<h1><?php echo $page->fancy_path() ?></h1>

<div class="text">
  <?php if ($page->not_found()) : ?>
  <p>Fichier inconnu.</p>
  <?php endif; ?>

  <?php echo $page->text(); ?>
</div>

<?php if ($page->is_dir()) : ?>

<table summary="Directory listing of folder <?php echo h($page->name()) ?>">
  <thead>
    <tr>
      <th>Type</th>
      <th>Name</th>
      <th>Size</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($page->files() as $file) : ?>
    <tr>
      <td><img src="<?php echo h($file->icon()) ?>" alt="<?php echo h($file->type()) ?>" /></td>
      <td>
        <a href="<?php echo h($file->uri()) ?>"><?php echo h($file->name()) ?></a>
        <?php if ($file->is_file() and $file->is_text()) : ?><a class="viewsource" title="View source" href="?<?php echo h($file->uri()) ?>">☭</a><?php endif; ?>
      </td>
      <td><?php echo h($file->size()) ?></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>

<?php endif; ?>

</body>
</html>
