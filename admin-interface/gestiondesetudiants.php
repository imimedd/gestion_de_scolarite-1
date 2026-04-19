 <?php
session_start();
require_once("config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$editMode = false;
if (isset($_GET['edit'])) {
    $editMode = true;
    $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $etudiant = $stmt->fetch();
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM etudiants WHERE id=?")->execute([$_GET['delete']]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $matricule = $_POST['matricule'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $date = $_POST['date_naissance'];
    $email = $_POST['email'];
    $niveau = $_POST['niveau'];
    $m1 = $_POST['m1'];
    $m2 = $_POST['m2'];

    $moyenne = ($m1 + $m2) / 2;
    $statut = ($moyenne >= 10) ? "Admis" : "Ajourné";

    if (!empty($_POST['id'])) {
        $pdo->prepare("UPDATE etudiants SET matricule=?, nom=?, prenom=?, date_naissance=?, email=?, niveau=?, m1=?, m2=?, moyenne=?, statut=? WHERE id=?")
        ->execute([$matricule,$nom,$prenom,$date,$email,$niveau,$m1,$m2,$moyenne,$statut,$_POST['id']]);
    } else {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO etudiants (matricule, nom, prenom, date_naissance, email, password, niveau, m1, m2, moyenne, statut)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
        ->execute([$matricule,$nom,$prenom,$date,$email,$password,$niveau,$m1,$m2,$moyenne,$statut]);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Étudiants</title>

<style>
body {font-family: Arial; background:#f4f6f9; margin:0;}
.nav {background:#2c3e50; color:white; padding:15px;}
.container {width:90%; margin:auto;}
.card {background:white; padding:20px; margin-top:20px; border-radius:10px;}
input {padding:10px; width:100%; margin:5px 0;}
button {padding:10px; background:#3498db; color:white; border:none;}
table {width:100%; border-collapse:collapse;}
th {background:#3498db; color:white;}
td,th {padding:10px; text-align:center;}
a {padding:5px 10px; color:white; text-decoration:none; border-radius:5px;}
.edit {background:orange;}
.delete {background:red;}
</style>

</head>

<body>

<div class="nav">Gestion Étudiants 🎓</div>

<div class="container">

<div class="card">
<h2><?= $editMode ? "Modifier" : "Ajouter" ?> étudiant</h2>

<form method="POST">
<input type="hidden" name="id" value="<?= $editMode ? $etudiant['id'] : '' ?>">

<input type="text" name="matricule" placeholder="Matricule" value="<?= $editMode ? $etudiant['matricule'] : '' ?>" required>
<input type="text" name="nom" placeholder="Nom" value="<?= $editMode ? $etudiant['nom'] : '' ?>" required>
<input type="text" name="prenom" placeholder="Prénom" value="<?= $editMode ? $etudiant['prenom'] : '' ?>" required>
<input type="date" name="date_naissance" value="<?= $editMode ? $etudiant['date_naissance'] : '' ?>" required>
<input type="email" name="email" placeholder="Email" value="<?= $editMode ? $etudiant['email'] : '' ?>" required>
<input type="text" name="niveau" placeholder="Niveau" value="<?= $editMode ? $etudiant['niveau'] : '' ?>" required>

<input type="number" step="0.01" name="m1" placeholder="Note M1" value="<?= $editMode ? $etudiant['m1'] : '' ?>" required>
<input type="number" step="0.01" name="m2" placeholder="Note M2" value="<?= $editMode ? $etudiant['m2'] : '' ?>" required>

<input type="password" name="password" placeholder="Mot de passe">

<button type="submit"><?= $editMode ? "Modifier" : "Ajouter" ?></button>
</form>
</div>

<div class="card">

<form method="GET">
<input type="text" name="search" placeholder="Rechercher">
<button>Search</button>
</form>

<table>
<tr>
<th>ID</th>
<th>Matricule</th>
<th>Nom</th>
<th>Prénom</th>
<th>M1</th>
<th>M2</th>
<th>Moyenne</th>
<th>Statut</th>
<th>Action</th>
</tr>

<?php
$count = 0;
 if (!empty($_GET['search'])) {
    $search = "%" . $_GET['search'] . "%";
    $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE nom LIKE ? OR prenom LIKE ?");
    $stmt->execute([$search,$search]);
} else {
    $stmt = $pdo->query("SELECT * FROM etudiants");
}

while ($row = $stmt->fetch()) {
    $count++;
    echo "<tr>
    <td>{$row['id']}</td>
    <td>{$row['matricule']}</td>
    <td>{$row['nom']}</td>
    <td>{$row['prenom']}</td>
    <td>{$row['m1']}</td>
    <td>{$row['m2']}</td>
    <td>{$row['moyenne']}</td>
    <td>{$row['statut']}</td>
    <td>
        <a class='edit' href='?edit={$row['id']}'>Edit</a>
        <a class='delete' href='?delete={$row['id']}'>Delete</a>
    </td>
    </tr>";
}

if ($count == 0) {
    echo "<tr><td colspan='9'>Aucun résultat</td></tr>";
}
?>

</table>

</div>

</div>

</body>
</html>