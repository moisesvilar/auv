<?php
	// llamada al archivo de conexión
	include("conectar.php");
        include("cors.php");
        
	$queryString = "SELECT survey.survey_id,
				survey.code,
				survey.name AS survey_name,
				survey.description,
				c.name AS cruise_name,
				survey.notes,
				s.status,
				survey.status_id,
				v.name AS vehicle_name,
                                v.status as vehicle_status,
				survey.vehicle_id
			FROM survey
			INNER JOIN cruise AS c
				ON survey.cruise_id = c.cruise_id
			INNER JOIN cruise_or_survey_status AS s
				ON survey.status_id = s.cruise_or_survey_status_id
			INNER JOIN vehicle AS v
				ON survey.vehicle_id = v.vehicle_id
			WHERE survey.status_id!=4
                        ORDER BY survey.survey_id";

	// consulta sql
	$query = pg_query($queryString) or die(pg_last_error());

	// looping que crea un array con los campos de la consulta
	$surveys = array();
	while($survey = pg_fetch_array($query, null, PGSQL_ASSOC)) {
	    $surveys[] = $survey;
	}

	// consulta del total de líneas en la tabla
	$queryTotal = pg_query('SELECT count(*) as num FROM survey') or die(pg_last_error());
	$row = pg_fetch_array($queryTotal, null, PGSQL_ASSOC);
	$total = $row['num'];

	// formato JSON
	echo json_encode(array(
		"success" => pg_last_error() == 0,
		"total" => $total,
		"surveys" => $surveys
	));
?>
