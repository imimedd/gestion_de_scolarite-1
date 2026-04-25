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
    $nom      = trim($_POST['nom_module']);
    $code     = trim($_POST['code_module']);
    $niveau   = trim($_POST['niveau']);
    $semestre = trim($_POST['semestre']);
    $coef     = trim($_POST['coef']);
    try {
        $pdo->prepare("INSERT INTO modules (nom_module, code_module, niveau, semestre, coef) VALUES (?, ?, ?, ?, ?)")
            ->execute([$nom, $code, $niveau, $semestre, $coef]);
        $success_message = "Module ajouté avec succès.";
    } catch (PDOException $e) {
        $error_message = "Erreur : " . $e->getMessage();
    }
}

// DELETE
if (isset($_GET['delete'])) {
    try {
        $pdo->prepare("DELETE FROM modules WHERE id_module=?")->execute([$_GET['delete']]);
        $success_message = "Module supprimé avec succès.";
    } catch (PDOException $e) {
        $error_message = "Erreur lors de la suppression.";
    }
}

$currentPage = basename($_SERVER['PHP_SELF']);
$annee       = date("Y") . " / " . (date("Y") + 1);
$modules     = $pdo->query("SELECT * FROM modules ORDER BY semestre, nom_module")->fetchAll(PDO::FETCH_ASSOC);
$count       = count($modules);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Modules — USTHB</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../prof-interface/style.css">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div style="text-align:center;">
        <img src="img/usthb1.png" width="100" height="100"/>
        <p style="color:#ffffff;font-size:14px;font-weight:bold;text-align:center;">USTHB — Administrateur</p>
    </div>
    <br><hr>
    <div style="display:flex;align-items:center;gap:10px;padding:0 15px;">
        <img src="img/prof.png" width="90" height="90"/>
        <span style="color:#ffffff;font-size:14px;"><?= htmlspecialchars(($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? '')) ?></span>
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
            <div class="header-icon">📚</div>
            <p style="color:#000;font-size:16px;">Gestion des modules — <span style="color:#888;font-weight:300;">Administrateur</span></p>
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
        <div class="section-title">ajouter un module</div>
        <div class="form-card">
            <h2>➕ Ajouter un module</h2>
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nom du module</label>
                        <input type="text" name="nom_module" placeholder="Intitulé du module" required>
                    </div>
                    <div class="form-group">
                        <label>Code</label>
                        <input type="text" name="code_module" placeholder="Code du module" required>
                    </div>
                    <div class="form-group">
                        <label>Niveau</label>
                        <input type="text" name="niveau" placeholder="ex: L1 Informatique" required>
                    </div>
                    <div class="form-group">
                        <label>Semestre</label>
                        <input type="text" name="semestre" placeholder="ex: Semestre 1" required>
                    </div>
                    <div class="form-group">
                        <label>Coefficient</label>
                        <input type="number" name="coef" placeholder="ex: 3" min="1" required>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Ajouter le module
                    </button>
                </div>
            </form>
        </div>

        <!-- TABLE -->
        <div class="section-title">liste des modules</div>
        <div class="table-card">
            <div class="table-card-header">
                Modules
                <span class="count-badge"><?= $count ?> résultat<?= $count > 1 ? 's' : '' ?></span>
            </div>
            <table class="table1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Nom du module</th>
                        <th>Niveau</th>
                        <th>Semestre</th>
                        <th>Coef</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($count === 0): ?>
                    <tr><td colspan="7"><div class="empty-state">Aucun module enregistré.</div></td></tr>
                <?php else: ?>
                    <?php foreach ($modules as $row): ?>
                    <tr>
                        <td class="matricule-cell"><?= htmlspecialchars($row['id_module']) ?></td>
                        <td><?= htmlspecialchars($row['code_module']) ?></td>
                        <td><strong><?= htmlspecialchars($row['nom_module']) ?></strong></td>
                        <td><?= htmlspecialchars($row['niveau']) ?></td>
                        <td><?= htmlspecialchars($row['semestre']) ?></td>
                        <td><?= htmlspecialchars($row['coef']) ?></td>
                        <td>
                            <div class="action-btns">
                                <a href="?delete=<?= urlencode($row['id_module']) ?>"
                                   class="btn-delete"
                                   onclick="return confirm('Supprimer le module <?= htmlspecialchars($row['nom_module'], ENT_QUOTES) ?> ?')">
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