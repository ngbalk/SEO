<?php

Class Slave{
	public $machine_id;
	public $urls_scraped;
	public $hostname;
	public $database;
	public $username;
	public $password;

	public function __construct($hostname, $database, $username, $password, $machine_id){
		$this->machine_id = $machine_id;
		$this->urls_scraped = $urls_scraped;
		$this->hostname = $hostname;
		$this->database = $database;
		$this->username = $username;
		$this->password = $password;
	}
	public function do_obey(){
		new Obey($this->hostname, $this->database, $this->username, $this->password, $this->machine_id);
	}
}

Class Obey{
	public $machine_id;

	public function __construct($hostname, $database, $username, $password, $machine_id){
		db_connect($hostname, $database, $username, $password, $machine_id);
		$this->machine_id = $machine_id;

	}

	public function db_connect($hostname, $database, $username, $password){	
		$dbhandle = mysql_connect($hostname, $username, $password)
				or die("Unable to connect to MySQL host");
		$selected = mysql_select_db($database, $dbhandle)
				or die("Could not select db");
	}

	public function check_for_job(){
		$sql = "SELECT 'url' FROM 'jobs' WHERE 'status' = 'available' LIMIT 1;";
		$result = mysql_query($sql);
		if(mysql_num_rows($result) == 0){
			return;
		}
		else{
			check_out($result);
		}
	}

	public function check_out($url){
		$status;
		$Crawler = new Crawler($url);
		if($Crawler){
			echo "Succesfully Crawled " . $url;
			$status=true;
		}
		else{
			$status = false;
		}
		check_in($status);

	}

	public function check_in($status){

	}

	public function set_status($status){
		
	}
}

?>