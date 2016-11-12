<?php

include("conectar.php");
include("cors.php");

$vehicle = $_GET['vehicle'];
$timestamp = $_GET['timestamp'];
$debug = $_GET['debug'];

if ($debug == 1) {
    $queryString = sprintf(
            "INSERT INTO VEHICLE_LOCATION (vehicle_id, position_ts, position_source_id, geom) VALUES 
             (%s,'%s',1,(SELECT ST_AsText(RandomPoint(42.25, -8.9, 42.2, -8.8))))", 
            $vehicle,
            $timestamp);
    pg_query($queryString);
}

$queryString = sprintf(
"select
    l.vehicle_location_id as id,
    l.vehicle_id as vehicle,
    l.position_ts as ts,
    ST_X(l.geom) AS lon,
    ST_Y(l.geom) AS lat
from vehicle_location as l
where l.vehicle_id = %s
and l.position_ts >= '%s'
order by l.position_ts desc
limit 1", $vehicle, $timestamp);

$rs = pg_query($queryString);

$position = pg_fetch_object($rs);

echo json_encode($position);
?>