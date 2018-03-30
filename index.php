<?php
require('./includes/top.php');
$page = 'index';
?>
<?php

$city = 'New York City; New'; 
if(isset($_POST['city'])){  
    $city = $_POST['city'];
}


$result = mysqli_query($db2,'SELECT date, pos, neg, weather FROM citiesdata WHERE name LIKE "%'.$city.'%"');
$array = array();
while ($row = mysqli_fetch_array($result) )  { 
    $array[] = $row;
    //echo($row['weather'].'<br>');
}

?>

<?php include('templates/header.php'); ?>

<input id="pac-input" class="controls" type="text" placeholder="Search for a specific city..." style="margin-top:10px; padding:5px; border:1px solid grey;">
<div id="map" style="width: 78%; height: 75%; margin-left: 10.9%;"></div>
<script type="text/javascript">
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);
function drawChart() {
    var data = google.visualization.arrayToDataTable([
        ['Week', 'Tweet sentiment', 'Weather score'],
        <?php
        foreach ( $array as $i => $row){
            $weather_label = $row['weather'];
            $weather_score = 0;
            if($weather_label == 'clear sky'){$weather_score = 9;}
            if($weather_label == 'few clouds'){$weather_score = 8;}
            if($weather_label == 'scattered clouds'){$weather_score = 7;}
            if($weather_label == 'broken clouds'){$weather_score = 6;}
            if($weather_label == 'shower rain'){$weather_score = 5;}
            if($weather_label == 'rain'){$weather_score = 4;}
            if($weather_label == 'thunderstorm'){$weather_score = 3;}
            if($weather_label == 'snow'){$weather_score = 2;}
            if($weather_label == 'mist'){$weather_score = 0;}
            echo ("[".$i.", " . $row['pos']/$row['neg'] . "," .  $weather_score . "],");
        }
        ?>
    ]);
    
    var options = {
        title: '<?php echo($city);?>',
        
        legend: { position: 'bottom' }
    };
    
    var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
    
    chart.draw(data, options);
}
</script>
<script>

var markers = [];
var map;

function initAutocomplete() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 41.025874, lng: -100.658379},
        zoom: 4,
        styles:     [
            {
                "elementType": "labels",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "administrative",
                "elementType": "geometry",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "administrative.land_parcel",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "administrative.neighborhood",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "poi",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "road",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "labels.icon",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "transit",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            }
        ]    
    });
    
    $.ajax({ url: 'ajax/getCities.php',
    type: 'get',
    success: function(output) {
        var myObj = JSON.parse(output);
        console.log(myObj[0].Lat);
        console.log(myObj[0].lng);
        
        var contentString = '<div id="content">'+
        
        '<div id="siteNotice">'+
        '</div>'+
        '<h3 id="firstHeading" class="firstHeading">Sentiment statistics <br> <h5>' + myObj[0].name + '</h5></h3>'+
        '<div id="bodyContent">'+
        '<p> <hr><b>Sentiment</b><br><br>Sentiment score: <b>' + roundUp((Number(myObj[0].pos)/Number(myObj[0].neg)),2) + '</b><br><br> Based on: <b>' + (Number(myObj[0].pos) + Number(myObj[0].neg)) +            '</b> tweets<hr><b>Weather</b><br><br>' + myObj[0].weather + '</div></div>';
        
        var infowindow = new google.maps.InfoWindow({
            content: contentString
        });
        
        var marker = new google.maps.Marker({
            position: {lat: Number(myObj[0].Lat) , lng: Number(myObj[0].lng)},
            map: map,
            icon: 'img/happyicon.png'
        }); 
        
        marker.addListener('click', function() {
            closeMarkers();
            infowindow.open(map, marker);
        });
        
        
        var contentString1 = '<div id="content">'+
        
        '<div id="siteNotice">'+
        '</div>'+
        '<h3 id="firstHeading" class="firstHeading">Sentiment statistics <br> <h5>' + myObj[1].name + '</h5></h3>'+
        '<div id="bodyContent">'+
        '<p> <hr><b>Sentiment</b><br><br>Sentiment score: <b>' + roundUp((Number(myObj[1].pos)/Number(myObj[1].neg)),2) + '</b><br><br> Based on: <b>' + (Number(myObj[1].pos) + Number(myObj[1].neg)) +            '</b> tweets<hr><b>Weather</b><br><br>' + myObj[1].weather + '</div></div>';
        
        var infowindow1 = new google.maps.InfoWindow({
            content: contentString1
        });
        
        var marker1 = new google.maps.Marker({
            position: {lat: Number(myObj[1].Lat) , lng: Number(myObj[1].lng)},
            map: map,
            icon: 'img/happyicon.png'
        }); 
        
        marker1.addListener('click', function() {
            closeMarkers();
            infowindow1.open(map, marker1);
        });
        
        var contentString2 = '<div id="content">'+
        
        '<div id="siteNotice">'+
        '</div>'+
        '<h3 id="firstHeading" class="firstHeading">Sentiment statistics <br> <h5>' + myObj[2].name + '</h5></h3>'+
        '<div id="bodyContent">'+
        '<p> <hr><b>Sentiment</b><br><br>Sentiment score: <b>' + roundUp((Number(myObj[2].pos)/Number(myObj[2].neg)),2) + '</b><br><br> Based on: <b>' + (Number(myObj[2].pos) + Number(myObj[2].neg)) +            '</b> tweets<hr><b>Weather</b><br><br>' + myObj[2].weather + '</div></div>';
        
        var infowindow2 = new google.maps.InfoWindow({
            content: contentString2
        });
        
        var marker2 = new google.maps.Marker({
            position: {lat: Number(myObj[2].Lat) , lng: Number(myObj[2].lng)},
            map: map,
            icon: 'img/happyicon.png'
        }); 
        
        marker2.addListener('click', function() {
            closeMarkers();
            infowindow2.open(map, marker2);
        });
        
        
        function closeMarkers(){
            infowindow.close(map, marker);
            infowindow1.close(map, marker1);
            infowindow2.close(map, marker2);
        };
        
    }
});

