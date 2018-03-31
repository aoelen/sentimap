# Readme SentiMap.org

![Logo](img/logo_small.png)

> **The Social Web group 6**
> - Allard Oelen
> - Gert Boer
> - Hameedat Omoine
> - Mathijs Oggel
> - Nip van Wees

This readme file gives a detailed look on the role of specific files and functions in the SentiMap application. More information about this research can be found in the project report *"Report-group6-SentiMap.pdf"*. The full working version of the application can be found via the project website of SentiMap: [www.sentimap.org](http://www.sentimap.org). The source code in this repository is the same as runs on the project website.

> **System requirements:**

> - PHP 5.6 or higher
> - - PHP PDO database extension  
> - MySQL 5.6 or higher (for database structure, see: `database.sql`)
> - Cronjobs


## 1. Data collection (cronjobs)
Two different scripts have been used to collect data. They run as a cronjob either every 15 or every 30 minutes. 
### 1.1 Simple data collection
The first script is located at `cron/getDataSimple.php`. This script runs every 30 minutes. The simple script does a lot of data processing already, before saving it to the database. For every of the 20 predefined cities, 100 tweets are collected. Those tweets are put into the phpInsight class, and a sentiment score comes out of it. This score is: positive, negative or neutral. The total amount of positive, negative and neutral tweets is stored in the database. Additionally, the weather description from the weather report, of that city is stored. Only the sentiment data of the tweets are stored, the tweets itself are discarded. 
### 1.2 Extended data collection  
The extended data collection script, which can be found at `cron/getDataExtended.php`, runs every 15 minutes. In contrast to the simple data collection script, no data analysis is conducted in this script. The raw tweets are stored for each city. Afterwards, the full weather report is stored as well (this in contrast to the previous script, where only the weather_description parameter was stored). The extended data collection script allows us to do the data analysis at a later stage. It is now possible to change, for example, the sentiment analysis script, or the weather scoring algorithm during the data analysis (since all the original data is stored). 

## 2. Features and functionalities 

### 2.1 Collect Twitter tweets
Using the `TwitterOAuth` tweets are collected from Twitter. The tweets are selected based on the latitude and longitude of 20 predefined cities, with a radius of 40 kilometers around the city center. Other search requirements are that only the most recent tweets are collected and that tweets have to be in English. In total 100 tweets per query are returned
### 2.2 Sentiment analysis
The sentiment analysis code is used both in the cronjob and in the external scripts, (that are only executed once) It is used to determine the sentiment of (individual) tweets.
#### 2.2.1 phpInsight
Each tweet in analyzed by phpInsight to discover whether the tweet is positive, negative or neutral. The script is very straightforward, and only looks for certain words to occur, to determine the sentiment. 
#### 2.2.2 IBM Watson 
Because phpInsight turned out to be inaccurate in determining sentiment, the IBM Watson Natural Language Understanding has been used. This is used with the tweets collected by the *Extended data collection* cronjob. 

The script that determines the tweet sentiment can be found in `scripts/tweetsToSentimentIBMBluemix.php`. Here a post request is made using *curl* to the Watson platform. Because the total amount of tweets was more than 600.000, the tweets of one tweet set are combined. The resulting text of combined tweets is sent to Bluemix. In the end, more than 6000 requests were made to Bluemix and the sentiment results were saved to our database. In total this operation took around 5 hours to run. 

### 2.3 Weather scoring algorithm
The weather scoring algorithm converts a weather reports, from Openweatherdata.org, to a weather score. This score in ranging from 0 to 10, where 10 is the best weather and 0 the worst weather. Because the weather score has a significant impact on our results, we have created a special class for all weather related function. This class can be found in the file `classes/weather.class.php`. Some of the functions inside this class are commented with extra information.
#### 2.3.1 Simple weather scoring
The simple weather score can be calculated using the following (static) function: `getWeatherScoreSimple($label)`. The label argument is the textual weather description. This description is converted to a score, which is returned by the function.

#### 2.3.2 Advanced weather scoring
The advanced weather scoring function is building on top of the simple function. The function can be called like this: `getWeatherScoreAdvanced($weatherLabel, $cityName, $month, $hour, $temp, $wind)`. Firstly the simple weather score is calculated (which was based on the $weatherLabel). The advanced weather scoring algorithm takes temperature and windiness also into account. This is done by comparing the wind chill to the average climate temperature. 

In order to do this, a method has been created to estimate the temperature at a certain point hours of the day, in a certain month, for a certain city. For this, climate data has been collected, and saved in the database table `climate_data_cities`. Here for each month and each city, the minimum and maximum temperature are stored. In order to simplify the temperature estimatin, a linear relation is assumed between the temperature and time. Additionally we assume that the coldest hour on a day is at 6 a.m., and the hottest at 15 p.m. The function `getClimateTemperature($city, $month)` returns the minimum and maximum temperature.  In the end, the average temperature is calculated. And this temperature is compared by the wind chill temperature. This temperature is calculated via the JAG/TI method, and looks like this:

```13.12 + (0.6215 * $temp) - (13.96 * pow($wind,0.16)) + (0.4867 * $temp * pow($wind,0.16));```

To find out more of the weather scoring algorithm, please take a look at the class. Most functions are commented, and they provide a clear overview of how the weather score is calculated. 


## 3. External libraries and APIs 
- [phpInsight](https://github.com/JWHennessey/phpInsight) 
- [Openweatherdata.org ](https://openweathermap.org/)
- [Google Maps API](https://developers.google.com/maps/)
- [IBM Watson Natural Language Understanding ](https://www.ibm.com/watson/services/natural-language-understanding/) 
- [TwitterOAuth](https://github.com/abraham/twitteroauth)




## 4. File structure 

```
SentiMap
└───ajax
│   │    getCities.php - returns data of predefined cities for the current data tab
│   │    getCityData.php - for live querying of cities, returns tweet sentiment and current weather 
|    
└───classes
│   │   weather.class.php - contains a class for getting weather data and calculating weather scores
|   
└───cron
│   │   getDataExtended.php - collects raw tweet data, and weather reports
│   │   getDataExtended.php - collects tweet data, calculates and stores sentiment data
|   
└───css
│   │   style.css - style sheet of application
|   
└───img
│   │   .png files
│   │   ...
|   
└───includes
|   │   functions.php - legacy file for old weather scores
|   │   top.php - should be included in every file, makes database connection
|
└───scripts
|   │   addWeatherField.php - script for adding columns for weather report from raw JSON
|   │   tweetsToSentimentIBMBluemix.php - script for using Watson Natural Language Understanding to look at tweet sentiment
|   │   tweetsToSentimentPhpInsight.php - script for using phpInsight to look at tweet sentiment
|   
└───templates
│   │   footer.php - footer for each HTML file
│   │   header.php - header (including menu, and other template data) for each HTML file
|   
└───vendors
│   │   contains multiple PHP libraries from third parties 
│   │   ...
|  
|   config.php - set database info, and API keys
|   database-with-data.sql - database contains all data
|   database.sql - database structure
|   historic.php - shows historic data, for 3 experiment types
|   index.php - file for live querying the map
|   projectinfo.php - shows scatter plots for each experiment
|   README.md - this file
```