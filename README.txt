The Scraper consists of main php files: domain_handler.php, page_scrape.php, database.php, and information.php

The scraper contains the classes Raw_Data, Page, Script, ScrapeDB, and Crawler.  The Crawler controls these other
classes.  


Currently I have been scraping www.frontcoding.com to test everything out.  At the top of domain_handler.php, you can go in 
and change this to any other domain name that you like.  If you go into information.php, you can change the server name, 
database name, username, and password that you would like to use on your system.  If you open up domain_handler in your
localhost (i.e. localhost/domain_handler.php) it should run and fill out the database.

***NOTE: The relationships table is still empty and most of the whois data is still empty.

I have also included a SQL file called seo.sql which should create the schema for your database.  This was exported from 
phpmyadmin.

If you need any more information, email me at nick@frontcoding.com.