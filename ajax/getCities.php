<?php
require('./../includes/top.php');

$querry = "SELECT distinct * FROM  `citiesdata` WHERE name IN ('New York City; New', ' Los Angeles; Califo', 'Chicago; Illinois') group by name";
$query = mysqli_query($db2, $querry);
$rows = array();

while($r = mysqli_fetch_assoc($query)) {
    $rows[] = $r;
}
echo json_encode($rows);