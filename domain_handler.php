<?php

//This class will do the looping through urls, storing and updating as we scrape.  Will call methods in page_scrape.php in order to find all urls on page.
// download SEO from, and get the exact same results.  we want to scrape the things that they are scraping.
require_once "phpwhois-4.2.2/whois.main.php";
require_once "ultimate-web-scraper/support/http.php";
require_once "ultimate-web-scraper/support/web_browser.php";
require_once "ultimate-web-scraper/support/simple_html_dom.php";
require_once "page_scrape.php";
require_once "database.php";
require_once "information.php";


ini_set('max_execution_time', 300);
$myCrawler = new Crawler("http://frontcoding.com/");
$myCrawler->doCrawl();
Class Crawler {

	public $myRootUrl;
	public $myRootPage;
	public $to_scrape; //Queue that stores the urls that we still need to visit. will use methods array_push(array, value) and array_shift() which pops off queued value
	public $myVisited; //urls that we have already scraped

	public function __construct($root){
		global $root_url;
		$root_url = $root;
		$this->to_scrape = array();
		$this->myVisited = array();
		$this->myRootUrl = $root;
		
		$root_data = new Raw_Data($root);
		$this->myRootPage = new Page($root, $root_data->html);
		$this->myRootPage->init();
		
		
		echo "LINKS IN" . var_dump($this->myRootPage->links_in) . "<br>" ;

		foreach ($this->myRootPage->links_in as $key => $value) {
			if(!in_array($value, $this->to_scrape)){
				array_push($this->to_scrape, $value);
			}
		}
	}
	public function doCrawl(){
		$myDB = new ScrapeDB($GLOBALS['username'], $GLOBALS['password'], $GLOBALS['hostname'], $GLOBALS['database']);
		$hostid = $myDB->init_host($this->myRootPage);
		while (!empty($this->to_scrape)) {
			$url = array_shift($this->to_scrape);
			array_push($this->myVisited, $url);
			$raw_data = new Raw_Data($url);
			$current = new Page($raw_data->url, $raw_data->html);
			$current->init();
			//$current->table_traverse(tableid, x to the right, y down); //returns contents of the specified cell
			//$current->get_tag(specified tag) //returns an array containing all contents of the tag.
			$myDB->init_save_page($current, $hostid);
			$myDB->save_scripts($current->get_scripts());
			$myDB->save_css($current->get_css());

			$all_links_in = $current->links_in;
			foreach ($all_links_in as $key => $next) {
				if(!in_array($next, $this->myVisited) && !in_array($next, $this->to_scrape)){
					array_push($this->to_scrape, $next);
				}
			}
		}
		$myDB->close();
	}
}


?>