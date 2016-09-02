<?php
/**
	Application Front Controller
*/

namespace Any2Web;

use Edoceo\Radix;

require_once(dirname(dirname(__FILE__)) . '/boot.php');

Radix::init();
Radix::exec();
Radix::view();
Radix::send();

exit(0);
