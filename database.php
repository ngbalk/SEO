<?php
Class ScrapeDB{
	public $saved_sheets;
	public $saved_scripts;
	public $saved_images;
	public $host_id;


	public function __construct($username, $password, $hostname, $database){
		$this->saved_images = array();
		$this->saved_scripts = array();
		$this->saved_sheets = array();

		$dbhandle = mysql_connect($hostname, $username, $password)
			or die("Unable to connect to MySQL host");

		$selected = mysql_select_db($database, $dbhandle)
			or die("Could not select db");
		}
	
	public function init_save_page($page, $hostid){
		//raw_data variables
		$urltoinsert = mysql_real_escape_string($page->url);
		$htmltoinsert = mysql_real_escape_string($page->html->innertext);
		$datatype = "html";
		$numbytes = $page->size;
		//end
		$raw_data_insert = mysql_query("INSERT INTO raw_data (url, data_type, size, scraper_data) VALUES ('$urltoinsert', '$datatype', $numbytes, '$htmltoinsert');");
		if(!$raw_data_insert){
			die("	raw data insert error" . mysql_error());

		}
		$this->form_relations(mysql_insert_id(), $this->host_id);
		

		if(is_object($page) && isset($page)){
			if(is_object($page->title)){
				$pagetitle = mysql_real_escape_string($page->title->innertext);
			}
			else {
				$pagetitle = "--No Title--";
			}
		}
		else{
			$pagetitle = "--No Title--";
		}	
		$pagetype = "html";
		$webpages_insert=mysql_query("INSERT INTO webpages (tag_title, doctype, raw_data_id) VALUES ('$pagetitle', '$pagetype', last_insert_id());");
		if(!$webpages_insert){
			die("	webpages insert error: " . mysql_error());
		}
		$parent_id = mysql_insert_id();
		$parenttype = "html";
		//end
		// foreach ($page->myWords as $myword => $freq) {
		// 	$word = mysql_real_escape_string($myword);
		// 	$toinsert = mysql_query("INSERT INTO words (parent_id, word, frequency, parent_type) VALUES ($parent_id, '$word', $freq, '$parenttype');");
		// 	if(!$toinsert){
		// 		die("	word insert error: " . $word .   mysql_error());
		// 	}
		// }

		// foreach ($page->myTags as $mytag => $mycontent) {		
		// 	$tag = mysql_real_escape_string($mytag);
		// 	$content = mysql_real_escape_string($mycontent);
		// 	$toinsert = mysql_query("INSERT INTO tags (parent_id, tag, parent_type, content) VALUES ($parent_id, '$tag', '$parenttype', '$content');");
		// 	if(!$toinsert){
		// 		die("	tag insert error: " . $tag . $content .  mysql_error());
		// 	}
		// }
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
			$raw_data_id = mysql_insert_id();
			$this->form_relations($raw_data_id, $this->host_id);
			$sql = "INSERT INTO documents (type, raw_data_id) VALUES ('$datatype', '$raw_data_id');";
			$doc_insert = mysql_query($sql);
			if(!$doc_insert){
				die("Failed to insert into documents" . mysql_error());
			}
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
			$raw_data_id = mysql_insert_id();
			$this->form_relations($raw_data_id, $this->host_id);
			$sql = "INSERT INTO documents (type, raw_data_id) VALUES ('$datatype', '$raw_data_id');";
			$doc_insert = mysql_query($sql);
			if(!$doc_insert){
				die("Failed to CSS insert into documents" . mysql_error());
			}


		}
	}
	
	public function save_images($images){
			$type_array = array(
				IMAGETYPE_PNG => "png",
				IMAGETYPE_JPEG => "jpeg",
				IMAGETYPE_GIF => "gif",
				);
		foreach ($images as $src) {
			if(in_array($src, $this->saved_images)){
				continue;
			}
			array_push($this->saved_images, $src);
			$checktype = exif_imagetype($src);
			if(!array_search($checktype, $type_array)){
				$data_type = "Other Image Type";
			}
			else{
				$data_type = $type_array[$checktype];
			}
			$img = get_headers($src, 1);
			$img_size = $img["Content-Length"];
			$sql = "INSERT INTO raw_data (url, data_type, size) VALUES ('$src', '$data_type', '$img_size');";
			$result = mysql_query($sql);
			if(!$result){
				die ("Failed to save images" . mysql_error());
			}
			$raw_data_id = mysql_insert_id();
			$this->form_relations($raw_data_id, $this->host_id);
			$sql = "INSERT INTO documents (type, raw_data_id) VALUES ('$data_type', '$raw_data_id');";
			$doc_img_insert = mysql_query($sql);
			if(!$doc_img_insert){
				die("Failed to insert image into Documents" . mysql_error());
			}
		}
	}
	public function form_relations($raw_data_id, $host_id){
		$sql = "INSERT INTO relationships (raw_data_id, host_id) VALUES ('$raw_data_id', '$host_id');";
		$relations_insert = mysql_query($sql);
		if(!$relations_insert){
			die("failed to create relation in Relationships" . mysql_error());
		}
	}
	public function save_who_is($hostname, $who_is_data){
		$columns = "registrar, whois_server, referral_url, name_server, status, updated_date, creation_date, expiration_date, administrative_contact, technical_contact";
		$sql = "INSERT INTO web_host (host_name, " . $columns . ")";
		$sql_values = " VALUES ('$hostname', ";
		
		$columns_array = explode(", ", $columns);
		foreach ($columns_array as $column) {
			if(!array_key_exists($column, $who_is_data)){
				$data = "--Not Specified--";
			}
			else{
			$data = $who_is_data[$column];
			}
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
		
	}	
}

