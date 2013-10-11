<?php
require_once "ultimate-web-scraper/support/http.php";
require_once "ultimate-web-scraper/support/web_browser.php";
require_once "ultimate-web-scraper/support/simple_html_dom.php";
$myPage = new Page("http://www.frontcoding.com");
echo $myPage->title->plaintext // ->plaintext is !important
. " " . $myPage->links_count_in
. " " . $myPage->links_count_out
. " " . $myPage->html
. " " . $myPage->language
. " " . $myPage->doctype;
class Page{
	public $html; //store as simple_html_dom
	public $url; //should be a string
	public $title;
	public $doctype; //not important, maybe we should delete this
	public $links_count_in;
	public $links_count_out;
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
	  			$this->parse_title();//parse for title
	  			//parse for doctype
	  			$this->parse_linksin();//count number of links in
	  			$this->parse_linksout();//count number of links out
	  			$this->check_language();//determine the language !could be tricky... still thinking of a method to do this (maybe just determine if Chinese i.e. non ASCII)
	  			$this->get_doctype();



	  		}

	}
	public function get_html($result){
		$this->html->load($result["body"]);
	}
	public function parse_title(){
		$this->title = $this->html->find("title",0);
	}
	public function parse_doctype(){ 
		//not so sure how important this is... if its not an HTML we can't even do anything with it, so this attribute is irrelevent
	}
	public function parse_linksin(){
		$linksin = array();
		$all_links = $this->html->find("a"); //this array stores all anchors, now need to find if are links in/links out
		foreach ($all_links as $link) {
			$href = $link->href;
			if(strpos($href, $this->url) !== false){
				array_push($linksin, $href);
			}
		}
			$this->links_count_in = count($linksin);
	}
	public function parse_linksout(){
		$linksout = array();
		$all_links = $this->html->find("a"); 
		foreach ($all_links as $link) {
			$href = $link->href;
			if(strpos($href, $this->url) == false){
				array_push($linksout, $href);
			}
		}
			$this->links_count_out = count($linksout);

	}
	public function check_language(){
		if (!preg_match("/[^A-Za-z0-9]/", $this->title)){
			$this->language = "E"; //E stands for 'English'
    		// string contains only english letters & digits
			}	
		else{
			$this->language = "!E"; //!E stands for 'Not English'
			}
	}
	public function get_doctype(){

	// $content = file_get_contents($this->url);
 //   	$content = str_replace("\n","",$content);
 //    $get_doctype = preg_match_all('/(<!DOCTYPE.+\">)<html/i',$content,$matches);
 //    $doctype = $matches[1][0];
 //    $this->doctype = $doctype;
	// }



}
	public function words(){
		/*
		This function will look for all words in the HTML as well as find the frequency of each word.  When we store these in a the database
		"keyword" we will store the word with the words frequency.

		*/
	}
	

?>