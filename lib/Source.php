<?php
/**
	The Source Document for Processing
*/

namespace Any2Web;

use Edoceo\Radix\Filter;

class Source
{
	private $_path; // Path to Working Directory (from Job)
	private $_file;

	public $_name; // @todo fix this
	private $_extn;

	public $hash;

	function __construct($p)
	{
		$this->_path = $p;
		// if (!empty($_GET['source'])) {
		// 	// Fetch
        //
		// 	// Testing WKHTML
		// 	$cmd = '/var/www/edoceo.com/api.edoceo.com/wkhtmltopdf.static ';
		// 	$cmd.= escapeshellarg($_GET['source']) . ' ';
		// 	$cmd.= escapeshellarg(sprintf('%s/out.png',$_ENV['tmp']));
        //
		// 	// echo $cmd;
		// 	$out = shell_exec("$cmd 2>&1");
		// 	// exit(0);
        //
		// 	//  $this->_kind = 'png';
        //
		// 	// $this->_fetch_to_tmp();
		// 	// $inf = pathinfo(parse_url($_GET['s'],PHP_URL_PATH));
		// 	// print_r($inf);
		// } else {
        //
		// 	if (empty($_FILES['source']['name'])) {
		// 		// Bail
		// 	}
		// 	if ($_FILES['source']['error'] != 0) {
		// 		// Bail
		// 	}
		// 	if ($_FILES['source']['size'] == 0) {
		// 		// Bail
		// 	}
        //
		// 	// Success
		// 	// print_r($_FILES);
		// 	move_uploaded_file($_FILES['source']['tmp_name'],sprintf('%s/src.bin',$_ENV['tmp']));
		// }
	}

	public function getFile()
	{
		return $this->_file;
	}

	public function getName()
	{
		return $this->_name;
	}

	// @deprecated move to MIME
	public function mime()
	{
		if (empty($this->_mime)) {
			$this->_mime = MIME::fromFile($this->_file);
		}

		return $this->_mime;
	}

	/**
		Read the Input from GET, POST or PUT
		@todo Split to Routines
	*/
	function read()
	{
		$source = null;
		$source_name = null;
		$source_type = null;

		switch ($_SERVER['REQUEST_METHOD']) {
		case 'GET':

			$source = $_GET['source'];
			$source_name = $_GET['source_name'];
			$source_type = 'link';

			break;

		case 'POST':

			// Posted Link
			if (!empty($_POST['source'])) {

				$source = $_POST['source'];
				$source_type = 'link';

			} elseif (!empty($_FILES['source'])) {

				if ($_FILES['source']['error'] != 0) {
					die("Error: {$_FILES['source']['error']}");
				}

				if ($_FILES['source']['size'] == 0) {
					die("Error: Bad Size");
				}

				if (empty($_FILES['source']['name'])) {
					die("Error: Bad name");
				}

				// Save
				$source = $_FILES['source']['tmp_name'];
				$source_name = basename($_FILES['source']['name']);
				$source_type = 'file';

			}

			if (!empty($_POST['source_name'])) {
				$source_name = $_POST['source_name'];
			}

			break;

		case 'PUT':
			$this->_name = basename($_GET['source']);
			$source = $this->_read_put();
			break;
		}

		// Validate Sources
		switch ($source_type) {
		case 'file':
			// OK
			break;
		case 'link':

			$source = Filter::uri($source);

			if (empty($source)) {
				die("Invalid Source");
			}

			break;
		}

		// Eval Source Name
		switch ($source_type) {
		case 'file':
			// It's OK
			break;
		case 'link':
			if (empty($source_name)) {
				$source_name = parse_url($source, PHP_URL_PATH);
			}
			$source_name = basename($source_name);

			$tmpfile = sprintf('%s/source.tmp', $this->_path);
			$this->_fetch($source, $tmpfile);
			$source = $tmpfile;
		}

		$this->_name = rawurlencode($source_name);
		$this->_name = preg_replace('/\.\w{2,6}$/', null, $this->_name);

		$this->_extn = preg_match('/\.(\w{2,6})$/', $source_name, $m) ? $m[1] : null;
		$this->_extn = strtolower($this->_extn);

		$this->_mime = MIME::fromFile($source);
		if (empty($this->_extn)) {
			$this->_extn = MIME::fileExtension($this->_mime);
		}

		$this->_file = sprintf('%s/%s.%s', $this->_path, $this->_name, $this->_extn);

		switch ($source_type) {
		case 'file':
			move_uploaded_file($source, $this->_file);
			break;
		case 'link':
			rename($source, $this->_file);
			break;
		}

		$data = print_r($this, true);
		$file = sprintf('%s/source.obj', $this->_path);
		file_put_contents($file, $data);

	}

