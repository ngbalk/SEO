<?php
ini_set('max_execution_time', 1000);
require_once "phpwhois-4.2.2/whois.main.php";
require_once "phpwhois-4.2.2/whois.utils.php";
require_once("registered-domains-php-master/effectiveTLDs.inc.php");
require_once("registered-domains-php-master/regDomain.inc.php");
require_once "ultimate-web-scraper/support/http.php";
require_once "ultimate-web-scraper/support/web_browser.php";
require_once "ultimate-web-scraper/support/simple_html_dom.php";
require_once "page_scrape.php";
require_once "database.php";
require_once "domain_handler.php";
// $sourcefile = "Milkpowder-new.csv";
// $handle = fopen($sourcefile, "r");
// while($row = fgetcsv($handle)) {
// 	foreach ($row as $url) {
// 		if(substr($url, 0, 4) == "http"){
// 			echo $query . '<br>';
// 			$query = $url;
// 			$myCrawler = new Crawler($query);
// 			$myCrawler->doCrawl();			
// 		}
// 	}
// }
// fclose($handle);

$myCrawler = new Crawler('http://www.holle.cn/');
if($myCrawler->doCrawl()){
	echo "Crawl Complete";
}
// $ret = ConvertRelativeToAbsoluteURL(ExtractURL('http://www.google.com'), 'http://www.espn.com/index.php');
// echo var_dump($ret);
// $thisthing = ExtractURL('node/56');
// echo var_dump($thisthing['host']);
