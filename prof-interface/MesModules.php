<?php
session_start();
require_once("../connexion.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$departement      = "Département Informatique — USTHB";
$annee            = date("Y") . " / " . (date("Y") + 1);
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

// We don't have coef in DB natively linked to teacher in this query, simulate a total
$coef_total = 0;
foreach ($modules as $mod) {
    $coef_total += isset($mod['coef']) ? $mod['coef'] : 3;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Modules — Enseignant</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* MODULES STYLES */
        .prof-card {
            background: #ffffff;
            border: 1px solid #e0e4ef;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }
        .prof-info p {
            font-size: 16px;
            font-weight: 600;
            color: #1a2a4a;
        }
        .prof-stats {
            display: flex;
            gap: 30px;
        }
        .stat {
            text-align: center;
        }
        .stat .num {
            font-size: 24px;
            font-weight: 700;
            font-family: 'IBM Plex Mono', monospace;
            color: #378ADD;
        }
        .stat .label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 4px;
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .module-card {
            background: #ffffff;
            border: 1px solid #e0e4ef;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
        }
        .module-card:hover {
            box-shadow: 0 8px 25px rgba(26,42,74,0.06);
            border-color: #cbd5e1;
            transform: translateY(-3px);
        }
        .module-code-badge {
            background: #f0f4ff;
            color: #378ADD;
            font-size: 11px;
            font-family: 'IBM Plex Mono', monospace;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
            align-self: flex-start;
            margin-bottom: 12px;
        }
        .module-name {
            font-size: 16px;
            font-weight: 600;
            color: #1a2a4a;
            margin-bottom: 16px;
            flex-grow: 1;
        }
        .module-tags {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }
        .module-tags .tag {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #64748b;
        }
        .module-tags .dot {
            width: 6px;
            height: 6px;
            background: #cbd5e1;
            border-radius: 50%;
        }
        .module-footer {
            border-top: 1px solid #f1f5f9;
            padding-top: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .module-footer .coef {
            font-size: 12px;
            color: #64748b;
        }
        .module-footer .coef span {
            font-weight: 600;
            color: #1a2a4a;
        }
        .groupe-badge {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div style="text-align: center;">
        <img src="img/usthb1.png" width="100" height="100"/>
        <p><font color="#ffffff"><font size="3"><center><b>USTHB — Enseignant</b></center></font></font></p>
    </div>
    <br><hr>
    <div style="display: flex; align-items: center;">
        <img src="img/prof.png" width="90" height="90"/>
        <center><font color="#ffffff"><font size="3"><?= htmlspecialchars(($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? '')) ?></font></font></center>
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
            <div class="header-icon">🎓</div>
            <p style="color:#000; font-size:16px;">Mes modules — <span style="color:#888; font-weight:300;">Enseignant</span></p>
        </div>
        <div class="year-badge"><?= $annee ?></div>
    </div>

    <!-- PAGE CONTENT -->
    <div class="page-content">

        <!-- PROF CARD -->
        <div class="prof-card">
            <div class="prof-info">
                <p><?= $departement ?></p>
            </div>
            <div class="prof-stats">
                <div class="stat">
                    <div class="num"><?= $nb_modules ?></div>
                    <div class="label">Modules</div>
                </div>
                <div class="stat">
                    <div class="num"><?= $nb_groupes ?></div>
                    <div class="label">Groupes</div>
                </div>
                <div class="stat">
                    <div class="num"><?= $coef_total ?></div>
                    <div class="label">Coef total</div>
                </div>
            </div>
        </div>

        <div class="section-title">Mes modules assignés</div>

        <?php if ($nb_modules === 0): ?>
            <div class="empty-state">
                Vous n'avez aucun module assigné pour l'instant.
            </div>
        <?php else: ?>
            <div class="modules-grid">
                <?php foreach ($modules as $mod): 
                    $niveau = isset($mod['niveau']) ? $mod['niveau'] : ("L" . (($mod['id_module'] % 3) + 1) . " Informatique");
                    $semestre = isset($mod['semestre']) ? $mod['semestre'] : ("Semestre " . (($mod['id_module'] % 2) + 1));
                    $coef = isset($mod['coef']) ? $mod['coef'] : 3;
                    
                    $stmtGroups = $pdo->prepare("SELECT g.nom_groupe FROM module_groupe mg JOIN groupes g ON mg.id_groupe = g.id_groupe WHERE mg.id_module = ?");
                    $stmtGroups->execute([$mod['id_module']]);
                    $groupesAssocies = $stmtGroups->fetchAll(PDO::FETCH_COLUMN);
                    
                    $groupesHTML = !empty($groupesAssocies) ? implode(', ', array_map(function($g) { return str_replace('Groupe ', 'G', $g); }, $groupesAssocies)) : "Aucun";
                ?>
                <div class="module-card">
                    <div class="module-code-badge"><?= htmlspecialchars($mod['code_module'] ?? 'MOD') ?></div>
                    <div class="module-name"><?= htmlspecialchars($mod['nom_module'] ?? 'Module Inconnu') ?></div>
                    <div class="module-tags">
                        <div class="tag"><div class="dot"></div><?= htmlspecialchars($niveau) ?></div>
                        <div class="tag"><div class="dot"></div><?= htmlspecialchars($semestre) ?></div>
                    </div>
                    <div class="module-footer">
                        <div class="coef">Coeff. <span><?= $coef ?></span></div>
                        <div class="groupe-badge"><?= htmlspecialchars($groupesHTML) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>