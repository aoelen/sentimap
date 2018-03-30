<?php
/*require('./../includes/top.php');
require('./../includes/functions.php'); 
require_once('../vendors/phpInsight/autoload.php');
set_time_limit(10000);
$start = microtime(true);

$sets = mysqli_query($db2,'SELECT * FROM sets LIMIT 6500,500');

while ($set = mysqli_fetch_array($sets)) { 
    
    $tweetsSet = mysqli_query($db2, 'SELECT * FROM tweets WHERE set_id = ' . $set['id'] . ' ORDER BY id ASC');
    $tweets = array();
    
    while ($tweet = mysqli_fetch_array($tweetsSet) )  { 
        $tweets[] = $tweet['tweet'];
    }

    $sentiment = new \PHPInsight\Sentiment();
    $pos = 0;
    $neg = 0;
    $neu = 0;
    
    foreach ($tweets as $tweet) {
        $scores = $sentiment->score($tweet);
        $class = $sentiment->categorise($tweet);

        if ($class == "pos"){
            $pos = $pos + 1;
        };
        if ($class == "neu"){
            $neu = $neu + 1;
        };
        if ($class == "neg"){
            $neg = $neg + 1;
        };
    }
    
    mysqli_query($db2, 'INSERT INTO citiesdata2 (name, pos, neg, neu, set_id, date) VALUES ("' . $set['name'] . '", ' . $pos .', ' . $neg .', ' . $neu .', "' . $set['id'] . '", "' . $set['time'] . '")');
    
}
//echo "<BR>:POS" . $pos;
//echo "<BR>:NEG" . $neg;
//echo "<BR>:NUE" . $neu;
echo 'Done, execution time (seconds): ';

$time_elapsed_secs = microtime(true) - $start;
echo $time_elapsed_secs;