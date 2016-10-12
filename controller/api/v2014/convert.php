<?php
/**
	Steps are Basically:
	Convert to PDF
	Convert to PNG - In Archive Zip
*/

namespace Any2Web;

header('Content-Type: text/plain');

Auth::check();

_inflate_input();

$J = new Job();
$S = $J->readSource();

try {
	$S->read();
	$S->mime();
} catch (\Exception $e) {
	_bail('400', 'E#022: Invalid Source Document Provided: ' . $e->getMessage());
}

$O = $J->makeOutput($S);
file_put_contents(APP_ROOT . '/var/output.obj', print_r($O, true));

switch ($S->mime()) {
case 'application/pdf':

	switch ($O->mime) {
	case 'image/png':
	default:
		$O = _convert_pdf2png($J, $S, $O);
		break;
	}

 	_send_output($O);

	break;

case 'application/vnd.oasis.opendocument.text': // odt

	//$pdf_file = sprintf('%s/%s.pdf', $J->getPath(), $S->getName());
	//Convert::odt2pdf($S->getFile(), $pdf_file);

	// to PDF
	// to PNGs
	// to ZIP

	// $cmd = sprintf('odt2pdf.sh %s %s', APP_ROOT, escapeshellarg($S->_path), escapeshellarg($out));
	// $log = sprintf('%s/var/odt2pdf.log', APP_ROOT);
	$log = sprintf('%s/odt2pdf.log', $J->getPath());
	_cmd_log($cmd, $log);

	switch ($O->mime) {
	case 'application/pdf':
		// _send_output($O);
		break;
	case 'image/png':

		// _pdf2png($S, $O);
		// _png2zip($S, $O);

		break;
	}

	// $buf = shell_exec("$cmd >/var/www/any2web/var/odt2pdf.log 2>&1");
	// die("cmd:$cmd\nbuf:$buf");

	if (!is_file($out)) {
		die("cmd:$cmd\nbuf:$buf");
	}

	_send_file($out);

	// _unlink($S->_path);
	// _unlink($out);

	break;

case 'application/vnd.ms-powerpoint': // ppt
case 'application/vnd.oasis.opendocument.presentation': // odp
case 'application/vnd.openxmlformats-officedocument.presentationml.presentation': // pptx

	$O->file = sprintf('%s/%s.pdf', $J->getPath(), $S->getName());

	$cmd = sprintf('ppt2pdf.sh %s %s', escapeshellarg($S->getFile()), escapeshellarg($O->file));
	$log = sprintf('%s/ppt2pdf.log', $J->getPath());
	_cmd_log($cmd, $log);

	switch ($O->mime) {
	case 'application/pdf':
		_send_output($O);
		break;
	case 'image/png':

		// Convert to a ZIP file with PNGs
		$Z = new \stdClass();
		$Z->file = preg_replace('/\.pdf$/', '.png', $O->file);

		$cmd = array();
		$cmd[] = 'pdf2png.sh';
		$cmd[] = escapeshellarg($O->file);
		$cmd[] = escapeshellarg($Z->file);
		$log = sprintf('%s/pdf2png.log', $J->getPath());
		_cmd_log($cmd, $log);

		$O->file = preg_replace('/\.pdf$/', '.zip', $O->file);
		$O->mime = 'application/zip';

		if (is_file($O->file)) {
			_send_output($O);
		}
	}

	break;

case 'application/vnd.oasis.opendocument.spreadsheet': // ods
case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': // xlsx

	$old = $S->_path;
	$new = sprintf('/tmp/any2web/%s', $S->_name);
	move_uploaded_file($old, $new);
	$S->_path = $new;
	$pdf_file = sprintf('/tmp/any2web/%s', preg_replace('/\.xlsx$/i', '.pdf', $S->_name));

	$cmd = sprintf('%s/bin/xls2pdf.sh %s %s', APP_ROOT, escapeshellarg($S->_path), escapeshellarg($out));
	$log = sprintf('%s/var/xls2pdf.log', APP_ROOT);
	_cmd_log($cmd, $log);

	if (!is_file($pdf_file)) {
		echo "cmd:$cmd\n";
		echo "buf:$buf\n";
		die("Failed to Process: " . basename($S->_name));
	}

	_send_file($pdf_file);

	break;

case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document': // docx

	$src_file = $S->getFile();
	$pdf_file = sprintf('%s/%s.pdf', $J->getPath(), $S->getName());

	$cmd = sprintf('doc2pdf.sh %s %s', escapeshellarg($src_file), escapeshellarg($pdf_file));
	$log = sprintf('%s/doc2pdf.log', $J->getPath());
	_cmd_log($cmd, $log);

	if (!is_file($pdf_file)) {
		throw new Exception('Failed to create PDF Format');
	}

	// _send_file($o);
	switch ($O->mime) {
	case 'application/pdf':
		$O->file = $pdf_file;
		_send_output($O);
		break;
	case 'image/png':

		$png_file = sprintf('%s/%s.png', $J->getPath(), $S->getName());

		$cmd = array();
		$cmd[] = 'pdf2png.sh';
		$cmd[] = escapeshellarg($pdf_file);
		$cmd[] = escapeshellarg($png_file);

		//switch ($O->mime) {
		//case 'image/png': // Tall Image
		//case 'image/png+tall': // Tall Image
		//	$cmd[] = escapeshellarg($png_file);
		//	$cmd[] = 'png-v';
		//	break;
		//case 'image/png+wide': // Wide Image
		//	$cmd[] = escapeshellarg($png_file);
		//	$cmd[] = 'png-h';
		//	break;
		//case 'application/zip':
		//	$cmd[] = escapeshellarg($zip_file);
		//	$cmd[] = 'zip';
		//	break;
		//default:
		//	throw new \Exception("Invalid '{$O->pack}' Pack");
		//}

		$log = sprintf('%s/pdf2png.log', $J->getPath());
		_cmd_log($cmd, $log);

		$O->file = preg_replace('/\.pdf$/', '.zip', $pdf_file);
		$O->mime = 'application/zip';

		if (is_file($O->file)) {
			_send_output($O);
		}

	}

	break;

case 'image/bmp':
case 'image/x-ms-bmp': // alias
case 'image/x-windows-bmp': // alias
case 'image/gif':
case 'image/jpeg':
case 'image/png':
case 'image/tiff':
case 'image/x-icon':

	$src_file = $S->getFile();
	$out_file = preg_replace('/\.\w{3,4}$/', '.img', $src_file);

	switch ($O->mime) {
	case 'image/gif':
		// Convert to PNGs
		$out_file = preg_replace('/\.\w{3,4}$/', '.gif', $src_file);
		$cmd = sprintf('img2xxx.sh %s %s gif', escapeshellarg($src_file), escapeshellarg($out_file));
		$log = sprintf('%s/img2gif.log', $J->getPath());
		_cmd_log($cmd, $log);
		break;
	case 'image/jpeg':
		// Convert to PNGs
		$out_file = preg_replace('/\.\w{3,4}$/', '.jpeg', $src_file);
		$cmd = sprintf('img2xxx.sh %s %s jpeg', escapeshellarg($src_file), escapeshellarg($out_file));
		$log = sprintf('%s/img2jpeg.log', $J->getPath());
		_cmd_log($cmd, $log);
		break;
	case 'image/png':
		$out_file = preg_replace('/\.\w{3,4}$/', '.png', $src_file);
		$cmd = sprintf('img2xxx.sh %s %s png', escapeshellarg($src_file), escapeshellarg($out_file));
		$log = sprintf('%s/img2png.log', $J->getPath());
		_cmd_log($cmd, $log);
		break;
	}

	if (!is_file($out_file)) {
		_bail(500, 'C#161: Failed to Convert');
	}

	$O->file = $out_file;
	_send_output($O);

	break;

case 'text/html':
case 'text/plain':
case 'text/x-shellscript':

	$src_file = $S->getFile();
	$pdf_file = preg_replace('/\.\w{3,4}$/', '.pdf', $src_file);
	$png_file = preg_replace('/\.\w{3,4}$/', '.png', $src_file);

	// wkhtml
	// $cmd = sprintf('%s/bin/uri2pdf-wkhtml.sh %s %s', APP_ROOT, escapeshellarg($S->_link), escapeshellarg($out));

	// phantomjs
	$cmd = sprintf('uri2pdf-phantom.sh %s %s', escapeshellarg($src_file), escapeshellarg($png_file));
	$log = sprintf('%s/uri2pdf.log', $J->getPath());
	_cmd_log($cmd, $log);
	$O->file = $png_file;

	//$cmd = array();
	//$cmd[] = 'pdf2png.sh';
	//$cmd[] = escapeshellarg($pdf_file);
	//$cmd[] = escapeshellarg($png_file);
    //
	//$log = sprintf('%s/pdf2png.log', $J->getPath());
	//_cmd_log($cmd, $log);
    //
	//$O->file = preg_replace('/\.pdf$/', '.zip', $pdf_file);
	//$O->mime = 'application/zip';
    //
	//if (is_file($O->file)) {
	//	_send_output($O);
	//}

	_send_output($O);

	break;

default:

	_bail(500, 'E#215: MIME type not handled: ' . $S->mime());

}

