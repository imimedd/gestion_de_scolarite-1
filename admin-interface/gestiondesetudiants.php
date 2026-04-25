<?php
session_start();
require_once("../connexion.php");

$success_message = null;
$error_message   = null;
$editMode        = false;
$etudiant        = null;

// DELETE
if (isset($_GET['delete'])) {
    try {
        $pdo->prepare("DELETE FROM etudiants WHERE numero=?")->execute([$_GET['delete']]);
        $success_message = "Étudiant supprimé avec succès.";
    } catch (PDOException $e) {
        $error_message = "Erreur lors de la suppression.";
    }
}

// EDIT mode
if (isset($_GET['edit'])) {
    $editMode = true;
    $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE numero=?");
    $stmt->execute([$_GET['edit']]);
    $etudiant = $stmt->fetch();
}

// INSERT / UPDATE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matricule  = trim($_POST['matricule']);
    $nom        = trim($_POST['nom']);
    $prenom     = trim($_POST['prenom']);
    $palier     = trim($_POST['palier']);
    $specialite = trim($_POST['specialite']);
    $section    = trim($_POST['section']);
    $etat       = trim($_POST['etat']);
    $groupe_td  = trim($_POST['groupe_td']);

    try {
        if (!empty($_POST['numero'])) {
            $pdo->prepare("UPDATE etudiants SET matricule=?, nom=?, prenom=?, palier=?, specialite=?, section=?, etat=?, groupe_td=? WHERE numero=?")
                ->execute([$matricule, $nom, $prenom, $palier, $specialite, $section, $etat, $groupe_td, $_POST['numero']]);
            $success_message = "Étudiant modifié avec succès.";
        } else {
            $pdo->prepare("INSERT INTO etudiants (matricule, nom, prenom, palier, specialite, section, etat, groupe_td) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")
                ->execute([$matricule, $nom, $prenom, $palier, $specialite, $section, $etat, $groupe_td]);
            $success_message = "Étudiant ajouté avec succès.";
        }
        $editMode = false;
    } catch (PDOException $e) {
        $error_message = "Erreur : " . $e->getMessage();
    }
}

$currentPage = basename($_SERVER['PHP_SELF']);
$annee = date("Y") . " / " . (date("Y") + 1);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Étudiants — USTHB</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../prof-interface/style.css">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div style="text-align: center;">
        <img src="img/usthb1.png" width="100" height="100"/>
        <p><font color="#ffffff"><font size="3"><center><b>usthb - Administrateur</b></center></font></font></p>
    </div>
    <br><hr>
    <div style="display: flex; align-items: center;">
        <img src="img/prof.png" width="90" height="90"/>
        <center><font color="#ffffff"><font size="3"><?= htmlspecialchars($_SESSION['prenom'] ?? '') . ' ' . htmlspecialchars($_SESSION['nom'] ?? '') ?></font></font></center>
    </div>
    <hr><br>
    <a href="acceuille1.php"              class="<?= $currentPage == 'acceuille1.php' ? 'active' : '' ?>">Accueil</a>
    <a href="Gestion_des_modules.php"     class="<?= $currentPage == 'Gestion_des_modules.php' ? 'active' : '' ?>">Gérer les modules</a>
    <a href="Gestion_des_notes.php"       class="<?= $currentPage == 'Gestion_des_notes.php' ? 'active' : '' ?>">Gérer les notes</a>
    <a href="Gestion_des_enseignants.php" class="<?= $currentPage == 'Gestion_des_enseignants.php' ? 'active' : '' ?>">Gérer les enseignants</a>
    <a href="gestiondesetudiants.php"     class="<?= strtolower($currentPage) == 'gestiondesetudiants.php' ? 'active' : '' ?>">Gérer les étudiants</a>
    <a href="logout.php"                  class="<?= $currentPage == 'logout.php' ? 'active' : '' ?>">Déconnexion</a>
</div>

