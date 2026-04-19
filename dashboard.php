<?php
session_start();
require_once("database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// STATS
$nb_etudiants = $pdo->query("SELECT COUNT(*) FROM etudiants")->fetchColumn();
$nb_modules = $pdo->query("SELECT COUNT(*) FROM modules")->fetchColumn();
$nb_enseignants = $pdo->query("SELECT COUNT(*) FROM enseignants")->fetchColumn();

// BEST STUDENT
$best = $pdo->query("SELECT nom, moyenne FROM etudiants ORDER BY moyenne DESC LIMIT 1")->fetch();

// GLOBAL MOYENNE
$global = $pdo->query("SELECT AVG(moyenne) FROM etudiants")->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>

<style>
body {margin:0;font-family:Arial;background:#f4f6f9;}
.sidebar {width:220px;height:100vh;background:#2c3e50;position:fixed;color:white;}
.sidebar h2 {text-align:center;padding:10px;}
.sidebar a {display:block;padding:12px;color:white;text-decoration:none;}
.sidebar a:hover {background:#34495e;}

.main {margin-left:220px;padding:20px;}

.cards {display:flex;gap:20px;flex-wrap:wrap;}
.card {flex:1;background:white;padding:20px;border-radius:10px;text-align:center;}

.big {font-size:25px;font-weight:bold;}
.green {color:green;}
</style>

</head>

<body>

<div class="sidebar">
<h2>Admin</h2>
<a href="etudiants.php">Étudiants</a>
<a href="enseignants.php">Enseignants</a>
<a href="modules.php">Modules</a>
<a href="notes.php">Notes</a>
<a href="inscriptions.php">Inscriptions</a>
<a href="logout.php">Déconnexion</a>
</div>

<div class="main">

<h1>Dashboard 📊</h1>

<div class="cards">

<div class="card">
<h3>Étudiants</h3>
<p class="big"><?= $nb_etudiants ?></p>
</div>

<div class="card">
<h3>Modules</h3>
<p class="big"><?= $nb_modules ?></p>
</div>

<div class="card">
<h3>Enseignants</h3>
<p class="big"><?= $nb_enseignants ?></p>
</div>

<div class="card">
<h3>Moyenne Générale</h3>
<p class="big"><?= round($global,2) ?></p>
</div>

<div class="card">
<h3>Meilleur Étudiant</h3>
<p class="big green">
<?= $best ? $best['nom'] . " (" . round($best['moyenne'],2) . ")" : "N/A" ?>
</p>
</div>

</div>

</div>

</body>
</html>