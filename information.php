<?php
	$username = "root";
	$password = "root";
	$hostname = "localhost";
	$database = "seo";
	$machine_id = 1;
	$responsibility = array(0,100); //this parameter is for parsing the words after scraping, the first is the lower percentile, and the second is the higher percentile
	$sourcefile = "Milkpowder-new.csv"; //specify the csv file you are going to be pulling urls from.
	$maxPages = 800;  //for the sake of efficiency and good data, set the max number of pages within one domain that the scraper will scrape.  This is important because it prevents one large domain from skewing the rest of the data.

