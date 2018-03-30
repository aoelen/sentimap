<?php

// Convert a weather description from https://openweathermap.org/weather-conditions to a scaled score

function getWeatherScore($label) {
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