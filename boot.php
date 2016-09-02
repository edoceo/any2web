<?php
/**
	Application Bootstrap
*/

namespace Any2Web;

use Edoceo\Radix\DB\SQL;

define('APP_NAME', 'any2web.i/o');
define('APP_SITE', 'http://any2web.io');
define('APP_ROOT', dirname(__FILE__));
define('APP_SALT', sha1(APP_ROOT . APP_SITE . APP_NAME));

error_reporting((E_ALL|E_STRICT) ^ E_NOTICE);

require_once(APP_ROOT . '/lib/Auth.php');
require_once(APP_ROOT . '/lib/MIME.php');
require_once(APP_ROOT . '/lib/Job.php');
require_once(APP_ROOT . '/lib/Source.php');
require_once(APP_ROOT . '/lib/Output.php');

require_once(APP_ROOT . '/vendor/autoload.php');

SQL::init(sprintf('sqlite:%s/etc/a2w.sdb', APP_ROOT));