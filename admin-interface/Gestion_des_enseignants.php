<?php
session_start();
require_once("../connexion.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$success_message = null;
$error_message   = null;

// ADD
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom    = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email  = trim($_POST['email']);
    try {
        $pdo->prepare("INSERT INTO enseignants (nom, prenom, email) VALUES (?, ?, ?)")
            ->execute([$nom, $prenom, $email]);
        $success_message = "Enseignant ajouté avec succès.";
    } catch (PDOException $e) {
        $error_message = "Erreur : " . $e->getMessage();
    }
}

// DELETE
if (isset($_GET['delete'])) {
    try {
        $pdo->prepare("DELETE FROM enseignants WHERE id_enseignant=?")->execute([$_GET['delete']]);
        $success_message = "Enseignant supprimé avec succès.";
    } catch (PDOException $e) {
        $error_message = "Erreur lors de la suppression.";
    }
}

$currentPage = basename($_SERVER['PHP_SELF']);
$annee = date("Y") . " / " . (date("Y") + 1);
$enseignants = $pdo->query("SELECT * FROM enseignants ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
$count = count($enseignants);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Enseignants — USTHB</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../prof-interface/style.css">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div style="text-align:center;">
        <img src="img/usthb1.png" width="100" height="100"/>
        <p><font color="#ffffff"><font size="3"><center><b>USTHB — Administrateur</b></center></font></font></p>
    </div>
    <br><hr>
    <div style="display:flex;align-items:center;">
        <img src="img/prof.png" width="90" height="90"/>
        <center><font color="#ffffff"><font size="3"><?= htmlspecialchars(($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? '')) ?></font></font></center>
    </div>
    <hr><br>
    <a href="acceuille1.php"              class="<?= $currentPage=='acceuille1.php'?'active':'' ?>">Accueil</a>
    <a href="Gestion_des_modules.php"     class="<?= $currentPage=='Gestion_des_modules.php'?'active':'' ?>">Gérer les modules</a>
    <a href="Gestion_des_notes.php"       class="<?= $currentPage=='Gestion_des_notes.php'?'active':'' ?>">Gérer les notes</a>
    <a href="Gestion_des_enseignants.php" class="<?= $currentPage=='Gestion_des_enseignants.php'?'active':'' ?>">Gérer les enseignants</a>
    <a href="gestiondesetudiants.php"     class="<?= $currentPage=='gestiondesetudiants.php'?'active':'' ?>">Gérer les étudiants</a>
    <a href="logout.php"                  class="<?= $currentPage=='logout.php'?'active':'' ?>">Déconnexion</a>
</div>

<!-- MAIN -->
<div class="main">
    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <div class="header-icon">👨‍🏫</div>
            <p style="color:#000;font-size:16px;">Gestion des enseignants — <span style="color:#888;font-weight:300;">Administrateur</span></p>
        </div>
        <div class="year-badge"><?= $annee ?></div>
    </div>

    <div class="page-content">

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line></svg>
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <!-- FORM -->
        <div class="section-title">ajouter un enseignant</div>
        <div class="form-card">
            <h2>➕ Ajouter un enseignant</h2>
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" name="nom" placeholder="Nom de famille" required>
                    </div>
                    <div class="form-group">
                        <label>Prénom</label>
                        <input type="text" name="prenom" placeholder="Prénom" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="exemple@usthb.dz" required>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Ajouter l'enseignant
                    </button>
                </div>
            </form>
        </div>

        <!-- TABLE -->
        <div class="section-title">liste des enseignants</div>
        <div class="table-card">
            <div class="table-card-header">
                Enseignants
                <span class="count-badge"><?= $count ?> résultat<?= $count > 1 ? 's' : '' ?></span>
            </div>
            <table>
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
                <?php if ($count === 0): ?>
                    <tr><td colspan="5"><div class="empty-state">Aucun enseignant enregistré.</div></td></tr>
                <?php else: ?>
                    <?php foreach ($enseignants as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id_enseignant']) ?></td>
                        <td><strong><?= htmlspecialchars($row['nom']) ?></strong></td>
                        <td><?= htmlspecialchars($row['prenom']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <div class="action-btns">
                                <a href="?delete=<?= $row['id_enseignant'] ?>"
                                   class="btn-delete"
                                   onclick="return confirm('Supprimer <?= htmlspecialchars($row['nom'].' '.$row['prenom'], ENT_QUOTES) ?> ?')">
                                    🗑️ Supprimer
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</body>
</html>