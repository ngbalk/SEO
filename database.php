<?php
/*
Connect to the database
*/
Class ScrapeDB{



	public function __construct(){
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
		}
	}
	public function init_save_page($page, $hostid){
		//raw_data variables
		$urltoinsert = $page->url;
		$htmltoinsert = $page->html->plaintext;
		$datatype = "html";
		$numbytes = strlen($htmltoinsert);
		//end
		$raw_data_insert = mysql_query("INSERT INTO raw_data (url, data_type, size, scraper_data) VALUES ($urltoinsert, $datatype, $numbytes, $htmltoinsert)");
		if(!$raw_data_insert){
			die("raw data insert error" . mysql_error());

		}
		

		//webpages variables
		$pagetitle = $page->title;
		$pagetype = "html";
		$raw_data_id = mysql_query("SELECT last_insert_id()");
		//end
		$webpages_insert=mysql_query("INSERT INTO webpages (tag_title, doctype, raw_data_id) VALUES ($page->title, $pagetype, $raw_data_id)");
		if(!$webpages_insert){
			die("webpages insert error: " . mysql_error());
		}

		//word variables
		$parent_id = mysql_query("SELECT last_insert_id()");
		$parenttype = "html";
		//end
		foreach ($page->myWords as $word => $frequency) {
			$toinsert = mysql_query("INSERT INTO words (parent_id, word, frequency, parent_type) VALUES ($parent_id, $word, $frequency, $parenttype)");
			if(!$toinsert){
				die("word insert error: " .  mysql_error());
			}
		}

		//tag variables

		//end
		foreach ($page->myTags as $tag => $content) {
		
			$toinsert = mysql_query("INSERT INTO tags (parent_id, tag, parent_type, content) VALUES ($parent_id, $tag, $parenttype, $content)");
			if(!$toinsert){
				die("word insert error: " .  mysql_error());
			}
		}
		echo "_____DATABASE SAVE COMPLETE______";
	}

	public function init_host($rootpage){
		$id;
		$hostname = $rootpage->url;
		$host_insert = mysql_query("INSERT INTO web_host (host_name) VALUES ($hostname)");
		if(!$host_insert){
			echo "Failed to initialize host "
		}
		$id = mysql_query("SELECT last_insert_id()");
		//saves whois data, host id, etc
		return $id;
	}

?>