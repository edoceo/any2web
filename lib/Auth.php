<?php
/**

*/

namespace Any2Web;

use Edoceo\Radix\DB\SQL;

class Auth
{
	static function check()
	{

		$auth = preg_match('/^Token (.+)$/', $_SERVER['HTTP_AUTHORIZATION'], $m) ? $m[1] : null;
		if (empty($auth)) {
			print_r($_SERVER);
			die("Not Authorized, No Token");
		}

		// Lookup
		$sql = 'SELECT * FROM auth WHERE hash = ? LIMIT 1';
		$arg = array($m[1]);
		$res = SQL::fetch_one($sql, $arg);
		if (empty($res)) {
			die("Not Authorized, Invalid Token");
		}

	}

	/**

	*/
	static function saveUrl($url)
	{




	}

}
