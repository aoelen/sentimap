<?php
require('./../includes/top.php');
require('./../includes/functions.php'); 
require_once('../vendors/phpInsight/autoload.php');
set_time_limit(10000);
$start = microtime(true);


$cities = array(
    "New York City; New",
    "Los Angeles; Califo",
    "Chicago; Illinois",
    "Houston; Texas",
    "Philadelphia; Pennsy",
    "Phoenix; Arizona",
    "San Antonio; Texas",
    "San Diego; Californi",
    "Dallas; Texas",
    "San Jose; California",
    "Austin; Texas",
    "Jacksonville; Florid",
    "Indianapolis; Indian",
    "San Francisco; Calif",
    "Columbus; Ohio",
    "Fort Worth; Texas",
    "Charlotte; North Car",
    "Detroit; Michigan",
    "El Paso; Texas",
    "Memphis; Tennessee"
);

foreach($cities as $city) {
    for ($i = 1;$i < 13;$i++) {
        $e = mysqli_query($db2, 'INSERT INTO climate_data_cities (city,month_number) VALUES ("' . $city. '", ' . $i .')');
    }
}