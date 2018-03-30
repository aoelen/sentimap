<?php 
/**
 * Weather class 
 *
 * This file provides a PHP API class for openweathermap.org. The class is designed
 * to automatically save to data using a MySQL PDO instance.
 *
 * @author Sentimap.org
 * @version 1.2
 * 
 */

class Weather {
	/**
     * Instance latitude
     *
     * @var float
     */
	public $lat;
	/**
     * Instance longitude
     *
     * @var float
     */
	public $lng;
	
	/**
     * Instance set id (a weather report is related to a certain query id)
     *
     * @var float
     */
	public $setId;
	
	
	/**
     * API key of openweathermap.org
     *
     * @var string
     */
	const WEATHER_REPORT_API_KEY = 'PRIVATE';
	
	/**
     * URL of weather report API
     *
     * @var string
     */
	const WEATHER_REPORT_API_URL = 'http://api.openweathermap.org/data/2.5/weather?appid=';
	
	/**
     * Keys => Data that should be stored in the database 
     * Values => Location of this data in the response JSON of openweathermap.org
     *
     * @var array
     */
	const WEATHER_REPORT_DATA_STRUCTURE = [
		'temp' => '[main][temp]',
		'pressure' => '[main][pressure]',
		'humidity' => '[main][humidity]',
		'visibility' => '[visibility]',
		'wind_speed' => '[wind][speed]',
		'clouds' => '[clouds][all]',
		'weather_main' => '[weather][0][main]',
		'weather_icon' => '[weather][0][icon]',
		'lat' => '[coord][lat]',
		'lng' => '[coord][lon]',
		'city_name' => '[name]'
	];
	
	private $weatherReportCache;
	
	/**
     * Constructor 
     *
     * @param required float $lat - latitude
     * @param required float $lng - longitude
     * @return void
     */
    public function __construct($lat = null, $lng = null, $setId = null) {
		$this->lat = $lat;
		$this->lng = $lng;
		$this->setId = $setId;
    }
	
	/**
     * Gets the currect weather report of the current instance location 
     *
     * @return array with weather info
     */
	public function getCurrentWeather() {
		$weather = $this->APIRequest([
			'lat' => $this->lat,
			'lon' => $this->lng,
			'units' => 'metric'
		]);
		
		$weather = $this->getRelevantData($weather);
		$this->weatherReportCache = $weather;
		
		return $weather;
	}
	
	/**
	 * Saves the current weather report to the database
	 * It is required to use getCurrentWeather() beforehand
	 *
	 * @param required float $lng - longitude
	 * @return bool
	 */
	public function saveWeatherReport($db) {
		if (empty($this->weatherReportCache)) {
			die('ERROR: execute "getCurrentWeather" before saving the weather report');
		}
		$report = $this->weatherReportCache;
		
		$saveFields = implode(', ', array_keys($report));
		$placeholder = str_repeat("?,", count($report));
		
		$sql = 'INSERT INTO weather_data (' . $saveFields . ', created_at, set_id) VALUES (' . $placeholder . ' NOW(), ' . $this->setId . ')';
		$stmt = $db->prepare($sql);
		
		return $stmt->execute(array_values($report));
	}
	
	/**
	 * Gets the weather report for an array of city coordinates,
	 * and save them all to the database
	 *
	 * This function is useful for a cronjob that runs everyday 
	 * Should be called statically, e.g.: $weather::saveManyWeatherReports(array, $db);
	 *
	 * @param required array $cities   
	 * @param required PDO instance $db 
	 * @return void
	 */
	public static function saveManyWeatherReports($cities, $db) {
		foreach ($cities as $city) {
			$lat = $city[0];
			$lng = $city[1];
			
			$weather = new Weather($lat, $lng);

			$currentWeatherReport = $weather->getCurrentWeather();
			$weather->saveWeatherReport($db);
		}
	}
	
	public static function getWeatherScoreSimple($label) {
		$weather_score = 0;
		
		switch($label) {
			case 'clear sky':
				$score = 10;
			break;
			case 'few clouds':
				$score = 9;
			break;
			case 'scattered clouds':
				$score = 8;
			break;
			case 'broken clouds':
				$score = 7;
			break;
			case 'overcast clouds':
				$score = 6;
			break;
			
			case 'light rain':
			case 'light intensity drizzle':
				$score = 5;
			break;
			
			case 'moderate rain':
			case 'rain':
				$score = 4;
			break;
			
			
			case 'haze':
				$score = 3;
			break;
			case 'mist':
				$score = 3;
			break;
			case 'fog':
				$score = 3;
			break;
			
			case 'light snow':
				$score = 2;
			break;
			case 'snow':
				$score = 2;
			break;
			
			case 'heavy intensity rain':
			case 'shower rain':
				$score = 1;
			break;
			
			case 'thunderstorm':
				$score = 1;
			break;
			
		
			default:
				$score = 0;
			break;
		}
		
		return $score;
	}
	
