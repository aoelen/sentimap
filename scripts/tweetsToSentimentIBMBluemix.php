<?php
require('./../includes/top.php');
require('./../includes/functions.php'); 
require_once('../vendors/phpInsight/autoload.php');
set_time_limit(100000000);
$start = microtime(true);

$sets = mysqli_query($db2,'SELECT * FROM sets');

while ($set = mysqli_fetch_array($sets)) { 
    
    $tweetsSet = mysqli_query($db2, 'SELECT * FROM tweets WHERE set_id = ' . $set['id'] . ' ORDER BY id ASC');
    $tweets = '';
    
    while ($tweet = mysqli_fetch_array($tweetsSet) )  { 
        $tweets = $tweets . ' ' . $tweet['tweet'];
    }  
    
    $ch = curl_init("https://gateway.watsonplatform.net/natural-language-understanding/api/v1/analyze");
    $username = 'PRIVATE';
    $password = 'PRIVATE';
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode(array('version' => '2017-02-27', 'text' => $tweets, 'features' => array('sentiment' => array('document' => true)))));
    curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: application/json'                                                                              
    )); 
    
    
    $result = curl_exec($ch);
    
    $result = json_decode($result, true);
    
    $label = $result['sentiment']['document']['label'];
    $score = $result['sentiment']['document']['score'];
    
    mysqli_query($db2, 'INSERT INTO citiesdata3 (name, set_id, date, score, label) VALUES ("' . $set['name'] . '", "' . $set['id'] . '", "' . $set['time'] . '", "' . $score . '", "' . $label . '")');
    
}

echo 'Done, execution time (seconds): ';

$time_elapsed_secs = microtime(true) - $start;
echo $time_elapsed_secs;