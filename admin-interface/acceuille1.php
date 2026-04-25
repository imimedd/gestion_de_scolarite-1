<?php
session_start();
require_once("../connexion.php");

$annee       = date("Y") . " / " . (date("Y") + 1);
$currentPage = basename($_SERVER['PHP_SELF']);

// Statistiques
$nb_etudiants   = $pdo->query("SELECT COUNT(*) FROM etudiants")->fetchColumn();
$nb_modules     = $pdo->query("SELECT COUNT(*) FROM modules")->fetchColumn();
$nb_enseignants = $pdo->query("SELECT COUNT(*) FROM enseignants")->fetchColumn();

// Étudiants récents
$etudiants = $pdo->query("SELECT numero, nom, prenom, groupe_td, section, etat FROM etudiants ORDER BY numero DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil — Administrateur</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-content {
            padding: 30px;
            background: #f4f6fb;
            flex: 1;
            color: #000;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: #ffffff;
            border: 1px solid #e0e4ef;
            border-radius: 12px;
            padding: 20px;
        }
        .stat-card .stat-label {
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
        }
        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 600;
            color: #1a2a4a;
            font-family: 'IBM Plex Mono', monospace;
        }
        .stat-card .stat-sub {
            font-size: 12px;
            color: #999;
            margin-top: 4px;
        }
        .stat-card.blue   { border-top: 3px solid #378ADD; }
        .stat-card.teal   { border-top: 3px solid #1D9E75; }
        .stat-card.purple { border-top: 3px solid #7F77DD; }

        .table-card {
            background: #ffffff;
            border: 1px solid #e0e4ef;
            border-radius: 12px;
            overflow: hidden;
        }
        .table-card-header {
            padding: 14px 20px;
            font-size: 14px;
            font-weight: 600;
            color: #1a2a4a;
            border-bottom: 1px solid #e0e4ef;
        }
        .table-card table { margin-top: 0; border-radius: 0; }
        .table-card th {
            background: #f4f6fb;
            color: #555;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .badge-actif {
            display: inline-block;
            background: #e6f4ea;
            color: #2d7a3a;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-inactif {
            display: inline-block;
            background: #f1efea;
            color: #5f5e5a;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
    </style>
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
        <center><font color="#ffffff"><font size="3"><?= $_SESSION['role'] ?></font></font></center>
    </div>
    <hr><br>
    <a href="acceuille1.php"              class="<?php if($currentPage == 'acceuille1.php') echo 'active'; ?>">Accueil</a>
    <a href="Gestion des modules.php"     class="<?php if($currentPage == 'Gestion des modules.php') echo 'active'; ?>">Gérer les modules</a>
    <a href="Gestion des notes.php"       class="<?php if($currentPage == 'Gestion_des_notes.php') echo 'active'; ?>">Gérer les notes</a>
    <a href="Gestion_des_enseignants.php" class="<?php if($currentPage == 'Gestion_des_enseignants.php') echo 'active'; ?>">Gérer les enseignants</a>
    <a href="gestiondesetudiants.php"     class="<?php if($currentPage == 'gestiondesetudiants.php') echo 'active'; ?>">Gérer les étudiants</a>
    <a href="logout.php"                  class="<?php if($currentPage == 'logout.php') echo 'active'; ?>">Déconnexion</a>
</div>

<!-- MAIN -->
<div class="main">

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <div class="header-icon">🏠</div>
            <p style="color: #000; font-size: 16px;">Accueil — <span style="color: #888; font-weight: 300;">Administrateur</span></p>
        </div>
        <div class="year-badge"><?= $annee ?></div>
    </div>

    <!-- CONTENU -->
    <div class="dashboard-content">

        <div class="section-title">statistiques</div>
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-label">Nombre total d'étudiants</div>
                <div class="stat-value"><?= $nb_etudiants ?></div>
                <div class="stat-sub">Tous groupes confondus</div>
            </div>
            <div class="stat-card teal">
                <div class="stat-label">Nombre de modules</div>
                <div class="stat-value"><?= $nb_modules ?></div>
                <div class="stat-sub">Semestre en cours</div>
            </div>
            <div class="stat-card purple">
                <div class="stat-label">Nombre d'enseignants</div>
                <div class="stat-value"><?= $nb_enseignants ?></div>
                <div class="stat-sub">Actifs cette année</div>
            </div>
        </div>

        <div class="table-card">
            <div class="table-card-header">Étudiants récents</div>
            <table class="table2">
                <thead>
                    <tr>
                        <th>Numéro</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Groupe TD</th>
                        <th>Section</th>
                        <th>État</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($etudiants as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['numero']) ?></td>
                        <td><?= htmlspecialchars($e['nom']) ?></td>
                        <td><?= htmlspecialchars($e['prenom']) ?></td>
                        <td><?= htmlspecialchars($e['groupe_td']) ?></td>
                        <td><?= htmlspecialchars($e['section']) ?></td>
                        <td>
                            <?php if(strtolower($e['etat']) === 'actif'): ?>
                                <span class="badge-actif">Actif</span>
                            <?php else: ?>
                                <span class="badge-inactif">Inactif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</body>
</html>