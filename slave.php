<?php

Class Slave{
	public $machine_id;
	public $hostname;
	public $database;
	public $username;
	public $password;

	public function __construct($hostname, $database, $username, $password, $machine_id){
		$this->machine_id = $machine_id;
		$this->hostname = $hostname;
		$this->database = $database;
		$this->username = $username;
		$this->password = $password;
	}
	public function doObey(){
		$action = new Obey($this->hostname, $this->database, $this->username, $this->password, $this->machine_id);
		return $action;
	}
	public function doParseRawData(){
		$action = new ParseRawData($this->hostname, $this->database, $this->username, $this->password, $this->machine_id);
		return $action;
	}
}

Class Obey{
	public $machine_id;

	public function __construct($hostname, $database, $username, $password, $machine_id){
		$this->db_connect($hostname, $database, $username, $password, $machine_id);
		$this->machine_id = $machine_id;

	}

	public function db_connect($hostname, $database, $username, $password){	
		$dbhandle = mysql_connect($hostname, $username, $password)
				or die("Unable to connect to MySQL host");
		echo "connected to host";
		$selected = mysql_select_db($database, $dbhandle)
				or die("Could not select db");
		echo "selected database";
		mysql_set_charset('utf8');
		mysql_query("SET NAMES utf8");
	}

	public function execute(){
		$found_job = $this->check_for_job();
		while(!$found_job){
			echo "Waiting...";
			$wait = rand(5,20);
			sleep($wait);
			$found_job = $this->check_for_job();
		}
	}

	public function check_for_job(){
		$sql = "SELECT * FROM jobs WHERE status = 'available' LIMIT 1;";
		$result = mysql_query($sql);
		if(!$result){
			die ("Query failed" . mysql_error());
			return false;
		}
		if(mysql_num_rows($result) == 0){
			echo "No jobs available, check back later";
			return false;
		}
		else{
			echo "Found a job, checking out this job";
			$row = mysql_fetch_array($result);
			$this->check_out($row[0], $row[1]);
			return true;
		}
	}

	public function check_out($id, $url){
		$sql = "UPDATE jobs SET status = 'working', machine_id = '$this->machine_id' WHERE id = '$id';";
		$result = mysql_query($sql);
		if(!$result){
			echo "changing status to working failed" . mysql_error();
		}
		$status;
		$Crawler = new Crawler($url);
		if($Crawler->valid){
			$Crawler->doCrawl();
			echo "Succesfully Crawled " . $url;
			$status=true;
		}	
		else{
			$status = false;
		}
		$this->check_in($status, $id);

	}

	public function check_in($status, $id){
		if($status){
			$ins = 'complete';
		}
		else{
			$ins = 'failed';
		}
		$sql = "UPDATE jobs SET status = '$ins' WHERE id = '$id';";
		$result = mysql_query($sql);
		if(!$result){
			echo "Could not update status to complete" . mysql_error();
		}
		$this->execute();	
	}


}
Class ParseRawData{
	public $Dictionary;

	public function __construct($hostname, $database, $username, $password, $machine_id){
		$this->Dictionary = new Dictionary;
		$this->Dictionary->loadDictionary("smart-character-segmenter/cwordict_fast.dic");
		$this->db_connect($hostname, $database, $username, $password, $machine_id);
		$this->machine_id = $machine_id;
	}
	public function db_connect($hostname, $database, $username, $password){	
		$dbhandle = mysql_connect($hostname, $username, $password)
				or die("Unable to connect to MySQL host");
		echo "connected to host";
		$selected = mysql_select_db($database, $dbhandle)
				or die("Could not select db");
		echo "selected database";
		mysql_set_charset('utf8');
		mysql_query("SET NAMES utf8");
	}
	public function execute(){
		$this->check_for_job();
	}
	public function check_for_job(){
		$sql = "SELECT * FROM jobs WHERE status = 'complete' LIMIT 1;";
		$result = mysql_query($sql);
		if(!$result){
			die ("Query failed" . mysql_error());
			return false;
		}
		if(mysql_num_rows($result) == 0){
			echo "No unparsed raw_data avaialable, Checking back again later";
			return false;
		}
		else{
			echo "Found raw data, checking out this raw data to parse";
			$row = mysql_fetch_array($result);
			$this->check_out($row[0], $row[1]);
			return true;
		}
	}
	public function check_out($id, $url){
		echo $url." ";
		$sql = "UPDATE jobs SET status = 'parsing', machine_id = '$this->machine_id' WHERE id = '$id';";
		$result = mysql_query($sql);
		if(!$result){
			echo "changing status to parsing failed" . mysql_error();
		}
		$url = mysql_real_escape_string($url);
		$status = true;
		$sql = "SELECT id FROM web_host WHERE host_name = '$url' LIMIT 1;";
		$result = mysql_query($sql);
		if(!$result){
			die("Could not get host_id ".mysql_error());
		}
		$row = mysql_fetch_array($result);
		$host_id = $row[0];
		$sql = "SELECT * FROM raw_data WHERE id IN (SELECT r.raw_data_id FROM relationships as r, web_host as w WHERE r.host_id = w.id and r.host_id IN (SELECT id FROM web_host as w2 WHERE w2.host_name = '$url'));";
		$result = mysql_query($sql);
		if(!$result){
			die("Failed to get raw data from database in order to scrape" . mysql_error());
		}
		$row = mysql_fetch_array($result);
		while ($row) {
			$raw_data_id = $row[0];
			$url = $row[1];
			$data_type = $row[2];
			$size = $row[3];
			$stamp = $row[4];
			$raw_data = $row[5];
			$html = str_get_html($raw_data);
			$Page = new Page($url, $html, $this->Dictionary);
			$Page->init();
			include "information.php";
			$Database = new ScrapeDB($username, $password, $hostname, $database);
			$Database->init_save_page($Page, $host_id, "parse");
			$this->Dictionary->clear();
			$row = mysql_fetch_array($result);
		}
		$this->check_in($status, $id);
	}
	public function check_in($status, $id){
		if($status){
			$ins = 'parsed';
		}
		else{
			$ins = 'failed';
		}
		$sql = "UPDATE jobs SET status = '$ins' WHERE id = '$id';";
		$result = mysql_query($sql);
		if(!$result){
			echo "Could not update status to parsed" . mysql_error();
		}
		$this->execute();	
	}
}

?>