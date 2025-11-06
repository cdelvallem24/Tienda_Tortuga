<?php
//DB details
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '4988';
$dbName = 'tienda';

//Create connection and select DB
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

if ($db->connect_error) {
    die("No hay Conexion con la base de datos: " . $db->connect_error);
}
