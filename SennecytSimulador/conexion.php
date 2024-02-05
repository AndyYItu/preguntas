<?php
$host = "localhost";
$usuario_db = "id21784416_sennecyt";
$contrasena_db = "@Admin2024";
$nombre_db = "id21784416_sennecyt";

$conexion = new mysqli($host, $usuario_db, $contrasena_db, $nombre_db);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
