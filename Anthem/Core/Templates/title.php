<?php if ($view->isSlotSet('title') && $view->getSlot('title') != $app['Core']['project']) echo $view->getSlot('title') . ' &mdash; ' ?>
<?php echo $app['Core']['project'] ?>