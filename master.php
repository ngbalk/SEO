<?php
require_once "information.php";
$myMaster = new Master($hostname, $database, $username, $password);
$myMaster->add_job($sourcefile);

Class Master{

	function __construct($hostname, $database, $username, $password){
		$dbhandle = mysql_connect($hostname, $username, $password)
				or die("Unable to connect to MySQL host");
		echo "connected to host";
		
		$selected = mysql_select_db($database, $dbhandle)
				or die("Could not select db");
		echo "selected database";


	}
	function add_job($sourcefile){
		$handle = fopen($sourcefile, "r");
		while($row = fgetcsv($handle)) {
			foreach ($row as $url) {
				if(substr($url, 0, 4) == "http"){
					echo "Waiting to drop job ";
					$randint = rand(45,75);
					sleep($randint);
					$query = $url;
					$sql = "INSERT INTO jobs VALUES (id, '$url', NULL, 'available');";
					$result = mysql_query($sql);
					if(!$result){
						echo "Could not insert new job" . mysql_error();
					}
					echo "--Dropped a new Job-- ";			
				}
			}
		}
		echo "All Jobs Dropped";
		fclose($handle);
	}

}
?>