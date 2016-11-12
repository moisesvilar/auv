<?php

	include("conectar.php");
        include("cors.php");

	$info = file_get_contents("php://input");

	$data = json_decode($info);

	$survey_id = $data->survey_id;
	$status_id = $data->status_id;

	// consulta sql
	$query = sprintf("UPDATE survey SET status_id = '%s' WHERE survey_id='%s'",
		$status_id,
		$survey_id);

	$rs = pg_query($query);

	echo json_encode(array(
		"success" => pg_last_error() == 0,
		"surveys" => array(
			"survey_id" => $survey_id,
			"status_id" => $status_id
		)
	));
?>
