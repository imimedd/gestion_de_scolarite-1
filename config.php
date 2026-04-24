<<<<<<< HEAD
<?php
$host = 'localhost';
$dbname = 'scolarite';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die('Erreur de connexion : ' . mysqli_connect_error());
}
=======
<?php
$host = 'localhost';
$dbname = 'scolarite';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die('Erreur de connexion : ' . mysqli_connect_error());
}
>>>>>>> 2932a0bf2df97e2007ef5a885fb58c4eb10562d5
?>