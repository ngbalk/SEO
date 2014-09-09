Nick Balkissoon
Version 0.0.1
12/16/2013
ngbalk@gmail.com

The code here is distributable, so just download the .zip

You can run this on any computer that has php already installed.  All you need to do is go to command line, cd to the directory
that contains the the files, and execute the function execute.php.  This function creates a Slave class that pings a database
for urls to scrape.  In order to connect to specify the database that you would like to connect to, specify this in 
information.php.   

information.php contains all of the specifications that you need in order to succesfully run this.
	$username = "root";  //specify the username for your database
	$password = "root"; //specify the password for your database
	$hostname = "localhost"; //specify the hostname of your database
	$database = "seotest"; //specify the name of the database
	$machine_id = 1; //specify a number that will help the database know which machine is parsing which jobs (all that matters is that all machine ids are unique)
	$sourcefile = "Milkpowder-new.csv"; //specify the csv file you are going to be pulling urls from.
	$maxPages = 500;  //for the sake of efficiency and good data, set the max number of pages within one domain that the scraper will scrape.  This is important because it prevents one large domain from skewing the rest of the data.
Instructions:


Preconditions:

Use "seotest.sql" to create a database in your database.
Check to make sure database collation is set to UTF-8
Set MYSQL's "max_allowed_packet" varaible to at least 32M







Master Computer:

1)From command line, cd into web-scraper (or whichever folder you have holding all of the files). 

2)In "information.php" Be sure to set the information for connection to your database.  Also, be sure to set $sourcefile = (path to you text file of urls).  See above for an example. 

3)Run "master.php".  On Windows, this can be done as:	 C:\(path to your php.exe) -f "C:\(path to your master.php)" 
For example, on my system I write:	 C:\xampp\php\php.exe -f "C:\xampp\htdocs\SEO\master.php" 
On Linux, simply cd into the directory containing "master.php" and type: 	php "master.php"

4)master.php will run and periodically "drop" jobs (urls) into the database for the Slave computers to pick up and begin scraping.




Slave Computer

1)From command line, cd into web-scraper (or whichever folder you have holding all of the files).

2)In "information.php", be sure to set your database information, as well as your machine_id and responsibility.  For machine_id choose any unique integer
that you think other Slave machines will not have. In execute.php configure what you want the tool to do.  IMPORTANT!!! If you merely want the tool to do the raw data scraping
(so that you can parse at a later time), comment-out $SlaveParser->execute();  If you want the tool to only do parsing (assuming that you have already scraped the raw data
and stored it in the database) comment-out $SlaveScraper->execute();  The system will not automatically begin parsing after scraping, so after scraping is finished, you must
manually change and run the tool again to do the parsing.

3)By default, our machine will not scrape images and files such as javascript files, but if you would like to activate this, simply uncomment the lines in domain_handler.php

//$myDB->save_scripts($current->get_scripts());
//$myDB->save_images($current->get_images());

Additionally, domain_handler.php also includes these functions:

//$current->get_all_table_data();
//$current->table_traverse(tableid, x to the right, y down); //returns contents of the specified cell
//$current->get_tag(specified tag) //returns an array containing all contents of the tag.

Uncomment these functions as well as you see fit, however they currently have no database interaction so you may need to add that if you wish for them to do more that just return data.

4)Run execute.php in the same way as described before. Ex:   C:\xampp\php\php.exe -f "C:\xampp\htdocs\SEO\execute.php" 

5)This will pick up a job out of the database and start scraping it.  While scraping the raw data, this will also parse the raw data and insert it into the database.

6)Be patient, this can take over a day depending on the number of URLs given.



DESCRIPTIONS OF CLASSES:

Master: handles all the dropping of jobs into the database.
Slave:  this class handles the checking of the database by the Slave machines for scraping/parsing
	Slave->doObey() handles scraping
	Slave->doParseRawData handles parsing
Crawler:handles the actual crawling and scraping of a website.  This Class is called by Slave when it is doing raw data scraping.
	This Crawler in turn calls a bunch of other classes that help in the scraping.  Pay attention to the parameter$maxPages which
	is set so that we crawl the same number of pages on all of our pages, preventing data from one website from overwhelming our analysis.
	If you want to turn this off, do it in information.php
Page: 	Every time Crawler encounters a new url, it creates a Page Class.  This Class handles the parsing of the website.  When scraping raw data, the 
	Page Class merely parses the internal URLs off of the page (leaving the more complex parsing for later), but when we are doing parsing, 
	the Page Class handles the complex parsing of English and Chinese words, tags, etc.
Raw_Data:This Class handles important things like getting whoIs information for a domain name and saving the raw data to the database.
ScrapeDB: This class in database.php handles all interactions with the database and is called whenever we want to save anything to the database.









