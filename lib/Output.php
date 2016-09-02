<?php
/**
	An Output Container
*/

namespace Any2Web;

class Output
{
	private $_path;

	public $mime;
	public $name;
	public $path;

    function __construct($p, $n)
	{
		$this->_path = $p;

		switch ($_SERVER['REQUEST_METHOD']) {
		case 'GET':
		case 'PUT':

			$this->mime = $_GET['output_mime'];
			$this->name = $_GET['output_name'];

			break;

		case 'POST':

			$this->mime = $_POST['output_mime'];
			$this->name = $_POST['output_name'];

		}

		$this->_mime_filter();

		// Check Name
		if (empty($this->name)) {
			$this->name = $S->_name;
		}

		$this->name	= preg_replace('/\.\w+$/', null, $this->name);

		// Mime
		if (empty($this->mime)) {
			$this->mime = MIME::fromExtension($this->name);
		}

		// Output Extension?
		$this->extn = Mime::fileExtension($this->mime);

    }

    /**
    	Sanatize the the MIME format
    	@return void
    */
    private function _mime_filter()
    {
    	// Promote these shorthand names
		switch ($this->mime) {
		case 'gif':
			$this->mime = 'image/gif';
			break;
		case 'jpeg':
		case 'jpg':
			$this->mime = 'image/jpeg';
			break;
		case 'pdf':
			$this->mime = 'application/pdf';
			break;
		case 'png':
			$this->mime = 'image/png';
			break;
		}

		// Set Default
		if (empty($this->mime)) {
			$this->mime = 'image/png';
		}

    }
}