	public static function getWeatherScoreAdvanced($weatherLabel, $cityName, $month, $hour, $temp, $wind) {
		$score = Weather::getWeatherScoreSimple($weatherLabel) - 1;
		$tempClimate = Weather::getClimateTemperature($cityName, $month);
		$windChill = Weather::windChillTemperature($temp, $wind);
		
		$coldestHour = 6;
		$hottestHour = 15;
		
		$minTemp = $tempClimate['temp_min'];
		$maxTemp = $tempClimate['temp_max'];
		if ($hour > $coldestHour && $hour < $hottestHour) {
			$a = ($maxTemp - $minTemp) / ($hottestHour - $coldestHour);
			
			$b = $minTemp - (($maxTemp - $minTemp) / ($hottestHour - $coldestHour)) * $coldestHour;
			
			$predictedTemp = $a * $hour + $b;
		} else {
			$coldestHourAdapted = $coldestHour + 24;
			
			if ($hour >= 0 && $hour <= $coldestHour) {
				$hour2 = $hour + 24;
			} else {
				$hour2 = $hour;
			}
			
			$a = ($maxTemp - $minTemp) / ($hottestHour - $coldestHourAdapted);
			
			$b = $minTemp - (($maxTemp - $minTemp) / ($hottestHour - $coldestHourAdapted)) * $coldestHourAdapted;
			
			$predictedTemp = $a * $hour2 + $b;
		}

		if ($windChill > $predictedTemp) {
			$diff = $predictedTemp - $windChill;
			$positiveScore = 2.5 * (1 - pow(2, (-1 * 0.01 * pow($diff, 2))));
			$score = $score + $positiveScore;
		} else {
			$diff = $predictedTemp - $windChill;
			$negativeScore = 5 * (1 - pow(2, (-1 * 0.01 * pow($diff, 2))));
			$score = $score - $negativeScore;
		}
		
		if ($score < 0) {
			$score = 0;
		}
		if ($score > 10) {
			$score = 10;
		}
		
		return $score;
	}
	
	/**
	 * Private functions
	 */
	private static function getClimateTemperature($city, $month) {
		global $db;
		
		$sql = 'SELECT temp_min, temp_max FROM climate_data_cities WHERE city = :city AND month_number = :month';
		$stmt = $db->prepare($sql);
		$stmt->execute(array(':city' => $city, ':month' => $month));
		$climate = $stmt->fetch(PDO::FETCH_ASSOC);
		 		
 		return $climate;
 	}
 	
	private static function windChillTemperature($temp, $wind) {
		$windChill = 13.12 + (0.6215 * $temp) - (13.96 * pow($wind,0.16)) + (0.4867 * $temp * pow($wind,0.16));
		
		return $windChill;
	}
	
	private function APIEndpoint() {
		return self::WEATHER_REPORT_API_URL . self::WEATHER_REPORT_API_KEY;
	}
	
	private function APIRequest($settings) {
		$params = $this->setQueryStringParams($settings);
		
		$url = $this->APIEndpoint() . $params;
		$request = file_get_contents($url);
		
		return json_decode($request,true);
	}
	
	private function setQueryStringParams($settings) {
		$params = '';
		foreach ($settings as $key=>$value) {
			$params .= '&' . $key . '=' . $value;
		}
		return $params;
	}
	
	private function getRelevantData($rawWeather) {
		$data = [];
		
		foreach (self::WEATHER_REPORT_DATA_STRUCTURE as $item=>$location) {
			
			$data[$item] = $this->getArrayKey($location, $rawWeather);
		}
		$data['raw_json'] = json_encode($rawWeather);
		
		return $data;
	}
	
	# Function to convert String to array keys, from: https://stackoverflow.com/a/7003702
	# Usage: $this->getArrayKey('[key][deeperKey]', $array);
	private function getArrayKey($string, $vars) {
	    $keys = explode('][', substr($string, 1, -1 ));
		
	    foreach( $keys as $key ) {
	        $vars = $vars[$key];
	    }
		
	    return $vars;
	}
}


// Just for the demo, turn error reporting on
//error_reporting(E_ALL);
// The class needs a PDO connection, it should be passed as argument for 
// functions that are interacting with the database
//$db = new PDO('mysql:host=localhost;dbname=sentimap', 'root', '');
//$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


/*

###############################################
// Example 1: get weather report for a specific city (in this example Amsterdam)
###############################################

// Create new Weather object, using: new Weather(latitude, longitude);
$weather = new Weather(52.36,4.89);

// Get the currect Weather report, returns an array with useful info 
$currentWeatherReport = $weather->getCurrentWeather();

// Preview the fetched data  
echo '<pre>';print_r($currentWeatherReport);echo '</pre>';

// Save the weather report to the database
$weather->saveWeatherReport($db);




###############################################
// Example 2: get weather report from array of city coordinates, and save it all to the database
// Can be useful for the cronjob
###############################################

// Array with coordinates, add cities as [latitude, longitude],
$cities = [
	[52.36,4.89], //amsterdam
	[40.70, -74.00], // new york
	[41.877, -87.635] // chicago
];

// Get the weather reports and save to database (return true if everything went fine)
Weather::saveManyWeatherReports($cities, $db);*/