exit(0);

function _bail($code, $text)
{
	while (ob_get_level() > 0) {
		ob_end_clean();
	}

	header(sprintf('%s %d %s', $_SERVER['SERVER_PROTOCOL'], $code, $text));
	header(sprintf('Content-Type: application/json'));

	die(json_encode(array(
		'code' => $code,
		'text' => $text,
		'request' => array(
			'GET' => $_GET,
			'POST' => $_POST,
			'FILE' => $_FILES,
		)
	)));
}


function _cmd_log($cmd, $log)
{

	if (is_array($cmd)) {
		$cmd = implode(' ', $cmd);
	}

	$cmd = sprintf('%s/bin/%s', APP_ROOT, $cmd);
	$cmd = "$cmd >$log 2>&1";

	$buf = shell_exec($cmd);

}

/**
	Simply Filters the Input Fields to inflate short-names to canonical names
*/
function _inflate_input()
{
	// Short => Canonical
	$map_list = array(
		'o' => 'output',
		'om' => 'output_mime',
		'on' => 'output_name',
		's' => 'source',
		'sn' => 'source_name',
	);

	// Map
	switch ($_SERVER['REQUEST_METHOD']) {
	case 'GET':
	case 'PUT':
		foreach ($map_list as $a => $b) {
			if (empty($_GET[$b]) && !empty($_GET[$a])) {
				$_GET[$b] = $_GET[$a];
				unset($_GET[$a]);
			}
		}
		break;
	case 'POST':
		foreach ($map_list as $a => $b) {
			if (empty($_POST[$b]) && !empty($_POST[$a])) {
				$_POST[$b] = $_POST[$a];
				unset($_POST[$a]);
			}
		}
	}
}


