<?php
require dirname(__FILE__).'/lib/pouce.php';
$page = new Pouce(urldecode($_SERVER['REQUEST_URI']));
?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <title><?php echo h($page->name()) ?></title>
  <link rel="shortcut icon" href="/_pouce/images/<?= POUCE_IMG_SET ?>/favicon.png" />
  <link rel="stylesheet" href="/_pouce/css/global.css" media="all">
  <!-- GeSHi -->
  <style><?php echo $page->style(); ?></style>
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
  <thead>
    <tr>
      <th class="type">Type</th>
      <th class="name">Name</th>
      <th class="size">Size</th>
      <th class="source">Source</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($page->files() as $file) : ?>

    <tr>
      <td class="type">
        <img src="<?php echo h($file->icon()) ?>" alt="<?php echo h($file->type()) ?>" />
      </td>
      <td class="name">
        <a href="<?php echo h($file->uri()) ?>"><?php echo h($file->name()) ?></a>
      </td>
      <td class="size">
        <?php echo h($file->size()) ?>
      </td>
      <td  class="source">
        <?php if ($file->is_file() and $file->is_text()) : ?>
        <a class="viewsource" title="View source" href="?<?php echo h($file->uri()) ?>">
          <img src="/_pouce/images/<?= POUCE_IMG_SET ?>/viewsource.png" alt="">
        </a>
        <?php endif; ?>
      </td>
    </tr>
<?php endforeach; ?>

  </tbody>
</table>

<?php endif; ?>

</body>
</html>