// Create the search box and link it to the UI element.
var input = document.getElementById('pac-input');
var searchBox = new google.maps.places.SearchBox(input);
map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

// Bias the SearchBox results towards current map's viewport.
map.addListener('bounds_changed', function() {
    searchBox.setBounds(map.getBounds());
});

//var markers = [];
// Listen for the event fired when the user selects a prediction and retrieve
// more details for that place.
searchBox.addListener('places_changed', function() {
    var places = searchBox.getPlaces();
    
    if (places.length == 0) {
        return;
    }
    
    // Clear out the old markers.
    markers.forEach(function(marker) {
        marker.setMap(null);
    });
    markers = [];
    
    // For each place, get the icon, name and location.
    var bounds = new google.maps.LatLngBounds();
    places.forEach(function(place) {
        if (!place.geometry) {
            console.log("Returned place contains no geometry");
            return;
        }
        var icon = {
            url: place.icon,
            size: new google.maps.Size(71, 71),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(17, 34),
            scaledSize: new google.maps.Size(25, 25)
        };
        
        // Create a marker for each place.
        
        
        if (place.geometry.viewport) {
            // Only geocodes have viewport.
            bounds.union(place.geometry.viewport);
        } else {
            bounds.extend(place.geometry.location);
        }
    });
    map.fitBounds(bounds);
    sentiment(bounds);
});
}

function sentiment(bounds){
    var string = String(bounds);
    string = string.replace(/[|&;$%@"<>()+,]/g, "");
    string = string.split(" ");
    var city = String(document.getElementById('pac-input').value);
    var nelat = string[0];
    var nelng = string[1];
    var swlat = string[2];
    var swlng = string[3];
    var centerlat = Number((Number(nelat) + Number(swlat)) / 2);
    var centerlng = Number((Number(nelng) + Number(swlng)) / 2);
    addMarker(centerlat, centerlng);
    $.ajax({ url: 'ajax/getCityData.php',
    data: {lat: centerlat, lng: centerlng, name: city},
    type: 'GET',
    success: function(output) {
        var sentiment = output.split(";");
        var positive = sentiment[0];
        var negative = sentiment[1];
        var neutral = sentiment[2];
        var weather = sentiment[3];
        addMarker(centerlat, centerlng, positive, negative, neutral, weather)
    }
});
}

function addMarker(x, y, pos, neg, neu, weather) {
    pos = Number(pos);
    neu = Number(neu);
    neg = Number(neg);
    total = pos + neu + neg;
    var contentString = '<div id="content">'+
    
    '<div id="siteNotice">'+
    '</div>'+
    '<h3 id="firstHeading" class="firstHeading">Sentiment statistics <br> <h5>' + String(document.getElementById('pac-input').value) + '</h5></h3>'+
    '<div id="bodyContent">'+
    '<p> <hr><b>Sentiment</b><br><br>Sentiment score: <b>' + roundUp((pos/neg),2) + '</b><br><br> Based on: <b>' + (pos + neg) +            '</b> tweets<hr><b>Weather</b><br><br>' + weather + '</div></div>';
    
    var infowindow = new google.maps.InfoWindow({
        content: contentString
    });
    
    if (pos > neg){
        var marker = new google.maps.Marker({
            position: {lat: x, lng: y},
            map: map,
            icon: 'img/happyicon.png'
        }); 
        
    } else {
        var marker = new google.maps.Marker({
            position: {lat: x, lng: y},
            map: map,
            icon: 'img/happyicon.png'
        }); 
    }
        
    marker.addListener('click', function() {
        
        infowindow.open(map, marker);
    });
    marker.setMap(map);
}

function roundUp(num, precision) {
    precision = Math.pow(10, precision)
    return Math.ceil(num * precision) / precision
}

</script>
        
        
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCeTHUubh5DgyGrk01N9rCoQNBYaNvtSAg&callback=initMap&libraries=places&callback=initAutocomplete">
</script>


<?php include('templates/footer.php'); ?>