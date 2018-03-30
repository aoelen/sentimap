<?php

require(dirname(__FILE__) . '/../config.php');

// Turn on error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connect to database via PDO
$db = new PDO('mysql:host=' . DB_HOST .';dbname=' . DB_DATABASE . ';charset=utf8mb4', DB_USER, DB_PASSWORD);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Connect to database via mysqli
$db2 = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) ;
$selected = mysqli_select_db($db2, DB_DATABASE);

