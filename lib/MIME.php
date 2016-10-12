<?php
/**
	Tools for working with MIME

	@see http://www.sitepoint.com/web-foundations/mime-types-complete-list/
	http://stackoverflow.com/questions/4212861/what-is-a-correct-mime-type-for-docx-pptx-etc
	http://en.wikipedia.org/wiki/Image_file_formats
*/

namespace Any2Web;

class MIME
{
	private static $_extn_to_mime = array(
		'bmp'  => 'image/bmp',
		'html' => 'text/html',
		'pbm'  => 'image/x-portable-bitmap',
		'pdf'  => 'application/pdf',
		'png'  => 'image/png',
		'ppt'  => 'application/vnd.ms-powerpoint',
		'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'tiff' => 'image/tiff',
		'txt'  => 'text/plain',
		'webp' => 'image/webp',
	);

	/**
		Returns File Extension based on MimeType
		@param $mime a Mime Type
		@return File Extension
	*/
	static function fileExtension($mime)
	{
		$r = array_search($mime, self::$_extn_to_mime);

        if (empty($r)) {
        	$r = 'bin';
        }

		return $r;

	}

	/**
		@param $e File Extension
		@return mime type string
	*/
	static function fromExtension($e)
	{
		$r = self::$_extn_to_mime[$e];

		if (empty($r)) {
			$r = 'application/octet-stream';
		}

		return $r;

	}

	/**
		Read Mime Type from File
		@param $f File Path
		@return MIME type
	*/
	static function fromFile($f)
	{
		$cmd = array();
		$cmd[] = '/usr/bin/file';
		$cmd[] = '-bi';
		$cmd[] = escapeshellarg($f);
		$cmd[] = '2>&1';
		$cmd = implode(' ', $cmd);

		$buf = exec($cmd);

		$mime = strtok($buf,';');
		$mime = strtolower($mime);

		// For zip try magic?
		switch ($mime) {
		case 'application/zip':
			$x = self::_eval_archive_zip($f);
			if (!empty($x)) {
				$mime = $x;
			}
			break;
        }

		//	$map = array();
		//	$buf = file_get_contents('/etc/mime.types');
		//	if (preg_match_all('/^(\S+)\s+([\w\s]+)$/ms', $buf, $m)) {
		//		$c = count($m[0]);
		//		for ($i=0;$i<$c;$i++) {
		//			$ext_list = explode(' ', $m[2][$i]);
		//			foreach ($ext_list as $ext) {
		//				$map[$ext] = $m[1][$i];
		//			}
		//		}
		//	}
        //
		//	$ext = preg_match('/\.(\w+)$/', $S->_name, $m) ? $m[1] : null;
		//	if (!empty($map[$ext])) {
		//		$this->_mime = $map[$ext];
		//	}
        //
		//	break;
		//}

		return $mime;

	}

	/**
		Guess the mime type of the Zip file
		Sometimes the Microsoft Formats read as Zips, so we look for specific paths
		@return null or discovered mime-type
	*/
	private static function _eval_archive_zip($f)
	{
		$za = new \ZipArchive();
		if ($za->open($f)) {
			$c = $za->numFiles;
			if ($c > 0) {
				for ($i=0; $i<$c; $i++) {
					$n = $za->getNameIndex($i);
					$n = strtok($n, '/');
					switch ($n) {
					case 'ppt':
						return 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
					case 'word':
						return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
					case 'xl':
						return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
					}
				}
			}
		}
	}
}
