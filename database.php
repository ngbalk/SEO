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

	public function init_host($root){
		$hostname = mysql_real_escape_string($root->url);
		$this->save_who_is($hostname, $root->getwhois());
		$id = mysql_insert_id();
		$this->host_id = $id;
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
				die("Failed to CSS insert into documents" . mysql_error());
			}
			echo "Succesful CSS insert into Documents";


		}
	}
	
	public function save_images($images){
		foreach ($images as $src) {
			$data_type = mysql_real_escape_string(end(explode(".", $src)));
			$img = get_headers($src, 1);
			$img_size = $img["Content-Length"];
			$sql = "INSERT INTO raw_data (url, data_type, size) VALUES ('$src', '$data_type', '$img_size');";
			$result = mysql_query($sql);
			if(!$result){
				die ("Failed to save images" . mysql_error());
			}
			echo "Succesful image save";
			$raw_data_id = mysql_insert_id();
			$this->form_relations($raw_data_id, $this->host_id);
			$sql = "INSERT INTO documents (type, raw_data_id) VALUES ('$data_type', '$raw_data_id');";
			$doc_img_insert = mysql_query($sql);
			if(!$doc_img_insert){
				die("Failed to insert image into Documents" . mysql_error());
			}
			echo "Succesfully inserted Image into Documents";
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
	public function save_who_is($hostname, $who_is_data){
		$columns = "registrar, whois_server, referral_url, name_server, status, updated_date, creation_date, expiration_date, administrative_contact, technical_contact";
		$sql = "INSERT INTO web_host (host_name, " . $columns . ")";
		$sql_values = " VALUES ('$hostname', ";
		
		$columns_array = explode(", ", $columns);
		foreach ($columns_array as $column) {
			$data = $who_is_data[$column];
			$toadd = mysql_real_escape_string($data);
			$sql_values.= "'" . $toadd . "'" .", ";
		}
		$sql_values = substr($sql_values, 0, -2);
		$sql_values.=");";
		$sql .= $sql_values;
		$who_is_insert = mysql_query($sql);
		if(!$who_is_insert){
			die("Failed to insert who is data and initialize host  <br>" . $sql . "<br>" . mysql_error());
		}
		echo "Succesful Insert of Who Is Data and host initialization";
		
	}	
}

