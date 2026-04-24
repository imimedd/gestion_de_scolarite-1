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
<link rel="stylesheet" href="style.css">

<div class="admin-container">

    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="../image/logo.jpg" alt="USTHB">
        </div>
        <nav class="sidebar-nav">
            <a href="../dashboard.php">🏠 Dashboard</a>
            <a href="gestiondesetudiants.php">🎓 Étudiants</a>
            <a href="Gestion_des_enseignants.php" class="active">👨‍🏫 Enseignants</a>
            <a href="Gestion des modules .php">📚 Modules</a>
            <a href="Gestion des notes .php">📝 Notes</a>
            <a href="../logout.php" class="btn-deconnexion">🚪 Déconnexion</a>
        </nav>
    </aside>

    <main class="admin-main">

        <div class="admin-header">
            <h2>👨‍🏫 Gestion des Enseignants</h2>
        </div>

        <div class="form-card">
            <h3>Ajouter un Enseignant</h3>
            <form method="POST">
                <input type="text" name="nom" placeholder="Nom" required>
                <input type="text" name="prenom" placeholder="Prénom" required>
                <input type="email" name="email" placeholder="Email" required>
                <button type="submit">+ Ajouter</button>
            </form>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($pdo->query("SELECT * FROM enseignants") as $row): ?>
                <tr>
                    <td><?= $row['id_enseignant'] ?></td>
                    <td><?= $row['nom'] ?></td>
                    <td><?= $row['prenom'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td>
                        <a href="?delete=<?= $row['id_enseignant'] ?>" class="btn-supprimer">❌ Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </main>
</div>
