Mockingbird
===========

This is Mockingbird/"Казначей" ("Treasurer"), a personal finance web app,
written in PHP 5.3, Silex and Propel ORM.

Demo installation is available at: <http://mockingbird-demo.enotogorsk.ru/>.

It was originally written in 2012 as a personal project, both to solve an
actual task of personal finance management, and to be an exercise in
writing a mini web framework, named "Anthem" here.  Anthem is based on
Silex, adding on top of it:

* Simple module system
* Auth library (including OAuth via external services)
* Forms library
* App-wide settings
* Admin pages scaffolding
* Propel ORM integration
* Trivial implementation of other common subsystems like PHP-based templating,
  l10n, and console task runner
* Bootstrap-based layout
* A few sample content management modules (`AnthemCM` namespace)

The two things it really should have had (apart from 4-space indents, of course)
are composer-based dependency management/autoloading (as it is, it includes all
dependencies prepackaged in PHAR archives), and a test suite.  Apart from that
it's really fairly usable.  Writing Anthem also made me appreciate Symfony 2
much more.

The personal finance app itself resides in `Mockingbird` namespace.
It supports:

* Multiple user accounts (with login via Google etc.)
* Multiple currencies
* Multiple accounts for user
* Categories and tags for transactions
* Per-month budget planning via categories
* Some simple reports



Installation
------------

* Copy this codebase somewhere outside web server document root
* Use `Anthem/bootstrap.php` script to install Mockingbird into web dir:

  $ php ./Anthem/bootstrap.php /path/to/web/dir

* This will symlink static assets into web dir, and create four short front
  controller files and `.htaccess` for Apache.  If you are not using Apache,
  configure your web server similarly to handle all requests with `index.php`
  by default.
* Copy `localconfig.inc.default` into `localconfig.inc` and set database
  credentials and other configuration.
* Create database tables by loading `SQL/schema.sql` dump
* Create root user and base currency by executing:

  $ php /path/to/web/dir/console.php propel:load-fixtures Anthem/Auth Mockingbird

* Optionally, create demo account by loading `demo.sql` dump
* That's all!

If your server does not support https, you may want to change `config.inc` line:

  $config['Auth']['https'] = 'auth';

to:

  $config['Auth']['https'] = 'never';

Note that you do NOT need to do a `composer install`.  Composer defines
a dependency on Propel, but it is only needed to re-generate base ORM
classes and SQL definitions.  Mockingbird/Anthem uses a prepackaged
copy of Propel runtime otherwise.


Authors
-------

Mockingbird is written by Alexander Ulyanov <procyonar@gmail.com> and released
under MIT license.
