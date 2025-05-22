<?php
// db_connect.php

// Database configuration
define('DB_SERVER', 'localhost'); // database host
define('DB_USERNAME', 'root'); // MySQL username
define('DB_PASSWORD', ''); // MySQL password
define('DB_NAME', 'ewaste_connect'); //  database name

/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Set character set to UTF-8 for proper encoding
mysqli_set_charset($link, "utf8mb4");
