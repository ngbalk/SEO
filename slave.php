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
	public function do_obey(){
		$action = new Obey($this->hostname, $this->database, $this->username, $this->password, $this->machine_id);
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
		if($Crawler->doCrawl()){
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

?>