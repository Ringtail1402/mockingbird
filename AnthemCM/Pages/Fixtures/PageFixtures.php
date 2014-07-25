<?php

namespace AnthemCM\Pages\Fixtures;

use Silex\Application;
use Anthem\Propel\Fixtures\FixtureInterface;
use AnthemCM\Pages\Model\Page;
use AnthemCM\Pages\Model\PagePeer;

/**
 * Fixtures for Page module.
 */
class PageFixtures implements FixtureInterface
{
  public function getPriority()
  {
    return 0;
  }

  public function load(Application $app, array &$references)
  {
    $model = $app['pages.model'];
    $model->truncate();

    $page1 = new Page();
    $page1->setTitle('About');
    $page1->setContent(<<<EOT
<p>This is <b>Anthem</b>, a simple and practical Content Management Framework.</p>
<p>Anthem is inspired by symfony 1.x and Symfony 2, but is a lot slimmer than either of them.
  Anthem also goes beyond being a simple web framework, providing building blocks for real-world web
  applications which can be used with little to no modification.  In essence, like any CMF it tries
  to strike a balance between a bare-bones web framework and a full-featured CMS which may not even
  require any coding in many common cases.</p>
<p>Anthem features:</p>
<ul>
  <li>Modern PHP 5.3-based MVC framework</li>
  <li>Very fast and conceptually simple (based on Silex micro-framework)</li>
  <li>All expected framework features, including controllers, console tasks, templating and helpers,
    ORM layer, fixtures, forms, validation, and service container</li>
  <li>ORM and database access provided by powerful Propel 1.6 library</li>
  <li>Split into a set of modules which can be independently enables/disabled as needed</li>
  <li>Full-featured admin interface.  Very fast (AJAX-based), very usable, and ready for production
    usage out of the box.</li>
  <li>Content management modules, including pages, news, blogs, etc.  Just enable modules as needed,
    roll your own templates, and you're ready to go.</li>
</ul>
<p>Anthem is based on several open source projects:</p>
<ul>
  <li><a href="http://silex.sensiolabs.org/"><strong>Silex</strong></a>, a micro web framework for PHP</li>
  <li><a href="http://www.propelorm.org/"><strong>Propel</strong></a>, an ORM (database access) library</li>
  <li><a href="http://twitter.github.com/bootstrap/"><strong>Twitter Bootstrap</strong></a>, a CSS framework</li>
  <li><a href="http://jquery.com/"><strong>jQuery</strong></a>, a Javascript framework</li>
</ul>
<p>Anthem was written by Alexander Ulyanov &lt;<a href="mailto:procyonar@gmail.com">procyonar@gmail.com</a>&gt;
  in early 2012.</p>
EOT
    );
    $page1->setIsActive(true);
    $page1->save();
    
    $page2 = new Page();
    $page2->setTitle('Foo');
    $page2->setContent('<p>This is a dummy page.</p>');
    $page2->setIsActive(true);
    $page2->save();

    $page3 = new Page();
    $page3->setTitle('Bar');
    $page3->setContent('<p>This is another dummy page.</p>');
    $page3->setIsActive(true);
    $page3->save();

    $page4 = new Page();
    $page4->setTitle('Baz');
    $page4->setUrl('bar/baz');
    $page4->setContent('<p>This is yet another dummy page.</p>');
    $page4->setIsActive(true);
    $page4->save();
  }
}
