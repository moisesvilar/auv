<?php
 
// Nombre do servidor
$host = "localhost";

// Puerto
$port = 5432;
 
// Usuario
$user = "postgres";
 
// Contraseña
$password = "1234";
 
// Nombre de la base de datos
$dbname = "auv";
 
// Ejecuta la conexión, en caso negativo muestra el error
$dbconn = pg_connect("host = $host port = $port dbname = $dbname user = $user password = $password")
    or die('No ha sido posible realizar la conexión: ' . pg_last_error());
 
?>
