<?php

Class Crawler {
	public $myRootData;
	public $myRootUrl;
	public $myRootPage;
	public $to_scrape; 
	public $myVisited; 
	public $valid;

	public function __construct($root){
		global $root_url;
		$root_url = $root;
		$this->to_scrape = array();
		$this->myVisited = array();
		$this->myRootUrl = $root;
		$root_data = new Raw_Data($root); 
		if(!$root_data->valid){
			$this->valid=false;
		}
		else{
			$this->valid=true;
			$this->myRootData = $root_data;
			$this->myRootPage = new Page($root, $root_data->html);
			$this->myRootPage->init();

			foreach ($this->myRootPage->links_in as $key => $value) {
				if(!in_array(rtrim($value,"/"), $this->to_scrape)){
					array_push($this->to_scrape, rtrim($value, "/"));
				}
			}
		}

	}
	public function doCrawl(){
		$pageCount = 0;
		include "information.php";
		$myDB = new ScrapeDB($username, $password, $hostname, $database);
		$hostid = $myDB->init_host($this->myRootData);
		while (!empty($this->to_scrape) && $pageCount <= $maxPages) {
			$pageCount += 1;
			$url = array_shift($this->to_scrape);
			array_push($this->myVisited, $url);
			$raw_data = new Raw_Data($url);
			if($raw_data->valid == false){
				continue;
			}
			else{
				$current = new Page($raw_data->url, $raw_data->html);
				$current->init();
				$myDB->init_save_page($current, $hostid);
				//$myDB->save_scripts($current->get_scripts());
				//$myDB->save_images($current->get_images());
				//$current->get_all_table_data();
				//$current->table_traverse(tableid, x to the right, y down); //returns contents of the specified cell
				//$current->get_tag(specified tag) //returns an array containing all contents of the tag.
				//$myDB->save_css($current->get_css());
				$all_links_in = $current->links_in;
				foreach ($all_links_in as $key => $next) {
					if(!in_array(rtrim($next,"/"), $this->myVisited) && !in_array(rtrim($next,"/"), $this->to_scrape)){
						array_push($this->to_scrape, rtrim($next,"/"));
					}
				}
			}
		}
		return true;
		
	}
}


?>