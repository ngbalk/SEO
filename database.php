<?php
/*
Connect to the database
*/
$username = "root";
$password = "root";
$hostname = "localhost";
$database = "seo";

$dbhandle = mysql_connect($hostname, $username, $password)
	or die("Unable to connect to MySQL host");
echo "Connected to MySQL host<br>";

$selected = mysql_select_db($database, $dbhandle)
	or die("Could not select db");
echo "Selected db succesfully";

?>