function _send_file($pdf_file)
{
	$mime = '';

	$format = preg_match('/^(pdf|png|svg)$/', $_GET['format'], $m) ? $m[1] : 'pdf';
	$format = preg_match('/^(pdf|png|svg)$/', $_POST['format'], $m) ? $m[1] : $format;

	$out_png = preg_replace('/\.(\w+)$/', '.png', $pdf_file);
	$out_svg = preg_replace('/\.(\w+)$/', '.svg', $pdf_file);
	$out_zip = preg_replace('/\.(\w+)$/', '.zip', $pdf_file);
	// $out_zip = str_replace('.pdf', '.zip', $pdf_file);

	file_put_contents(APP_ROOT . '/var/output-_send_file.obj', print_r(array(
		'post' => $_POST,
		'format' => $format,
		'pdf_file' => $pdf_file,
		'out_png' => $out_png,
		'out_svg' => $out_svg,
		'out_zip' => $out_zip,
	), true));

	switch ($format) {
	case 'png':

		$cmd = array();
		$cmd[] = 'pdf2png.sh';
		$cmd[] = escapeshellarg($pdf_file);
		$cmd[] = escapeshellarg($out_png);
		// $cmd = sprintf('%s/bin/pdf2png.sh %s %s', APP_ROOT, escapeshellarg($pdf_file), escapeshellarg($out_png));
		$log = sprintf('%s/var/pdf2png.log', APP_ROOT);
		_cmd_log($cmd, $log);
		if (!is_file($out_zip)) {
			// die("cmd:$cmd<br>buf:$buf");
			die("Failed to Compact");
		}
		if (filesize($out_zip) <= 256) {
			die("Failed to Compact");
		}
		$out = $out_zip;
		$mime = 'application/zip; charset=binary';
		break;

	case 'svg':

		$cmd = sprintf('%s/bin/pdf2svg.sh %s %s', APP_ROOT, escapeshellarg($pdf_file), escapeshellarg($out_svg));
		$log = sprintf('%s/var/pdf2svg.log', APP_ROOT);

		$buf = shell_exec("$cmd >$log 2>&1");
		if (!is_file($out_zip)) {
			echo "cmd:$cmd\n";
			echo "buf:$buf\n";
			die("Failed to Process: " . basename($pdf_file));
		}
		$out = $out_zip;
		break;
	case 'pdf':
	default:
		$out = $pdf_file;
	}

	if (empty($out)) {
		die("Big Problem");
	}

	header(sprintf('Content-Disposition: attachment; filename="%s"', basename($out)));
	header(sprintf('Content-Length: %s', filesize($out)));
	header(sprintf('Content-Type: %s', $mime));

	readfile($out);

	if (!empty($out_png) && is_file($out_png)) unlink($out_png);
	if (!empty($out_svg) && is_file($out_svg)) unlink($out_svg);
	if (!empty($out_zip) && is_file($out_zip)) unlink($out_zip);
	if (!empty($out) && is_file($out)) unlink($out);

}

