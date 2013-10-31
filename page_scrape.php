<?php
/*
Page Scraper
Author: Nick Balkissoon
@FrontCoding
Email: ngbalk@gmail.com

TODO: 
CREATE method for getting doctype, whois data, file size, file type.  Currently we are inserting into the database using static attributes
for sake of debugging.
*/

class Page {
	
	public $html; //store as simple_html_dom
	public $url; //should be a string
	public $title;
	public $doctype; //not important, maybe we should delete this
	public $links_count_in; //no longer needed but will keep for now anyway for sake of debugging
	public $links_count_out; //no longer needed
	public $timestamp;
	public $websiteid; //not sure how this will be determined
	public $myTags;
	public $myWords;
	public $links_out;
	public $links_in;
	public $size;

	public function __construct($url, $html){
		
		$this->url = $url;
		$this->html = $html;	

	  		

	}

	public function init(){
		$this->parse_title();//parse for title
	  	$this->get_doctype();
	  	$this->find_html_tags();
	  	$this->find_all_words();
	  	$this->store_links_in();
	  	$this->store_links_out();
	}
	public function parse_title(){
		$this->title = $this->html->find("title",0);

	}

	/*
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
	*/
	public function store_links_in(){
		$global_url = $GLOBALS['root_url'];
		$linksin = array();
		$all_links = $this->html->find("a"); //this array stores all anchors, now need to find if are links in/links out
		foreach ($all_links as $link) {
			$href = $link->href;
			if(strpos($href, $global_url) !==false ){
				array_push($linksin, $href);
			}
			if(substr($href, 0, 1) == "/"){
				if(substr($global_url, -1) == "/"){
					$strtopush = $global_url . substr($href, 1);
				}
				else{
					$strtopush = $global_url . $href;
				}	
				array_push($linksin, $strtopush);
			}

		}
		$this->links_in = $linksin;
	}
	public function store_links_out(){
		$linksout = array();
		$all_links = $this->html->find("a"); 
		foreach ($all_links as $link) {
			$href = $link->href;
			if(strpos($href, $this->url) == false){
				array_push($linksout, $href);
			}
		}
		$this->links_out = $linksout;
	}

	public function get_doctype(){


		}

	public function find_html_tags(){
		$this->myTags = array();
		$htmltags = array("head","meta[content]","title","body","div","a[href]","span","bold","p",
				"header","ul","ol","li","table","tr","td","h1","h2","h3","h4","h5", "h6", "footer",
				"img[src]","img[alt]","menu","strong", "a" , "a[href]", "*[id]");
		foreach ($htmltags as $tag) {
			$concat = "";
			$tagcontent = $this->html->find($tag);
			foreach ($tagcontent as $it) {
				if($tag == "*[id]"){
					$concat.= ", " . $it->id;
				}
				elseif ($tag == "img[src]") {
					$concat.= ", " . $it->src;
				}
				elseif ($tag == "img[alt]"){
					$concat.= ", " . $it->alt;
				}
				elseif ($tag == "a[href]"){
					$concat.= ", " . $it->href;
				}
				elseif($tag == "meta[content]"){
					$concat.= ", " . $it->content;
				}
				else{
				$concat.= ", " . $it->plaintext;
				}
			}
			$this->myTags[$tag] = $concat;
			
		}
	}
	public function get_tag($tag){
		$ret = array();
		$all = $this->html->find($tag);
		foreach ($all as $con) {
			array_push($ret, $all->innertext);
		}
		return $ret;
	}
	public function find_all_words(){
		$tagstocheck = array("p","h1","h2","h3","h4","h5","h6","title","li","td","alt","a");
		$wordfreqs = array();
		foreach ($tagstocheck as $tag) {
			$elements= $this->html->find($tag);
				foreach ($elements as $element) {
					$words = explode(" ", $element->plaintext);
					foreach ($words as $word) {
						if(!array_key_exists($word, $wordfreqs)){
							$wordfreqs[$word] = 0;
						}
						$wordfreqs[$word]+=1;
					}
				}
			}
			arsort($wordfreqs);
			$this->myWords=$wordfreqs;
		}
	public function get_file_size(){
			$this->size = strlen($this->html->plaintext);		
		}

	public function get_scripts(){
		$all_scripts = array();
		foreach ($this->html->find('script') as $obj) {
			
	
			$src = $obj->src;
			array_push($all_scripts, $src);
		}
		return $all_scripts;
	}
	public function get_css(){
		$all_css = array();
		foreach ($this->html->find('link[type = text/css]') as $obj) {
			$src = $obj->href;
			array_push($all_css, $src);
		}
		return $all_css;
	}
	public function table_traverse($tableid, $xright, $ydown){  //make this apply to all tables on a page, so that we can pull any data that we need at a later point in time.
		$table = $this->html->find("#" . $tableid);
		$row = $table->children([$ydown]);
		$cell = $row->children([$xright]);
		return $cell->innertext;

	}


}

	class Raw_Data {
		public $url;
		public $html;
		public $data;
		public function __construct($url){

			$this->url = $url;
			$this->html = new simple_html_dom();
			$web = new WebBrowser();
			$result = $web->Process($url);
		  	if (!$result["success"])  echo "Error retrieving URL.  " . $result["error"] . "\n";
		  	else if ($result["response"]["code"] != 200)  echo "Error retrieving URL.  Server returned:  " . $result["response"]["code"] . " " . $result["response"]["meaning"] . "\n";
		  	else{
		  		$this->html->load($result['body']);
		  	}

		}
		public function get_who_is(){
			$whois = new Whois();
			if(!$whois->ValidDomain($this->url) ){
			echo ' Sorry, the domain is not valid or not supported. ';
			}

		}


}
	class Document {

		public $url;
		public $data;
		 public function __construct($url){
		 	$this->url = $url;
		 	if($url != ""){
		 		$this->data = file_get_contents($url);
		 	}
		 }
}






?>