<!-- MAIN -->
<div class="main">

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <div class="header-icon">🎓</div>
            <p style="color:#000; font-size:16px;">Gestion des étudiants — <span style="color:#888; font-weight:300;">Administrateur</span></p>
        </div>
        <div class="year-badge"><?= $annee ?></div>
    </div>
    <div class="page-content">

        <!-- ALERTS -->
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <!-- FORM -->
        <div class="form-card">
            <h2>
                <?php if ($editMode): ?>
                    ✏️ Modifier l'étudiant
                <?php else: ?>
                    ➕ Ajouter un étudiant
                <?php endif; ?>
            </h2>
            <form method="POST" action="gestiondesetudiants.php">
                <input type="hidden" name="numero" value="<?= $editMode ? htmlspecialchars($etudiant['numero']) : '' ?>">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Matricule</label>
                        <input type="text" name="matricule" placeholder="ex: 221234567" value="<?= $editMode ? htmlspecialchars($etudiant['matricule']) : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" name="nom" placeholder="Nom de famille" value="<?= $editMode ? htmlspecialchars($etudiant['nom']) : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Prénom</label>
                        <input type="text" name="prenom" placeholder="Prénom" value="<?= $editMode ? htmlspecialchars($etudiant['prenom']) : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Palier</label>
                        <select name="palier">
                            <?php foreach (['L1','L2','L3','M1','M2'] as $p): ?>
                                <option value="<?= $p ?>" <?= ($editMode && $etudiant['palier'] == $p) ? 'selected' : '' ?>><?= $p ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Spécialité</label>
                        <input type="text" name="specialite" placeholder="ex: ISIL" value="<?= $editMode ? htmlspecialchars($etudiant['specialite']) : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Section</label>
                        <select name="section">
                            <?php foreach (['A','B','C','D','E'] as $s): ?>
                                <option value="<?= $s ?>" <?= ($editMode && $etudiant['section'] == $s) ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>État</label>
                        <select name="etat">
                            <?php foreach (['AJR','ADC','ADM'] as $et): ?>
                                <option value="<?= $et ?>" <?= ($editMode && $etudiant['etat'] == $et) ? 'selected' : '' ?>><?= $et ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Groupe TD</label>
                        <input type="number" name="groupe_td" min="1" max="20" placeholder="ex: 3" value="<?= $editMode ? htmlspecialchars($etudiant['groupe_td']) : '' ?>" required>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <?= $editMode ? 'Enregistrer les modifications' : 'Ajouter l\'étudiant' ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- SEARCH + TABLE -->
        <div class="section-title">Liste des étudiants</div>

        <form method="GET" action="gestiondesetudiants.php" class="search-bar">
            <input type="text" name="search" placeholder="🔍  Rechercher par nom ou matricule..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="submit" class="btn btn-primary">Rechercher</button>
            <?php if (!empty($_GET['search'])): ?>
                <a href="gestiondesetudiants.php" class="btn btn-secondary">Effacer</a>
            <?php endif; ?>
        </form>

        <?php
        if (!empty($_GET['search'])) {
            $search = "%" . $_GET['search'] . "%";
            $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE nom LIKE ? OR matricule LIKE ? ORDER BY numero DESC");
            $stmt->execute([$search, $search]);
        } else {
            $stmt = $pdo->query("SELECT * FROM etudiants ORDER BY numero DESC");
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $count = count($rows);
        ?>

        <div class="table-card">
            <div class="table-card-header">
                Étudiants
                <span class="count-badge"><?= $count ?> résultat<?= $count > 1 ? 's' : '' ?></span>
            </div>
            <table class="table1">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Matricule</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Palier</th>
                        <th>Spécialité</th>
                        <th>Section</th>
                        <th>Groupe TD</th>
                        <th>État</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($count === 0): ?>
                    <tr><td colspan="10">
                        <div class="empty-state">
                            Aucun étudiant trouvé.
                        </div>
                    </td></tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['numero']) ?></td>
                        <td class="matricule-cell"><?= htmlspecialchars($row['matricule']) ?></td>
                        <td><strong><?= htmlspecialchars($row['nom']) ?></strong></td>
                        <td><?= htmlspecialchars($row['prenom']) ?></td>
                        <td><?= htmlspecialchars($row['palier']) ?></td>
                        <td><?= htmlspecialchars($row['specialite']) ?></td>
                        <td><?= htmlspecialchars($row['section']) ?></td>
                        <td><?= htmlspecialchars($row['groupe_td']) ?></td>
                        <td>
                            <?php
                            $etat = strtolower($row['etat']);
                            $badgeClass = 'badge-inactif';
                            if ($etat === 'actif') $badgeClass = 'badge-actif';
                            elseif ($etat === 'adm') $badgeClass = 'badge-adm';
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($row['etat']) ?></span>
                        </td>
                        <td>
                            <div class="action-btns">
                                <a href="?edit=<?= $row['numero'] ?>" class="btn-edit">
                                    ✏️ Modifier
                                </a>
                                <a href="gestiondesetudiants.php?delete=<?= $row['numero'] ?>" 
                                    class="btn-delete"
                                    onclick="return confirm('Supprimer <?= htmlspecialchars($row['nom'] . ' ' . $row['prenom'], ENT_QUOTES) ?> ?')">
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