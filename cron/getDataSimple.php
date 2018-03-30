<?php 
require('./../includes/top.php');

require_once('../vendors/twitter/twitteroauth/autoload.php');
require_once('../vendors/phpInsight/autoload.php');

use Abraham\TwitterOAuth\TwitterOAuth;

$toa = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);

$querry = "SELECT * FROM  `initial_cities`";
$query = mysqli_query($db2, $querry);
$rows = array();
$array = array();

while($r = mysqli_fetch_assoc($query)) {
    
    $array[] = $r;
};

$last = count($array) - 1;

foreach ($array as $i => $row){
    sleep(0.5);
    $isFirst = ($i == 0);
    $isLast = ($i == $last);
    $pos = 0;
    $neg = 0;
    $neu = 0;
    
    $strings = array();
    echo $row['City'];
    $name = $row['City'];
    $lat = $row['Lat'];
    $lng = $row['Lng'];
    echo "<br>";
    $jsonfile = file_get_contents("http://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lng&units=metric&appid=PRIVATE");
    $jsondata = json_decode($jsonfile);
    $desc = $jsondata->weather[0]->description;
    echo $desc;
    $query = array(
        "q" => '',
        "geocode" => '' . $lat . ',' . $lng . ',25km',
        'count' => 100,
        'lang', 'en'
    );
    
    $results = $toa->get('search/tweets', $query);
    
    foreach ($results->statuses as $result) {
        $result->user->screen_name . ": " . $result->text . "\n";
        $string = $result->text;
        $regex = "@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?).*$)@";
        
        $tweet = preg_replace($regex, ' ', $string);
        $tweet = preg_replace("/RT +@[^ :]+:? /", "", $tweet);
        $strings[] = $tweet;
    }
    
    $sentiment = new \PHPInsight\Sentiment();
    foreach ($strings as $string) {
        
        // calculations:
        $scores = $sentiment->score($string);
        $class = $sentiment->categorise($string);
        
        // output:
        //echo $class;
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
    echo "<br> $pos , $neg, $neu <br><br>";
    
    
    $querry = "INSERT INTO `citiesdata` (`name`, `Lat`, `lng`, `pos`, `neg`, `neu`, `weather`) VALUES ('" . $name . "', " . $lat . ", " .  $lng . "," . $pos . "," . $neg . ", " . $neu . ", '" . $desc . "')";
    $query = mysqli_query($db2, $querry);
}

