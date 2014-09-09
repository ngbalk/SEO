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
require_once "crawler.php";
require_once "slave.php";
require_once "information.php";
require_once "smart-character-segmenter/Dictionary.php";
$mySlave = new Slave($hostname, $database, $username, $password, $machine_id);
$SlaveScraper = $mySlave->doObey();
$SlaveParser = $mySlave->doParseRawData();
$SlaveScraper->execute(); //Activate this one to scrape for raw data
$SlaveParser->execute(); //Activate this one to parse all raw data



