<?php
	// llamada al archivo de conexión
	include("conectar.php");
        include("cors.php");

        $id = $_GET['id'];
        
        
	$queryString = sprintf("SELECT device.vehicle_id,
				device.name AS device_name,
				ds.status AS device_status				
			FROM device
			INNER JOIN device_status AS ds
				ON device.status_id = ds.device_status_id
                        WHERE device.vehicle_id = %s", $id);

	// consulta sql
	$query = pg_query($queryString) or die(pg_last_error());

	// looping que crea un array con los campos de la consulta
	$devices = array();
	while($device = pg_fetch_array($query, null, PGSQL_ASSOC)) {
	    $devices[] = $device;
	}

	// consulta del total de líneas en la tabla
	$queryTotal = pg_query('SELECT count(*) as num FROM device') or die(pg_last_error());
	$row = pg_fetch_array($queryTotal, null, PGSQL_ASSOC);
	$total = $row['num'];

	// formato JSON
	echo json_encode(array(
		"success" => pg_last_error() == 0,
		"total" => $total,
		"devices" => $devices
	));
?>
