<?php
/*
Page Scraper
Author: Nick Balkissoon
@FrontCoding
Email: ngbalk@gmail.com

TODO: 
Get CSS files still not really sure how to do that.
Create a View?
Ask Paulus for the next step.
*/

class Page {
	
	public $html; //store as simple_html_dom
	public $url; //should be a string
	public $title;
	public $doctype; //not important, maybe we should delete this
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
	  	$this->find_html_tags();
	  	$this->find_all_words();
	  	$this->store_links_in();
	  	$this->store_links_out();
	}
	public function parse_title(){
		$this->title = $this->html->find("title",0);

	}

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
		$myurl = $this->url;
		$mydomain = getRegisteredDomain(parse_url($myurl, PHP_URL_HOST));
		$linksout = array();
		$all_links = $this->html->find("a"); 
		foreach ($all_links as $link) {
			$href = $link->href;
			@$nextdomain = getRegisteredDomain(parse_url($href, PHP_URL_HOST));
			if($nextdomain != "" && $nextdomain != $mydomain){
				array_push($linksout, $href);
			}

		}
		$this->links_out = $linksout;
		return $linksout;
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
	public function get_images(){
		$all_img = array();
		foreach ($this->html->find('img') as $obj) {
			$src = $obj->src;
			array_push($all_img, $src);
		}
		return $all_img;
	}
	public function table_traverse($tableid, $xright, $ydown){  //make this apply to all tables on a page, so that we can pull any data that we need at a later point in time.
		$table = $this->html->find("#" . $tableid);
		$row = $table->children([$ydown]);
		$cell = $row->children([$xright]);
		return $cell->innertext;

	}
	public function get_all_table_data(){
		$tables_array = array();
		$tables = $this->html->find("table");
		foreach ($tables as $table) {
			$rows_array = array();
			$rows = $table->find("tr");
			foreach ($rows as $row) {
				$cells = $row->find("td");
				$cells_array = array();
				foreach ($cells as $cell) {
					$value = $cell->innertext;
					array_push($cells_array, $value);
				}
				array_push($rows_array, $cells_array);
			}
			array_push($tables_array, $rows_array);
		}
		return $tables_array;
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
public function getwhois(){
	$query = $this->url;
	 if(!is_numeric($query[0])){
	 @$query = getRegisteredDomain(parse_url($query, PHP_URL_HOST));
	 }
	$whois = new Whois();

	$insert_array = array();
	$to_insert = "";
	$values = "registrar, whois_server, referral_url, name_server, status, updated_date, creation_data, expiration_date, administrative_contact, technical_contact";
	$result = $whois->Lookup($query);
	$regyinfo = $result['regyinfo'];
	$regrinfo = $result['regrinfo'];
	$domain = $regrinfo['domain'];

	$insert_array['whois_server'] = $regyinfo['referrer'];
	$insert_array['registrar'] = $regyinfo['registrar'];
	$insert_array['referral_url'] = $regyinfo['referrer'];
	$insert_array['host_name'] = $domain['name'];
	$insert_array['name_server'] = "";

	foreach ($domain['nserver'] as $server => $address) {
		$insert_array['name_server'] .= $server . ": " . $address . ", ";
	}
	$insert_array['status'] = "";

	foreach ($domain['status'] as $status) {
		$insert_array['status'] .= $status. ", ";
	}

	$insert_array['updated_date'] = $domain['changed'];
	$insert_array['creation_date'] = $domain['created'];
	$insert_array['expiration_date'] = $domain['expires'];

	$admin = $regrinfo['admin'];
	$tech = $regrinfo['tech'];

	$insert_array['administrative_contact'] = "";
	$insert_array['administrative_contact'] .=  " " . $admin['organization'];
	$insert_array['administrative_contact'] .=  " " . $admin['name'];
	$insert_array['administrative_contact'] .=  " " . $admin['type'];
	foreach ($admin['address'] as $info) {
		$insert_array['administrative_contact'] .=  " " . $info;
	}
	$insert_array['administrative_contact'] .=  " " . $admin['phone'];
	$insert_array['administrative_contact'] .=  " " . $admin['fax'];
	$insert_array['administrative_contact'] .=  " " . $admin['email'];


	$insert_array['technical_contact'] = "";
	$insert_array['technical_contact'] .= " " .  $tech['organization'];
	$insert_array['technical_contact'] .=  " " . $tech['name'];
	$insert_array['technical_contact'] .=  " " . $tech['type'];
	foreach ($tech['address'] as $info) {
		$insert_array['technical_contact'] .=  " " . $info;
	}
	$insert_array['technical_contact'] .= " " .  $tech['phone'];
	$insert_array['technical_contact'] .=  " " . $tech['fax'];
	$insert_array['technical_contact'] .=  " " . $tech['email'];
	return $insert_array;


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