<?php
ini_set('max_execution_time', 1000000);
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
require_once "slave.php";
require_once "information.php";
$machine_id = 1;
$mySlave = new Slave($hostname, $database, $username, $password, $machine_id);
$myAction = $mySlave->do_obey();
$myAction->execute();
// $sourcefile = "Milkpowder-new.csv";
// $handle = fopen($sourcefile, "r");
// while($row = fgetcsv($handle)) {
// 	foreach ($row as $url) {
// 		if(substr($url, 0, 4) == "http"){
// 			$query = $url;
// 			echo $query . '<br>';
// 			$myCrawler = new Crawler($query);
// 			if($myCrawler->doCrawl()) echo "Crawl Complete";			
// 		}
// 	}
// }
// echo "All Crawls Complete";
// fclose($handle);

// $myCrawler = new Crawler('http://www.a2milk.com.au/faq.php');
// if($myCrawler->doCrawl()){
// 	echo "Crawl Complete";
// }


//A2 Platinum,A2 ??,http://www.a2milk.com.au/faq.php,New Zealand,A2-Platinum Folder (1 pic)
