<?php
session_start();
require_once("../database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// ADD
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id = $_POST['id_module'];
    $intitule = $_POST['nom_module'];
    $code = $_POST['code_module'];
    $niveau = $_POST['niveau'];
    $semestre = $_POST['semestre'];
    $coef = $_POST['coef'];
    $pdo->prepare("INSERT INTO modules (id_module, nom_module, code_module, niveau, semestre, coef) VALUES (?, ?, ?, ?, ?, ?)")
    ->execute([$id, $intitule, $code, $niveau, $semestre, $coef]);
}

// DELETE
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM modules WHERE id_module=?")->execute([$_GET['delete']]);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<title>Gestion des Modules</title>
 <link rel="stylesheet" href="style.css">
</head>

<body>
<div class="admin-container">

    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="../image/logo.jpg" alt="USTHB">
        </div>
        <nav class="sidebar-nav">
            <a href="../dashboard.php">🏠 Dashboard</a>
            <a href="gestiondesetudiants.php">🎓 Étudiants</a>
            <a href="Gestion_des_enseignants.php">👨‍🏫 Enseignants</a>
            <a href="Gestion des modules .php" class="active">📚 Modules</a>
            <a href="Gestion des notes .php">📝 Notes</a>
            <a href="../logout.php" class="btn-deconnexion">🚪 Déconnexion</a>
        </nav>
    </aside>

    <main class="admin-main">

        <div class="admin-header">
            <h2>📚 Gestion des Modules</h2>
        </div>

        <div class="form-card">
            <h3>Ajouter un Module</h3>
            <form method="POST">
                <input type="text" name="id_module" placeholder="id module" required>
                <input type="text" name="nom_module" placeholder="Intitulé du module" required>
                <input type="text" name="code_module" placeholder="Code du module" required>
                <input type="text" name="niveau" placeholder="Niveau (ex: L1 Informatique)" required>
                <input type="text" name="semestre" placeholder="Semestre (ex: Semestre 1)" required>
                <input type="number" name="coef" placeholder="Coefficient" required>
                <button type="submit" class="btn-ajouter">➕ Ajouter</button>
            </form>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>id_module</th>
                    <th>code_module</th>
                    <th>nom_module</th>
                    <th>niveau</th>
                    <th>semestre</th>
                    <th>coef</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($pdo->query("SELECT * FROM modules") as $row): ?>
                <tr>
                    <td><?= $row['id_module'] ?></td>
                    <td><?= $row['code_module'] ?></td>
                    <td><?= $row['nom_module'] ?></td>
                    <td><?= $row['niveau'] ?></td>
                    <td><?= $row['semestre'] ?></td>
                    <td><?= $row['coef'] ?></td>
                    <td>
                        <a href="?delete=<?= $row['id_module'] ?>" class="btn-supprimer">❌ Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
