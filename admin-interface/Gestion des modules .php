<?php
session_start();
require_once("config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ADD
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = $_POST['code'];
    $intitule = $_POST['intitule'];
    $coef = $_POST['coefficient'];

    $pdo->prepare("INSERT INTO modules (code, intitule, coefficient) VALUES (?, ?, ?)")
        ->execute([$code, $intitule, $coef]);
}

// DELETE
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM modules WHERE id=?")->execute([$_GET['delete']]);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Modules</title>
<style>
body {font-family:Arial;background:#f4f6f9;}
.container {width:80%;margin:auto;}
.card {background:white;padding:20px;margin-top:20px;}
table {width:100%;border-collapse:collapse;}
th {background:#3498db;color:white;}
td,th {padding:10px;text-align:center;}
.delete {background:red;color:white;padding:5px 10px;text-decoration:none;}
</style>
</head>

<body>

<div class="container">

<div class="card">
<h2>Ajouter Module</h2>

<form method="POST">
<input type="text" name="code" placeholder="Code" required><br><br>
<input type="text" name="intitule" placeholder="Intitulé" required><br><br>
<input type="number" name="coefficient" placeholder="Coefficient" required><br><br>
<button>Ajouter</button>
</form>
</div>

<div class="card">
<h2>Liste Modules</h2>

<table>
<tr>
<th>ID</th>
<th>Code</th>
<th>Intitulé</th>
<th>Coef</th>
<th>Action</th>
</tr>

<?php
foreach ($pdo->query("SELECT * FROM modules") as $row) {
    echo "<tr>
    <td>{$row['id']}</td>
    <td>{$row['code']}</td>
    <td>{$row['intitule']}</td>
    <td>{$row['coefficient']}</td>
    <td><a class='delete' href='?delete={$row['id']}'>Delete</a></td>
    </tr>";
}
?>

</table>

</div>

</div>

</body>
</html>