
<html>
<body>
<form action = "domain_handler.php" method = "post">
	Enter the url of the domain that you would like ot scrape:  <input type = "text" name = "query"><br>
	<input type = "submit" value="Scrape!">
</form>

</body>
<?php
			$type_array = array(
				IMAGETYPE_PNG => "png",
				IMAGETYPE_JPEG => "jpeg",
				IMAGETYPE_GIF => "gif",
				) ;
$my = exif_imagetype('http://www.gold-happ.com/CMS.aspx?mode=getfile&fid=238');
 echo IMAGETYPE_PNG;
 echo $type_array[$my];
?>