<?php 
require('./../includes/top.php');

include('./../classes/weather.class.php');

require_once('../vendors/twitter/twitteroauth/autoload.php');

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

$sql = 'SELECT twitter_id FROM tweets ORDER BY id DESC LIMIT 1';
$stmt = $db->prepare($sql);
$stmt->execute();
$lastTweet = $stmt->fetch(PDO::FETCH_ASSOC);
$lastTweetId = $lastTweet['twitter_id'];


foreach ($array as $i => $row) {
	sleep(0.5);
	echo 'Fetching: ' . $row['City'];
   
	
	$name = $row['City'];
    $lat = $row['Lat'];
    $lng = $row['Lng'];
    
	$sql = 'INSERT INTO sets (name,lat,lng) VALUES (?,?,?)';
	$stmt = $db->prepare($sql);
	
	$stmt->execute(array($name,$lat,$lng));
	$setId = $db->lastInsertId();
	
       
	$strings = array();
   
    $weather = new Weather($lat, $lng, $setId);
	
	// Get the currect Weather report, returns an array with useful info 
	$currentWeatherReport = $weather->getCurrentWeather();
		
	// Save the weather report to the database
	$weather->saveWeatherReport($db);
    
	$query = array(
		'q' => '',
		'geocode' => $lat . ',' . $lng . ',40km',
		'count' => 100,
		'lang' => 'en',
		'result_type' => 'recent',
		'since_id' => $lastTweetId
	);
		
	$results = $toa->get('search/tweets', $query);
 
	foreach ($results->statuses as $result) {
	 	$result->user->screen_name . ": " . $result->text . "\n";
	 	$string = $result->text;
	 	$regex = "@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?).*$)@";
		
	 	$tweet = preg_replace($regex, ' ', $string);
	 	$tweet = preg_replace("/RT +@[^ :]+:? /", "", $tweet);
	 	echo $tweet; echo '<br>';;
	 	$sql = 'INSERT INTO tweets (tweet, set_id, twitter_id) VALUES (?, ?, ?)';
	 	$stmt = $db->prepare($sql);
		$stmt->execute(array($tweet, $setId, intval($result->id)));

	}
	echo '. Done. <br>';
}

?>