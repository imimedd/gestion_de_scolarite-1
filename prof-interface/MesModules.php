<?php
session_start();
require_once("connexion.php");

$departement      = "Département Informatique — USTHB";
$annee            = "2025 / 2026";
$currentPage      = basename($_SERVER['PHP_SELF']);



// Get id_enseignant from matricule session
$matricule = $_SESSION['user_id'] ?? 0;
$stmtEns = $pdo->prepare("SELECT id_enseignant FROM enseignants WHERE matricule = ?");
$stmtEns->execute([$matricule]);
$id_enseignant = $stmtEns->fetchColumn();

// Fetch modules from DB assigned to this teacher
$stmt = $pdo->prepare("
    SELECT m.* 
    FROM modules m
    JOIN enseignant_module em ON m.id_module = em.id_module
    WHERE em.id_enseignant = ?
");
$stmt->execute([$id_enseignant]);
$modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
$nb_modules = count($modules);

// Total distinct groups for this teacher's modules
$stmtGrp = $pdo->prepare("
    SELECT COUNT(DISTINCT mg.id_groupe)
    FROM module_groupe mg
    JOIN enseignant_module em ON mg.id_module = em.id_module
    WHERE em.id_enseignant = ?
");
$stmtGrp->execute([$id_enseignant]);
$nb_groupes = $stmtGrp->fetchColumn();

// We don't have coef in DB, so we simulate a total
$coef_total = $nb_modules * 3; 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord — Enseignant</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
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
        <center><font color="#ffffff"><font size="3"><?= $_SESSION['prenom'] . ' ' . $_SESSION['nom'] ?></font></font></center><br>
    </div>
    <hr>
    <br>
    <a href="index.php" class="<?php if($currentPage == 'index.php') echo 'active'; ?>">Accueil</a>
    <a href="MesModules.php" class="<?php if($currentPage == 'MesModules.php') echo 'active'; ?>">Mes Modules</a>
    <a href="saisirlesnotes.php" class="<?php if($currentPage == 'saisirlesnotes.php') echo 'active'; ?>">Saisir les notes</a>
    <a href="listeDesEtudiants.php" class="<?php if($currentPage == 'listeDesEtudiants.php') echo 'active'; ?>">Liste des étudiants</a>
    <a href="logout.php" class="<?php if($currentPage == 'logout.php') echo 'active'; ?>">Déconnexion</a>
</div>

<!-- CONTENU -->
<div style="flex: 1;">

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <div class="header-icon">🎓</div>
            <p><font color="#000000"><font size="4">Tableau de bord — <span>Enseignant</span></font></font></p>
        </div>
        <div class="year-badge"><?php echo $annee; ?></div>
    </div>

    <!-- MAIN -->
    <div class="main">

        <!-- PROF CARD -->
        <div class="prof-card">
            <div class="prof-info">
                <p><?php echo $departement; ?></p>
            </div>
            <div class="prof-stats">
                <div class="stat">
                    <div class="num"><?php echo $nb_modules; ?></div>
                    <div class="label">Modules</div>
                </div>
                <div class="stat">
                    <div class="num"><?php echo $nb_groupes; ?></div>
                    <div class="label">Groupes</div>
                </div>
                <div class="stat">
                    <div class="num"><?php echo $coef_total; ?></div>
                    <div class="label">Coef total</div>
                </div>
            </div>
        </div>

        <!-- MODULES -->
        <div class="section-title"><font color="#0000">Mes modules assignés</font></div>

        <div class="modules-grid">

            <?php foreach ($modules as $mod): 
                // Generate realistic but deterministic mockup data for missing DB fields
                $niveau = "L" . (($mod['id_module'] % 3) + 1) . " Informatique";
                $semestre = "Semestre " . (($mod['id_module'] % 2) + 1);
                $coef = 3; // Static coefficient mock
                
                // Fetch groups for this module to show in the badge
                $stmtGroups = $pdo->prepare("SELECT g.nom_groupe FROM module_groupe mg JOIN groupes g ON mg.id_groupe = g.id_groupe WHERE mg.id_module = ?");
                $stmtGroups->execute([$mod['id_module']]);
                $groupesAssocies = $stmtGroups->fetchAll(PDO::FETCH_COLUMN);
                
                // Format the groups (e.g., G1, G2)
                $groupesHTML = !empty($groupesAssocies) ? implode(', ', array_map(function($g) { return str_replace('Groupe ', 'G', $g); }, $groupesAssocies)) : "Aucun";
            ?>
            <div class="module-card">
                <div class="module-code-badge"><?php echo htmlspecialchars($mod['code_module']); ?></div>
                <div class="module-name"><?php echo htmlspecialchars($mod['nom_module']); ?></div>
                <div class="module-tags">
                    <div class="tag"><div class="dot"></div><?php echo $niveau; ?></div>
                    <div class="tag"><div class="dot"></div><?php echo $semestre; ?></div>
                </div>
                <div class="module-footer">
                    <div class="coef">Coeff. <span><?php echo $coef; ?></span></div>
                    <div class="groupe-badge"><?php echo htmlspecialchars($groupesHTML); ?></div>
                </div>
            </div>
            <?php endforeach; ?>

        </div><!-- /modules-grid -->

    </div><!-- /main -->

</div><!-- /flex -->

</body>
</html>