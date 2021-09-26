<?php
$servername = "localhost";
$username = "root";
$password = "";

/**
 * Introducción de credenciales
 * Creación y comprobación de la conexión
 **/

$conn = mysqli_connect($servername, $username, $password);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
include_once("../model/database.php");
$database = database::getInstance();
$result_dblp = $database->query("SELECT * FROM dblp_results");
$result_gs = $database->query("SELECT * FROM gs_results");

$lee_autor=$database->query("SELECT autor FROM dblp_results");
$autor = mysqli_fetch_array($lee_autor);

// Contadores de resultados
$lee_num_dblp=$database->query("SELECT COUNT(*) FROM dblp_results");
$num_dblp = mysqli_fetch_array($lee_num_dblp); // DBLP

$lee_num_gs=$database->query("SELECT COUNT(*) FROM gs_results");
$num_gs = mysqli_fetch_array($lee_num_gs); // Google Scgolar




