The code here is distributable, so just download the .zip

You can run this on any computer that has php already installed.  All you need to do is go to command line, cd to the directory
that contains the the files, and execute the function execute.php.  This function creates a Slave class that pings a database
for urls to scrape.  In order to connect to specify the database that you would like to connect to, specify this in 
information.php.  

Linux is currently installed on PC-16, and PC-14 in the conference room.  In order for them to be able to run the .php files, 
we will need to also install something like LAMP on them.  Once that is installed, the "Slave" computers need simply to
execute "execute.php" while the "Master" computer executes "master.php".  

AGAIN, SPECIFY THE DATABASE TO CONNECT TO IN "information.php".  ALSO, SLAVE MACHINES NEED SPECIFY machine_id IN "information.php"

