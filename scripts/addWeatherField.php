<?php
require('./../includes/top.php');
require('./../includes/functions.php'); 
require_once('../vendors/phpInsight/autoload.php');
set_time_limit(10000);
$start = microtime(true);


$data = mysqli_query($db2, 'SELECT id,raw_json FROM weather_data');
$tweets = array();

while ($weather = mysqli_fetch_array($data) )  { 
    
    $parse = json_decode($weather['raw_json'], true);
    $desc = $parse['weather'][0]['description'];
    
    mysqli_query($db2, 'UPDATE weather_data SET weather_description = "' . $desc . '" WHERE id =' . $weather['id']);
}

/*
foreach($cities as $city) {
    for ($i = 1;$i < 13;$i++) {
        $e = mysqli_query($db2, 'INSERT INTO climate_data_cities (city,month_number) VALUES ("' . $city. '", ' . $i .')');
    }
}*/