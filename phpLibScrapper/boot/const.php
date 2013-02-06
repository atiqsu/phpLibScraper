<?php

    //
    //   Constants (development environment)
    //

    define("COOKIE", 'txt/cookie.txt');                   // Cookie file
    define("DEBUG_CURL", FALSE);                          // View debug 
    define("CURL_TIMEOUT", 25);

    ini_set ('error_reporting', E_ALL);                   // View all errors 
    ini_set ('max_execution_time', 0);                    // Maxim execution time (0 = no limit)

    define("MYSQL_ADDRESS", "localhost");                 // MySQL: Define the IP address of your MySQL Server
    define("MYSQL_USERNAME", "root");                     // MySQL: Define your MySQL user name
    define("MYSQL_PASSWORD", "mypassword");               // MySQL: Define your MySQL password
    define("DATABASE", "pscraper");                       // MySQL: Define your default database
    define("SUCCESS", true);                              // MySQL: Successful operation flag
    define("FAILURE", false);                             // MySQL: Failed operation flag

?>
