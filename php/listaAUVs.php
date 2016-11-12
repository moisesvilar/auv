<?php
	// llamada al archivo de conexión
	include("conectar.php");
        include("cors.php");

	$queryString = "SELECT vehicle_id,
				name,
				description,
				status,
				make,
				model				
			FROM vehicle
                        ORDER BY name";

	// consulta sql
	$query = pg_query($queryString) or die(pg_last_error());

	// looping que crea un array con los campos de la consulta
	$vehicles = array();
	while($vehicle = pg_fetch_array($query, null, PGSQL_ASSOC)) {
	    $vehicles[] = $vehicle;
	}

	// consulta del total de líneas en la tabla
	$queryTotal = pg_query('SELECT count(*) as num FROM vehicle') or die(pg_last_error());
	$row = pg_fetch_array($queryTotal, null, PGSQL_ASSOC);
	$total = $row['num'];

	// formato JSON
	echo json_encode(array(
		"success" => pg_last_error() == 0,
		"total" => $total,
		"vehicles" => $vehicles
	));
?>
