<?php
/**
	A Job
	Tracks a Source and Outputs
*/

namespace Any2Web;

class Job
{
	private $_hash;
	private $_path;

	private $_Source;
	private $_Output;

	/**
		@param $jid Job ID
	*/
	public function __construct($jid=null)
	{

		// Use supplied hash or use magic to make one from request details
		if (!empty($jid)) {

			$this->_hash = $jid;

		} else {

			switch ($_SERVER['REQUEST_METHOD']) {
			case 'GET':
				ksort($_GET);
				$this->_hash = sha1(json_encode($_GET));
				break;
			case 'POST':
				ksort($_POST);
				$this->_hash = sha1(json_encode($_POST));
				break;
			}

		}

		$this->_path = sprintf('%s/var/job/%s', APP_ROOT, $this->_hash);

		// Make Working directory
		if (!is_dir($this->_path)) {
			mkdir($this->_path, 0755, true);

			// Save first details
			file_put_contents($this->_path . '/_GET.dump', print_r($_GET, true));
			file_put_contents($this->_path . '/_POST.dump', print_r($_POST, true));
			file_put_contents($this->_path . '/_FILES.dump', print_r($_FILES, true));
			file_put_contents($this->_path . '/job.obj', print_r($this, true));
		}
	}

	/**
		Simple Getter for Path
	*/
	function getPath()
	{
		return $this->_path;
	}

	/**
		Load the Source Objects
	*/
	public function readSource()
	{
		$S = new Source($this->_path);
		return $S;
	}

	/**
		@param $S Source Object
		@return Output Object
	*/
	public function makeOutput($S)
	{
		$O = new Output($this->_path, $S->getName());
		return $O;
	}

}
