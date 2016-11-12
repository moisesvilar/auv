<?php

// echo ini_set('display_errors', '1');
// llamada al archivo de conexión
include("conectar.php");
include("cors.php");

$survey_id = $_GET['survey'];

//$track_id = 6;
// consulta sql
$queryString = sprintf(
"SELECT
	w.waypoint_id, 
	w.code,
	w.name,
	w.description,
	w.track_node_index,
	ST_X(w.geom) AS lon,
	ST_Y(w.geom) AS lat
FROM waypoint AS w
INNER JOIN track AS t ON w.track_id = t.track_id
INNER JOIN survey as s ON t.track_id = s.track_id
WHERE s.survey_id=%s
ORDER BY w.track_node_index ASC;",
$survey_id);

$rs = pg_query($queryString);

// looping que crea un array con los campos de la consulta
$waypoints = array();
while ($waypoint = pg_fetch_array($rs, null, PGSQL_ASSOC)) {
    $waypoints[] = $waypoint;
}

// devolver resposta en formato JSON
echo json_encode(array(
    "success" => pg_last_error() == 0,
    "total" => sizeof($waypoints),
    "waypoints" => $waypoints
));
?>
