<h1>
  <img src="<?php echo $asset->image('Mockingbird:mockingbird-b48.png', true) ?>" valign="middle">
  <?php echo $app['Core']['project'] ?>
</h1>

<?php echo $view->getSlot('content') ?>

<address>
  Система учета личных финансов <b><a href="<?php echo $app['request']->getUriForPath('/') ?>"><?php echo $app['Core']['project'] ?></a></b>
</address>