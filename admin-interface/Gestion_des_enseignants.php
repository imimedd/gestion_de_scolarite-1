<?php
session_start();
require_once("../database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ADD
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];

    $pdo->prepare("INSERT INTO enseignants (nom, prenom, email) VALUES (?, ?, ?)")
        ->execute([$nom, $prenom, $email]);
}

// DELETE
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM enseignants WHERE id_enseignant=?")->execute([$_GET['delete']]);
}
?>

<!DOCTYPE html>
<html>
<head><title>Enseignants</title></head>
<body>

<h2>Ajouter Enseignant</h2>

<form method="POST">
<input type="text" name="nom" placeholder="Nom" required><br><br>
<input type="text" name="prenom" placeholder="Prénom" required><br><br>
<input type="email" name="email" placeholder="Email" required><br><br>
<button>Ajouter</button>
</form>

<hr>

<table border="1">
<tr>
<th>ID</th><th>Nom</th><th>Prénom</th><th>Email</th><th>Action</th>
</tr>

<?php
foreach ($pdo->query("SELECT * FROM enseignants") as $row) {
    echo "<tr>
    <td>{$row['id_enseignant']}</td>
    <td>{$row['nom']}</td>
    <td>{$row['prenom']}</td>
    <td>{$row['email']}</td>
    <td><a href='?delete={$row['id']}'>Delete</a></td>
    </tr>";
}
?>

</table>

</body>
</html>