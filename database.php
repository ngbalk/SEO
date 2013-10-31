<?php
/*
Connect to the database
*/
Class ScrapeDB{
	public $saved_sheets;
	public $saved_scripts;
	public $host_id;


	public function __construct($username, $password, $hostname, $database){
		$this->saved_scripts = array();

		$dbhandle = mysql_connect($hostname, $username, $password)
			or die("Unable to connect to MySQL host");
		echo "Connected to MySQL host<br>";

		$selected = mysql_select_db($database, $dbhandle)
			or die("Could not select db");
		echo "Selected db succesfully";
		}
	
	public function init_save_page($page, $hostid){
		//raw_data variables
		$urltoinsert = mysql_real_escape_string($page->url);
		$htmltoinsert = mysql_real_escape_string($page->html->plaintext);
		$datatype = "html";
		$numbytes = strlen($htmltoinsert);
		//end
		$raw_data_insert = mysql_query("INSERT INTO raw_data (url, data_type, size, scraper_data) VALUES ('$urltoinsert', '$datatype', $numbytes, '$htmltoinsert');");
		if(!$raw_data_insert){
			die("	raw data insert error" . mysql_error());

		}
		$this->form_relations(mysql_insert_id(), $this->host_id);
		echo "	succesful raw data insert";
		

		//webpages variables
		
		$pagetitle = mysql_real_escape_string($page->title->plaintext);
		
		
		$pagetype = "html";
		//$raw_data_id = explode("#", mysql_query("SELECT last_insert_id();"))[1];
		//end
		$webpages_insert=mysql_query("INSERT INTO webpages (tag_title, doctype, raw_data_id) VALUES ('$pagetitle', '$pagetype', last_insert_id());");
		if(!$webpages_insert){
			die("	webpages insert error: " . mysql_error());
		}
		echo "	succesful webpage insert";

		//word variables
		$parent_id = mysql_insert_id();
		$parenttype = "html";
		//end
		foreach ($page->myWords as $myword => $freq) {
			$word = mysql_real_escape_string($myword);
			$toinsert = mysql_query("INSERT INTO words (parent_id, word, frequency, parent_type) VALUES ($parent_id, '$word', $freq, '$parenttype');");
			if(!$toinsert){
				die("	word insert error: " . $word .   mysql_error());
			}
		}
		echo "	succesful word insert";

		//tag variables

		//end
		foreach ($page->myTags as $mytag => $mycontent) {		
			$tag = mysql_real_escape_string($mytag);
			$content = mysql_real_escape_string($mycontent);
			$toinsert = mysql_query("INSERT INTO tags (parent_id, tag, parent_type, content) VALUES ($parent_id, '$tag', '$parenttype', '$content');");
			if(!$toinsert){
				die("	tag insert error: " . $tag . $content .  mysql_error());
			}
		}
		echo "	succesful tag insert";
		echo "	_____DATABASE SAVE COMPLETE______";
	}

	public function init_host($rootpage){
		$id;
		$hostname = mysql_real_escape_string($rootpage->url);
		echo "MY HOST NAME" . $hostname;
		$sql = "INSERT INTO web_host (host_name) VALUES ('$hostname');";
		$host_insert = mysql_query($sql);
		if(!$host_insert){
			die("Failed to initialize host " . mysql_error());
		}
		echo "succesful host initialization insert";
		$id = mysql_insert_id();
		$this->host_id = $id;
		//saves whois data, host id, etc
		return $id;
	}
	public function close(){
		mysql_close();
	}
	public function save_scripts($scripts){
		foreach ($scripts as $link) {
			$linktocheck = array_shift(explode("?", $link));
			if(in_array($linktocheck, $this->saved_scripts)){
				continue;
			}
			array_push($this->saved_scripts, $linktocheck);
			$script = new Document($link);
			$urltoadd = mysql_real_escape_string($linktocheck);
			$datatype = mysql_real_escape_string(end(explode(".", $linktocheck)));
			$datatoadd = mysql_real_escape_string($script->data);
			$sizetoadd = strlen($datatoadd);
			$sql = "INSERT INTO raw_data (url, data_type, size, scraper_data) VALUES ('$urltoadd', '$datatype', '$sizetoadd', '$datatoadd');";
			$result = mysql_query($sql);
			if(!$result){
				die("Failed to insert scripts" . mysql_error());
			}
			echo "Succesful Script Insert";
			$raw_data_id = mysql_insert_id();
			$this->form_relations($raw_data_id, $this->host_id);
			$sql = "INSERT INTO documents (type, raw_data_id) VALUES ('$datatype', '$raw_data_id');";
			$doc_insert = mysql_query($sql);
			if(!$doc_insert){
				die("Failed to insert into documents" . mysql_error());
			}
			echo "Succesful insert into Documents";
		}

	}
	public function save_css($sheets){
		foreach ($sheets as $link) {
			
			if(in_array($link, $this->saved_sheets)){
				continue;
			}
			array_push($this->saved_sheets, $link);
			$sheet = new Document($sheet);
			$datatype = mysql_real_escape_string(end(explode(".", $link)));
			$datatoadd = mysql_real_escape_string($sheet->data);
			$sizetoadd = strlen($datatoadd);
			$sql = "INSERT INTO raw_data (url, data_type, size, scraper_data) VALUES ('$link', '$datatype', '$sizetoadd', '$datatoadd');";
			$result = mysql_query($sql);
			if(!$result){
				die("Failed to insert scripts" . mysql_error());
			}
			echo "Succesful Script Insert";
			$raw_data_id = mysql_insert_id();
			$this->form_relations($raw_data_id, $this->host_id);
			$sql = "INSERT INTO documents (type, raw_data_id) VALUES ('$datatype', '$raw_data_id');";
			$doc_insert = mysql_query($sql);
			if(!$doc_insert){
				die("Failed to insert into documents" . mysql_error());
			}
			echo "Succesful insert into Documents";


		}
	}
	public function form_relations($raw_data_id, $host_id){
		$sql = "INSERT INTO relationships (raw_data_id, host_id) VALUES ('$raw_data_id', '$host_id');";
		$relations_insert = mysql_query($sql);
		if(!$relations_insert){
			die("failed to create relation in Relationships" . mysql_error());
		}
		echo "Succesful relation insert into relationships";
	}	
}

