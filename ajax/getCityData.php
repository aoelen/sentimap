<?php 

require('./../includes/top.php');


require_once('../vendors/twitter/twitteroauth/autoload.php');
require_once('../vendors/phpInsight/autoload.php');
use Abraham\TwitterOAuth\TwitterOAuth;

$toa = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
$lng = $_GET['lng'];
$lat = $_GET['lat'];
$name = $_GET['name'];

$strings = array();

$pos = 0;
$neu = 0;
$neg = 0;

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

$jsonfile = file_get_contents("http://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lng&units=metric&appid=cce933c502efddadce25d1e70a3d429e");
$jsondata = json_decode($jsonfile);
$temp = $jsondata->main->temp;
$pressure = $jsondata->main->pressure;
$mintemp = $jsondata->main->temp_min;
$maxtemp = $jsondata->main->temp_max;
$wind = $jsondata->wind->speed;
$humidity = $jsondata->main->humidity;
$desc = $jsondata->weather[0]->description;
$maind = $jsondata->weather[0]->main;


$querry = "INSERT INTO `citiesdata` (`name`, `Lat`, `lng`, `pos`, `neg`, `neu`, `weather`) VALUES ('" . $name . "', " . $lat . ", " .  $lng . "," . $pos . "," . $neg . ", " . $neu . ", '" . $desc . "')";
$query = mysqli_query($db2, $querry);

echo "$pos ; $neg ; $neu ; $desc";


?>