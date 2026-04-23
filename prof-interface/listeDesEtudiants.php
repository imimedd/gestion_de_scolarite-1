<?php
require_once("../connexion.php");

$annee       = "2025 / 2026";
$currentPage = basename($_SERVER['PHP_SELF']);

session_start();
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

// Liste des ID de groupes valides pour l'enseignant afin de sécuriser la requête étudiante
$ids_groupes_valides = array_column($groupes, 'id_groupe');
if (empty($ids_groupes_valides)) {
    $ids_groupes_valides = [0]; // Aucun groupe
}
$in_groupes = implode(',', $ids_groupes_valides);

// Groupe choisi (par défaut : tous)
$groupe_choisi_id = $_GET['groupe'] ?? '';

// Recherche
$recherche = $_GET['recherche'] ?? '';

// Construire la requête pour les étudiants (seulement ceux des groupes de l'enseignant)
$sql = "SELECT e.matricule, e.nom, e.prenom, g.nom_groupe 
        FROM etudiants e 
        JOIN groupes g ON e.id_groupe = g.id_groupe
        WHERE e.id_groupe IN ($in_groupes)";
$params = [];
$conditions = [];

if (!empty($groupe_choisi_id)) {
    $conditions[] = "e.id_groupe = ?";
    $params[] = $groupe_choisi_id;
}

if (!empty($recherche)) {
    $conditions[] = "(e.nom LIKE ? OR e.prenom LIKE ? OR e.matricule LIKE ?)";
    $params[] = "%$recherche%";
    $params[] = "%$recherche%";
    $params[] = "%$recherche%";
}

if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY e.nom ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Compteur total
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
        table { width: 100%; border-collapse: collapse; margin-top: 24px; background: white; border-radius: 8px; overflow: hidden; }
        th { background: #1a2a4a; color: white; padding: 12px 16px; text-align: left; font-size: 13px; }
        td { padding: 10px 16px; border-bottom: 1px solid #eee; font-size: 14px; color: #000; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f5f8ff; }
        .no-data { color: #888; margin-top: 20px; font-size: 14px; }
        .badge-total {
            display: inline-block;
            background: #1a2a4a;
            color: white;
            font-size: 13px;
            padding: 4px 14px;
            border-radius: 20px;
            margin-top: 16px;
            font-family: 'IBM Plex Mono', monospace;
        }
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
    <div style="text-align: center;">
        <img src="img/usthb1.png" width="100" height="100"/>
        <p><font color="#ffffff"><font size="3"><center><b>usthb - Enseignant</b></center></font></font></p>
    </div>
    <br>
    <hr>
    <div style="display: flex; align-items: center;">
        <img src="img/prof.png" width="90" height="90"/><br>
        <center><font color="#ffffff"><font size="3"><?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?></font></font></center><br>
    </div>
    <hr>
    <br>
    <a href="index.php" class="<?php if($currentPage == 'index.php') echo 'active'; ?>">Accueil</a>
    <a href="MesModules.php" class="<?php if($currentPage == 'MesModules.php') echo 'active'; ?>">Mes Modules</a>
    <a href="saisirlesnotes.php" class="<?php if($currentPage == 'saisirlesnotes.php') echo 'active'; ?>">Saisir les notes</a>
    <a href="listeDesEtudiants.php" class="<?php if($currentPage == 'listeDesEtudiants.php') echo 'active'; ?>">Liste des étudiants</a>
    <a href="logout.php" class="<?php if($currentPage == 'logout.php') echo 'active'; ?>">Déconnexion</a>
</div>

<!-- MAIN -->
<div class="main">

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <div class="header-icon">👥</div>
            <p style="color: #000; font-size: 16px;">Liste des étudiants — <span style="color: #888; font-weight: 300;">Enseignant</span></p>
        </div>
        <div class="year-badge"><?php echo $annee; ?></div>
    </div>

    <!-- CONTENU -->
    <div class="content">

        <!-- FILTRES -->
        <form method="GET" action="">
            <div style="display: flex; gap: 24px; align-items: flex-end; flex-wrap: wrap;">

                <!-- GROUPE -->
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <div class="section-title">groupe</div>
                    <select name="groupe" onchange="this.form.submit()">
                        <option value="">Tous les groupes</option>
                        <?php foreach($groupes as $g): ?>
                            <option value="<?= $g['id_groupe'] ?>" <?= $groupe_choisi_id == $g['id_groupe'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g['nom_groupe']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- RECHERCHE -->
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <div class="section-title">recherche</div>
                    <input
                        type="text"
                        name="recherche"
                        placeholder="Nom, prénom ou matricule..."
                        value="<?= htmlspecialchars($recherche) ?>"
                        style="padding: 8px; border-radius: 4px; border: 1px solid #ccc; font-size: 14px; min-width: 200px;"
                    >
                </div>

                <!-- BOUTON RECHERCHE -->
                <div style="display: flex; flex-direction: column; gap: 4px; justify-content: flex-end;">
                    <button type="submit" style="padding: 8px 16px; background: #1a2a4a; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Rechercher
                    </button>
                </div>

            </div>
        </form>

        <!-- COMPTEUR -->
        <div class="badge-total"><?= $total ?> étudiant<?= $total > 1 ? 's' : '' ?> trouvé<?= $total > 1 ? 's' : '' ?></div>

        <!-- TABLEAU -->
        <?php if (empty($etudiants)): ?>
            <p class="no-data">Aucun étudiant trouvé.</p>
        <?php else: ?>
            <link rel="stylesheet" href="style.css">
        <table>
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
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($etudiant['matricule']) ?></td>
                    <td><?= htmlspecialchars($etudiant['nom']) ?></td>
                    <td><?= htmlspecialchars($etudiant['prenom']) ?></td>
                    <td><span class="groupe-tag"><?= htmlspecialchars($etudiant['nom_groupe']) ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

    </div>

</div>

</body>
</html>
