<?php
$host = 'localhost';
$dbname = 'gestion_scolarite';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die('Erreur de connexion : ' . mysqli_connect_error());
}
?>