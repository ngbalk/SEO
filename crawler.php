<?php

Class Crawler {
	public $rootData;
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
			$this->rootData = $root_data;
			$this->myRootPage = new Page($root, $root_data->html, $Dictionary = NULL);
			$this->myRootPage->store_links_in();
	  		$this->myRootPage->get_file_size();
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
		$Database = new ScrapeDB($username, $password, $hostname, $database);
		$hostid = $Database->init_host($this->rootData);
		while (!empty($this->to_scrape) && $pageCount <= $maxPages) {
			$pageCount += 1;
			$url = array_shift($this->to_scrape);
			array_push($this->myVisited, $url);
			$raw_data = new Raw_Data($url);
			if($raw_data->valid == false){
				continue;
			}
			else{
				$current = new Page($raw_data->url, $raw_data->html, $Dictionary = NULL);
	  			$current->store_links_in();
	  			$current->get_file_size();
				$Database->init_save_page($current, $hostid, "raw");
				//$Database->save_scripts($current->get_scripts());
				//$Database->save_images($current->get_images());
				//$current->get_all_table_data();
				//$current->table_traverse(tableid, x to the right, y down); //returns contents of the specified cell
				//$current->get_tag(specified tag) //returns an array containing all contents of the tag.
				//$Database->save_css($current->get_css());
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