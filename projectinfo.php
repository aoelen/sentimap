<?php
require('./includes/top.php');
$page = 'projectinfo';
include ('./classes/weather.class.php'); 

if (isset($_GET['page'])) {
    $subPage = $_GET['page'];
} else {
    $subPage = 1;
}

$city = 'New York City; New'; 
if(isset($_POST['city'])){  
    $city = $_POST['city'];
}

$weather = new Weather;
$array = array();

if ($subPage == 1) {
    $selectCity = $city != 'All' ? 'AND name LIKE "%'.$city.'%"' : '';
    
    $result = mysqli_query($db2,'SELECT date, pos, neg, weather, pos/neg AS sentiment FROM citiesdata WHERE weather != "" AND pos != "" ' . $selectCity . 'ORDER BY sentiment ASC'); //WHERE 
    while ($row = mysqli_fetch_array($result) )  { 
        $array[] = $row;
    }
} else if ($subPage == 2) {
    $selectCity = $city != 'All' ? 'AND name LIKE "%'.$city.'%"' : '';
    
    $result = mysqli_query($db2,'
    SELECT citiesdata2.pos/citiesdata2.neg AS sentiment, citiesdata2.name, DATE_FORMAT(citiesdata2.date,"%d") AS day, DATE_FORMAT(citiesdata2.date,"%m") AS month, DATE_FORMAT(citiesdata2.date,"%H") AS hour, citiesdata2.pos, citiesdata2.neg, citiesdata2.set_id, weather_data.temp, weather_data.wind_speed, weather_data.weather_description FROM citiesdata2
    LEFT JOIN weather_data ON citiesdata2.set_id = weather_data.set_id
    WHERE citiesdata2.pos != "" ' . $selectCity . ' ORDER BY sentiment ASC');
    
    while ($row = mysqli_fetch_array($result) )  { 
        $array[] = $row;
    }
    
} else if ($subPage == 3) {    
    $selectCity = $city != 'All' ? 'WHERE name LIKE "%'.$city.'%"' : '';
    
    $result = mysqli_query($db2,'
    SELECT citiesdata3.score AS sentiment, citiesdata3.name, DATE_FORMAT(citiesdata3.date,"%d") AS day, DATE_FORMAT(citiesdata3.date,"%m") AS month, DATE_FORMAT(citiesdata3.date,"%H") AS hour, citiesdata3.set_id, weather_data.temp, weather_data.wind_speed, weather_data.weather_description FROM citiesdata3
    LEFT JOIN weather_data ON citiesdata3.set_id = weather_data.set_id
    ' . $selectCity . ' ORDER BY sentiment ASC');
    
    while ($row = mysqli_fetch_array($result) )  { 
        $array[] = $row;
    }
}

?>
<?php include('templates/header.php'); ?>

<script type="text/javascript">
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);
function drawChart() {
    var data = google.visualization.arrayToDataTable([
        ['', 'Tweet'],
        <?php
        if ($subPage == 1) {
            foreach ( $array as $i => $row){
                $weather_label = $row['weather'];
                $weather_score = $weather::getWeatherScoreSimple($weather_label);
                
                if ($weather_score == 0) {
                    continue;
                }
                
                echo ("[". $row['sentiment'] . "," .  $weather_score . "],");
            }
        } else if ($subPage == 2) {
            
            foreach ( $array as $i => $row){
                $weather_score = $weather::getWeatherScoreAdvanced($row['weather_description'], $row['name'],$row['month'], $row['hour'], $row['temp'], $row['wind_speed']);
                
                if ($weather_score == 0) {
                    continue;
                }
                
                echo ("[". $row['sentiment'] . "," .  $weather_score . "],");
            }
        } else if ($subPage == 3) {
            
            foreach ( $array as $i => $row){
                $weather_score = $weather::getWeatherScoreAdvanced($row['weather_description'], $row['name'],$row['month'], $row['hour'], $row['temp'], $row['wind_speed']);
                
                if ($weather_score == 0) {
                    continue;
                }
                
                echo ("[". $row['sentiment'] . "," .  $weather_score . "],");
            }
        }
        ?>
    ]);
    
    var options = {
        title: '<?php echo($city);?>',
        hAxis: {title: 'Sentiment'},
        vAxis: {title: 'Weather'},
        legend: { position: 'bottom' },
        trendlines: { 0: {
            color: 'green',
            lineWidth: 2,
            showR2: true,
            visibleInLegend: true,
            
            //opacity: 0.2
        } }    // Draw a trendline for data series 0.
    };
    
    var chart = new google.visualization.ScatterChart(document.getElementById('curve_chart'));
    
    chart.draw(data, options);
}
</script>


<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCeTHUubh5DgyGrk01N9rCoQNBYaNvtSAg&callback=initMap&libraries=places&callback=initAutocomplete">
</script>

<div class="container">
    <div class="submenu">
        <a href="historic.php?page=1" <?php if($subPage == 1){ echo 'class="selected"'; } ?>>Weather: simple, Sentiment: phpInsight</a>
        <a href="projectinfo.php?page=2" <?php if($subPage == 2){ echo 'class="selected"'; } ?>>Weather: advanced, Sentiment: phpInsight</a>
        <a href="projectinfo.php?page=3" <?php if($subPage == 3){ echo 'class="selected"'; } ?>>Weather: advanced, Sentiment: IBM Bluemix</a>
    </div>
    <div class="clear"></div>
    <center> <form method="post" accept="#"><br>
        
        Select city: <select name="city" class="select" onchange="this.form.submit()">
            <option value="New York City; New" <?php if($city == 'New York City; New') { echo 'selected="selected"'; } ?>>New York City; New York</option>
            <option value="Los Angeles; Califo" <?php if($city == 'Los Angeles; Califo') { echo 'selected="selected"'; } ?>> Los Angeles; California</option>
            <option value="Chicago; Illinois" <?php if($city == 'Chicago; Illinois') { echo 'selected="selected"'; } ?>>Chicago; Illinois</option>
            <option value="Houston; Texas" <?php if($city == 'Houston; Texas') { echo 'selected="selected"'; } ?>>Houston; Texas</option>
            <option value="Philadelphia; Pennsy" <?php if($city == 'Philadelphia; Pennsy') { echo 'selected="selected"'; } ?>>Philadelphia; Pennsylvania</option>
            <option value="Phoenix; Arizona" <?php if($city == 'Phoenix; Arizona') { echo 'selected="selected"'; } ?>>Phoenix; Arizona</option>
            <option value="San Antonio; Texas" <?php if($city == 'San Antonio; Texas') { echo 'selected="selected"'; } ?>>San Antonio; Texas</option>
            <option value="San Diego; Californi" <?php if($city == 'San Diego; Californi') { echo 'selected="selected"'; } ?>>San Diego; California</option>
            <option value="Dallas; Texas" <?php if($city == 'Dallas; Texas') { echo 'selected="selected"'; } ?>>Dallas; Texas</option>
            <option value="San Jose; California" <?php if($city == 'San Jose; California') { echo 'selected="selected"'; } ?>>San Jose; California</option>
            <option value="Austin; Texas" <?php if($city == 'Austin; Texas') { echo 'selected="selected"'; } ?>>Austin; Texas</option>
            <option value="Jacksonville; Florid" <?php if($city == 'Jacksonville; Florid') { echo 'selected="selected"'; } ?>>Jacksonville; Floridira</option>
            <option value="Indianapolis; Indian" <?php if($city == 'Indianapolis; Indian') { echo 'selected="selected"'; } ?>>Indianapolis; Indiana</option>
            <option value="San Francisco; Calif" <?php if($city == 'San Francisco; Calif') { echo 'selected="selected"'; } ?>>San Francisco; California</option>
            <option value="Columbus; Ohio" <?php if($city == 'Columbus; Ohio') { echo 'selected="selected"'; } ?>>Columbus; Ohio</option>
            <option value="Fort Worth; Texas" <?php if($city == 'Fort Worth; Texas') { echo 'selected="selected"'; } ?>>Fort Worth; Texas</option>
            <option value="Charlotte; North Car" <?php if($city == 'Charlotte; North Car') { echo 'selected="selected"'; } ?>>Charlotte; North Carolina</option>
            <option value="Detroit; Michigan" <?php if($city == 'Detroit; Michigan') { echo 'selected="selected"'; } ?>>Detroit; Michigan</option>
            <option value="El Paso; Texas" <?php if($city == 'El Paso; Texas') { echo 'selected="selected"'; } ?>>El Paso; Texas</option>
            <option value="Memphis; Tennessee" <?php if($city == 'Memphis; Tennessee') { echo 'selected="selected"'; } ?>>Memphis; Tennessee</option>
            <option value="All" <?php if($city == 'All') { echo 'selected="selected"'; } ?>>All cities (warning: slow query)</option>
        </select>
        
    </form></center>
    <div id="curve_chart" style="width: 80%; margin-left: 10%; height: 50vh;"></div>
    
</div>
<?php include('templates/footer.php'); ?>