	/**
	*/
	private function _read_put_to_file()
	{
		$tf = sprintf('%s/source.tmp', $this->_path);

		// Tmp File First
		$ih = fopen('php://input', 'r');
		$oh = fopen($tf, 'w');
		while ($x = fread($ih, 4096)) {
			fwrite($oh, $x);
		}

		fclose($ih);
		fclose($oh);

		return $tf;
	}

	/**
		@param $url Source URL
		@param $out Output File
	*/
	private function _fetch($url, $out)
	{
		// Filter Link for Baddies
//		if (empty($chk['host'])) {
//			throw new \Exception("S#245: Invalid Host");
//		}
//
//		// @see http://en.wikipedia.org/wiki/Reserved_IP_addresses
//		$dns = dns_get_record($chk['host'], DNS_A);
//		foreach ($dns as $i => $rec) {
//			// Bad Network
//			// 0.*, 10.*, 127.*, 169.254.* (private), 172.* (should be 16-31), 192.168.* and the high-order reserved: 240-255
//			if (preg_match('/^(0\.|10|127|169\.254|172|192\.168|2[45][0-9])/', $rec['ip'])) {
//				throw new \Exception("S#254: Invalid Host");
//			}
//			// Bad Host
//			if (preg_match('/(0|255)$/', $rec['ip'])) {
//				throw new \Exception("S#258: Invalid Host: {$chk['host']} / {$rec['ip']}");
//			}
//			// IPv6: ^fec0::/10 and ^fc00::/7
//		}

		$ch = curl_init($url);
		// Booleans
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIESESSION, true);
		curl_setopt($ch, CURLOPT_CRLF, false);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_FILETIME, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, false);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_NETRC, false);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		//if ( (!empty(self::$_opts['verbose'])) && (is_resource(self::$_opts['verbose'])) ) {
		//	curl_setopt(self::$_ch, CURLOPT_VERBOSE, true);
		//	curl_setopt(self::$_ch, CURLOPT_STDERR, self::$_opts['verbose']);
		//}
		//curl_setopt($ch, CURLOPT_BUFFERSIZE, 16384);
		//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
		//curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		// curl_setopt($ch, CURLOPT_MAXREDIRS, 0);
		// curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, 'edoceo/any2web toolkit/2016.25');
		// if ( (!empty(self::$_opts['head'])) ) {
		//	 curl_setopt(self::$_ch, CURLOPT_HTTPHEADER, self::$_opts['head']);
		// }
		// if (!empty(self::$_opts['cookie'])) {
		//	 curl_setopt(self::$_ch, CURLOPT_COOKIEFILE, self::$_opts['cookie']);
		//	 curl_setopt(self::$_ch, CURLOPT_COOKIEJAR, self::$_opts['cookie']);
		// }
		// curl_setopt(self::$_ch, CURLOPT_HEADERFUNCTION, array('self','_curl_head'));

		if (empty($out)) {
			$out = tempnam('/tmp', 'api');
		}
		$out_dir = dirname($out);
		if (!is_dir($out_dir)) {
			mkdir($out_dir, 0755, true);
		}

		$out_fh = fopen($out, 'w');

		curl_setopt($ch, CURLOPT_FILE, $out_fh);

		$res = curl_exec($ch);
		$inf = curl_getinfo($ch);

		if (200 != $inf['http_code']) {
			throw new \Exception("S#307: Invalid Document; Error: {$inf['http_code']}");
		}

		if (0 == filesize($out)) {
			throw new \Exception("S#310: Invalid Document; Error: {$inf['http_code']}");
		}

		curl_close($ch);

		fclose($out_fh);

	}
}

