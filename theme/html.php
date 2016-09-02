<?php
/**

*/

header('Content-Type: text/html; charset=utf-8;');
header('Cache-Control: public, max-age=300');

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="content-type" content="text/html; charset=utf-8;">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=$_ENV['title']?></title>
<link href="//gcdn.org/pure/0.5.0/pure.css" rel="stylesheet" type="text/css">
<link href="/css/base.css" rel="stylesheet" type="text/css"></head>
<body>
<header>
<nav>
<ul>
<li><a href="/"><?=APP_NAME?></a></li>
<li><a href="/convert">Convert</a></li>
<li><a href="/convert/pdf">PDF</a></li>
<li><a href="/convert/png">PNG</a></li>
<li><a href="/convert/svg">SVG</a></li>

<li><a href="/formats#doc" title="Convert Microsoft Word (.doc, .docx), OpenOffice/LibreOffice (.odt) to PDF or PNG">Documents</a></li>
<li><a href="/formats#xls" title="Convert Microsoft Excel (.xls, .xlsx), OpenOffice/LibreOffice (.ods) to PDF or PNG">Spreadsheets</a></li>
<li><a href="/formats#ppt" title="Convert Microsoft PowerPoint (.ppt, .pptx), OpenOffice/LibreOffice (.odp) to PDF or PNG">Presentations</a></li>
<!--
<li><a href="/formats#odt">odt</a></li>
<li><a href="/formats#xls">xls</a></li>
<li><a href="/formats#ods">ods</a></li>
<li><a href="/formats#ppt">ppt</a></li>
<li><a href="/formats#odp">odp</a></li>
<li><a href="/formats#html">html</a></li>
<li><a href="/formats#txt">txt</a></li>
<li><a href="/formats#uri">uri</a></li>
-->
<li><a href="/api">API</a></li>
</ul>
</nav><div style="clear:left;"></div>
</header>

<div style="margin: 0; padding:0;">
<?php
echo $this->body;
?>
</div>

<footer>
Hub designed by <a href="http://www.thenounproject.com/Gabriele Fumero">Gabriele Fumero</a> from the <a href="http://www.thenounproject.com">Noun Project</a>
</footer>

</body>
</html>