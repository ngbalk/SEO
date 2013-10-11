<?php
require_once "ultimate-web-scraper/support/http.php";
require_once "ultimate-web-scraper/support/web.php";
require_once "ultimate-web-scraper/support/simple_html_dom.php";

class Page{
	public $html;
	public $url; //should be a string
	public $title;
	public $doctype; //not important, maybe we should delete this
	public $linksin_count;
	public $linksout_count;
	public $language;
	public $timestamp;
	public $websiteid; //not sure how this will be determined

	public function __construct($url){
		$this->url = $url;
		$this->html = new simple_html_dom();
		$web = new WebBrowser();
		$result = $web->Process($url);
	  	if (!$result["success"])  echo "Error retrieving URL.  " . $result["error"] . "\n";
	  	else if ($result["response"]["code"] != 200)  echo "Error retrieving URL.  Server returned:  " . $result["response"]["code"] . " " . $result["response"]["meaning"] . "\n";
	  	else
	  		{
	  			$this->get_html($result);//get the body of the html, store as attribute in class (blob)
	  			//parse for title
	  			//parse for doctype
	  			//count number of links in
	  			//count number of links out
	  			//determine the language !could be tricky... still thinking of a method to do this (maybe just determine if Chinese i.e. non ASCII)
	  			//



	  		}

	}
	public function get_html($result){
		$this->html = load($result["body"]);
	}
	public function parse_title(){
		$this->title = $this->html->('title');
	}
	public function parse_doctype(){ 
		//not so sure how important this is... if its not an HTML we can't even do anything with it, so this attribute is irrelevent
	}
	public function parse_linksin(){
		$all_links = $this->html->find("a"); //this array stores all anchors, now need to find if are links in/links out
		foreach ($all_links as $link) {
			$href = $link->href;
		}

	}
	public function parse_linksout(){

	}



}
}
?>