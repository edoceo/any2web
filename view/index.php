<?php

$_ENV['title'] = 'Convert Anything to Web Ready Formats';

$max = array();
$max[] = ini_get('post_max_size');
$max[] = ini_get('upload_max_filesize');

$min = min($max);


?>

<div style="background:#bbb; text-align:center;">
<h1 style="padding:1em;"><?=$_ENV['title']?></h1>
</div>

<div style="padding: 1em 2em;">
<h2 style="text-align:center;">Convert from (almost) Anything!</h2>
<div style="display:flex; flex-wrap:wrap;">
<div style="flex: 1 0 33%; padding:1em;"><h3>Word Processing</h3><p>Microsoft Word 2010, 2013, LibreOffice Writer, OpenOffice Writer</p></div>
<div style="flex: 1 0 33%; padding:1em;"><h3>Presentations</h3><p>Microsoft Powerpoint 2010, 2013, LibreOffice Impress</p></div>
<div style="flex: 1 0 33%; padding:1em;"><h3>Spreadsheets</h3><p>Microsoft Excel 2010, 2013, LibreOffice Calc</p></div>
<div style="flex: 1 0 33%; padding:1em;"><h3>Images</h3><p>Bitmap, GIF, JPEG, PNG, WEBP to GIF, JPEG, PNG or WEBP formats</p></div>
<div style="flex: 1 0 33%; padding:1em;"><h3>PDF Documents</h3><p>To Image Formats</p></div>
<div style="flex: 1 0 33%; padding:1em;"><h3>HTML5</h3><p>Web-Pages direct to PDF or Image formats</p></div>
</div>
</div>

<div style="background:#ddd; border-top: 4px solid #333; padding: 1em 2em;">
<h2 style="text-align:center;">Conversion Output Formats</h2>
<div style="display:flex; flex-wrap:wrap">
<div style="flex: 1 1 auto; padding:1em;"><a href="/convert/html">HTML</a></div>
<div style="flex: 1 1 auto; padding:1em;"><a href="/convert/pdf">PDFs</a></div>
<div style="flex: 1 1 auto; padding:1em;"><a href="/convert/png">PNGs</a></div>
<div style="flex: 1 1 auto; padding:1em;"><a href="/convert/svg">SVGs</a></div>
</div>
</div>

<section style="text-align:center;">
<?php
if (!empty($_POST['email'])) {
	mail('busby@edoceo.com', '[and2web.io] Notify', print_r($_POST, true));
	echo '<p style="font-size:24px;">Thanks!</p>';
} else {
?>
	<form method="post">
	<p style="font-size:24px;">If you give me your email I'll announce to you when the service is fully online</p>
	<input name="email" type="email">
	<button>Notify Me!</button>
	<p>You can also follow <a href="https://twitter.com/any2web">@any2web</a></p>
	</form>
<?php
}
?>
</section>

<div style="background:#ddd; border-top: 4px solid #333; padding: 1em 2em;">
<div style="margin: 0 auto; min-width: 480px; text-align:center; width: 60%;">
	<h2 style="">Try It!</h2>
	<form action="/convert" enctype="multipart/form-data" method="post">
		<!-- <input type="text" name="source"> or --> 
		<div style="padding:0.5em;">
		<input type="file" name="source"> <?= $min ?>
		</div>
		<div style="padding:0.5em;">
			<button name="a" type="submit" value="pdf">Convert to PDF</button>
			<button name="a" type="submit" value="png">Convert to PNG</button>
			<!-- <button name="a" type="submit" value="jpeg">Convert to JPEG</button> -->
			<button name="a" type="submit" value="svg">Convert to SVG</button>
		</div>
	</form>

</section>
</div>

<!--
<p>If you need to convert media files you can checkout <a href="http://ffmpeg.io/">ffmpeg.io</a></p>
-->