function _send_output($O)
{
	file_put_contents(APP_ROOT . '/var/output-_send_output.obj', print_r($O, true));

	if (!is_file($O->file)) {
		throw new \Exception('E#354: Conversion Failed');
		// _bail('500', 'E#354: Conversion Failed');
	}
	if (0 == filesize($O->file)) {
		throw new \Exception('E#357: Conversion Failed');
		//_bail('500', 'E#357: Conversion Failed');
	}

	while (ob_get_level() > 0) {
		ob_end_clean();
	}

	header(sprintf('Content-Disposition: inline; filename="%s.%s"', $O->name, $O->extn));
	header(sprintf('Content-Length: %d', filesize($O->file)));
	header(sprintf('Content-Type: %s', $O->mime));

	// @todo Check for SendFIle Module
	readfile($O->file);

}


function _convert_pdf2png($J, $S, $O)
{
	$O->file = sprintf('%s/%s.png', $J->getPath(), $S->getName());
	//= sprintf('%s/%s.png', $J->getPath(), $S->getName());

	$cmd = sprintf('pdf2png.sh %s %s', escapeshellarg($S->getFile()), escapeshellarg($O->file));
	$log = sprintf('%s/pdf2png.log', $J->getPath());
	_cmd_log($cmd, $log);

	// = APP_ROOT . '/var/' . $O->hash . '/' . preg_replace('/\.\w+$/', '.zip', $S->_name);
	$O->file = preg_replace('/\.png$/', '.zip', $O->file);
	$O->extn = 'zip';
	$O->mime = 'application/zip; charset=binary';

	return $O;
}

function _unlink($f)
{
	if (is_file($f)) {
		unlink($f);
	}
}