<?php

//This class will do the looping through urls, storing and updating as we scrape.  Will call methods in page_scrape.php in order to find all urls on page.
require_once "ultimate-web-scraper/support/http.php";
require_once "ultimate-web-scraper/support/web_browser.php";
require_once "ultimate-web-scraper/support/simple_html_dom.php";
require_once "page_scrape.php";
require_once "database.php";
Class Crawler {
	public $myRootUrl;
	public $myRootPage
	public $to_scrape; //Queue that stores the urls that we still need to visit. will use methods array_push(array, value) and array_shift() which pops off queued value
	public $myVisited; //urls that we have already scraped

	public function __construct($root){

		$this->to_scrape = array();
		$this->myVisited = array();
		$this->myRootUrl = $root;
		$this->myRootPage = new Page($root);
		foreach ($myRootPage->links_in as $key => $value) {
			array_push($to_scrape, $value);
		}
	}
	public function doCrawl(){
		while (!empty($this->to_scrape)) {
			$url = array_shift($this->to_scrape);
			array_push($this->myVisited, $url);
			$current = new Page($url);
			$myDB = new ScrapeDB($current);
			$hostid = $myDB->init_host($this->myRootPage);
			$myDB->init_save_page($current, $hostid);
			$all_links_in = $current->links_in;
			foreach ($all_links_in as $key => $next) {
				if(!in_array($next, $this->myVisited){
					array_push($this->to_scrape, $next);
				}
			}
		}
	}
}


?>