<?php
session_start();
require_once("../connexion.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$annee       = date("Y") . " / " . (date("Y") + 1);
$currentPage = basename($_SERVER['PHP_SELF']);

// Get id_enseignant from matricule session
$matricule = $_SESSION['user_id'] ?? 0;
$stmtEns = $pdo->prepare("SELECT id_enseignant FROM enseignants WHERE matricule = ?");
$stmtEns->execute([$matricule]);
$id_enseignant = $stmtEns->fetchColumn();

// Récupérer les groupes depuis la BDD (seulement les groupes de l'enseignant)
$stmtGroupes = $pdo->prepare("
    SELECT DISTINCT g.id_groupe, g.nom_groupe 
    FROM groupes g
    JOIN module_groupe mg ON g.id_groupe = mg.id_groupe
    JOIN enseignant_module em ON mg.id_module = em.id_module
    WHERE em.id_enseignant = ?
    ORDER BY g.nom_groupe
");
$stmtGroupes->execute([$id_enseignant]);
$groupes = $stmtGroupes->fetchAll(PDO::FETCH_ASSOC);

// Liste des ID de groupes valides pour l'enseignant
$ids_groupes_valides = array_column($groupes, 'id_groupe');
if (empty($ids_groupes_valides)) {
    $ids_groupes_valides = [0];
}
$in_groupes = implode(',', $ids_groupes_valides);

// Groupe choisi (par défaut : tous)
$groupe_choisi_id = $_GET['groupe'] ?? '';

// Recherche
$recherche = $_GET['recherche'] ?? '';

// Construire la requête — etudiants.groupe_td = groupes.id_groupe
$sql = "SELECT e.matricule, e.nom, e.prenom, g.nom_groupe 
        FROM etudiants e 
        JOIN groupes g ON e.groupe_td = g.id_groupe
        WHERE e.groupe_td IN ($in_groupes)";
$params = [];

if (!empty($groupe_choisi_id)) {
    $sql .= " AND e.groupe_td = ?";
    $params[] = $groupe_choisi_id;
}

if (!empty($recherche)) {
    $sql .= " AND (e.nom LIKE ? OR e.prenom LIKE ? OR e.matricule LIKE ?)";
    $params[] = "%$recherche%";
    $params[] = "%$recherche%";
    $params[] = "%$recherche%";
}

$sql .= " ORDER BY e.nom ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = count($etudiants);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des étudiants — Enseignant</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .groupe-tag {
            display: inline-block;
            background: #e8eef7;
            color: #1a2a4a;
            font-size: 12px;
            padding: 3px 10px;
            border-radius: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div style="text-align:center;">
        <img src="img/usthb1.png" width="100" height="100"/>
        <p style="color:#ffffff;font-size:14px;font-weight:bold;text-align:center;">USTHB — Enseignant</p>
    </div>
    <br><hr>
    <div style="display:flex;align-items:center;gap:10px;padding:0 15px;">
        <img src="img/prof.png" width="90" height="90"/>
        <span style="color:#ffffff;font-size:14px;"><?= htmlspecialchars(($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? '')) ?></span>
    </div>
    <hr><br>
    <a href="acceuille.php"           class="<?= $currentPage == 'acceuille.php' ? 'active' : '' ?>">Accueil</a>
    <a href="MesModules.php"          class="<?= $currentPage == 'MesModules.php' ? 'active' : '' ?>">Mes Modules</a>
    <a href="saisirlesnotes.php"      class="<?= $currentPage == 'saisirlesnotes.php' ? 'active' : '' ?>">Saisir les notes</a>
    <a href="listeDesEtudiants.php"   class="<?= $currentPage == 'listeDesEtudiants.php' ? 'active' : '' ?>">Liste des étudiants</a>
    <a href="logout.php"              class="<?= $currentPage == 'logout.php' ? 'active' : '' ?>">Déconnexion</a>
</div>

<!-- MAIN -->
<div class="main">

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <div class="header-icon">👥</div>
            <p style="color:#000;font-size:16px;">Liste des étudiants — <span style="color:#888;font-weight:300;">Enseignant</span></p>
        </div>
        <div class="year-badge"><?= $annee ?></div>
    </div>

    <!-- CONTENU -->
    <div class="page-content">

        <!-- FILTRES -->
        <form method="GET" action="" class="toolbar">
            <div class="toolbar-group">
                <label>Groupe</label>
                <select name="groupe" onchange="this.form.submit()" style="padding:8px 12px;border:1px solid #dde1ef;border-radius:8px;">
                    <option value="">Tous les groupes</option>
                    <?php foreach($groupes as $g): ?>
                        <option value="<?= $g['id_groupe'] ?>" <?= $groupe_choisi_id == $g['id_groupe'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g['nom_groupe']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="toolbar-sep"></div>

            <div class="toolbar-group" style="flex:1;">
                <input type="text" name="recherche" placeholder="Rechercher nom, prénom ou matricule..." value="<?= htmlspecialchars($recherche) ?>" style="width:100%;">
            </div>

            <div class="toolbar-group">
                <button type="submit" class="btn btn-secondary">Rechercher</button>
            </div>
        </form>

        <!-- TABLEAU -->
        <?php if (empty($etudiants)): ?>
            <div class="table-card">
                <div class="empty-state">Aucun étudiant trouvé.</div>
            </div>
        <?php else: ?>
            <div class="table-card">
                <div class="table-card-header">
                    Étudiants
                    <span class="count-badge"><?= $total ?> résultat<?= $total > 1 ? 's' : '' ?></span>
                </div>
                <table class="table1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Matricule</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Groupe</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($etudiants as $i => $etudiant): ?>
                        <tr>
                            <td style="color:#888;font-size:12px;"><?= $i + 1 ?></td>
                            <td class="matricule-cell"><?= htmlspecialchars($etudiant['matricule']) ?></td>
                            <td><strong><?= htmlspecialchars($etudiant['nom']) ?></strong></td>
                            <td><?= htmlspecialchars($etudiant['prenom']) ?></td>
                            <td><span class="groupe-tag"><?= htmlspecialchars($etudiant['nom_groupe']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>