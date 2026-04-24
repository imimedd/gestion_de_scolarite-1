<?php
session_start();
require_once("../connexion.php");

$annee       = date("Y") . " / " . (date("Y") + 1);
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil — Administrateur</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../prof-interface/style.css">
    <style>
        .welcome-card {
            background: linear-gradient(135deg, #1a2a4a 0%, #2c3e6a 100%);
            color: white;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(26, 42, 74, 0.15);
        }
        .welcome-card h2 { margin-bottom: 12px; font-size: 26px; font-weight: 700; }
        .welcome-card p { opacity: 0.9; font-size: 15px; max-width: 600px; line-height: 1.6; }
        
        .quick-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; margin-top: 10px; }
        .ql-card {
            background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px;
            transition: all 0.3s ease; text-decoration: none; color: #1a2a4a;
            display: flex; flex-direction: column; gap: 12px;
        }
        .ql-card:hover {
            transform: translateY(-5px); box-shadow: 0 12px 24px rgba(0,0,0,0.06); border-color: #cbd5e1;
        }
        .ql-icon { font-size: 32px; margin-bottom: 4px; }
        .ql-title { font-weight: 600; font-size: 17px; }
        .ql-desc { font-size: 13.5px; color: #64748b; line-height: 1.5; }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div style="text-align: center;">
        <img src="img/usthb1.png" width="100" height="100"/>
        <p><font color="#ffffff"><font size="3"><center><b>usthb - Administrateur</b></center></font></font></p>
    </div>
    <br>
    <hr>
    <div style="display: flex; align-items: center;">
        <img src="img/prof.png" width="90" height="90"/><br>
        <center><font color="#ffffff"><font size="3"><?= $_SESSION['prenom'] . ' ' . $_SESSION['nom'] ?></font></font></center><br>
    </div>
    <hr>
    <br>
    <a href="acceuille1.php" class="<?php if($currentPage == 'acceuille1.php') echo 'active'; ?>">Accueil</a>
    <a href="Gestion des modules.php" class="<?php if($currentPage == 'Gestion des modules.php') echo 'active'; ?>">Gérer les utilisateurs</a>
    <a href="Gestion des notes.php" class="<?php if($currentPage == 'Gestion des notes.php') echo 'active'; ?>">Gérer les modules</a>
    <a href="Gestion_des_enseignants.php" class="<?php if($currentPage == 'Gestion_des_enseignants.php') echo 'active'; ?>">Gérer les groupes</a>
    <a href="Gestiondesétudiants.php" class="<?php if($currentPage == 'Gestiondesétudiants.php') echo 'active'; ?>">Gérer les étudiants</a>
    <a href="logout.php" class="<?php if($currentPage == 'logout.php') echo 'active'; ?>">Déconnexion</a>
</div>

<!-- MAIN -->
<div class="main">

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <div class="header-icon">🏠</div>
            <p style="color: #000; font-size: 16px;">Accueil — <span style="color: #888; font-weight: 300;">Administrateur</span></p>
        </div>
        <div class="year-badge"><?php echo $annee; ?></div>
    </div>

    <!-- CONTENU -->
    <div class="content">
        <div class="welcome-card">
            <h2>Bienvenue sur votre espace administrateur</h2>
            <p>Gérez les utilisateurs, les modules, les groupes et les étudiants rapidement et en toute sécurité sur la plateforme académique de l'USTHB.</p>
        </div>

        <div class="section-title">accès rapide</div>

        <div class="quick-links">
            <a href="Gestion des modules.php" class="ql-card">
                <div class="ql-icon">👥</div>
                <div class="ql-title">Gérer les utilisateurs</div>
                <div class="ql-desc">Ajoutez, modifiez ou supprimez les comptes administrateurs et enseignants de la plateforme.</div>
            </a>

            <a href="Gestion des notes.php" class="ql-card">
                <div class="ql-icon">📚</div>
                <div class="ql-title">Gérer les modules</div>
                <div class="ql-desc">Consultez et administrez les modules, leurs coefficients et les affectations aux groupes.</div>
            </a>

            <a href="Gestion_des_enseignants.php" class="ql-card">
                <div class="ql-icon">👨‍🏫</div>
                <div class="ql-title">Gérer les groupes</div>
                <div class="ql-desc">Organisez les groupes TD et TP, et associez les étudiants et enseignants correspondants.</div>
            </a>

            <a href="Gestiondesétudiants.php" class="ql-card">
                <div class="ql-icon">🎓</div>
                <div class="ql-title">Gérer les étudiants</div>
                <div class="ql-desc">Parcourez et gérez la liste complète des étudiants inscrits par section, palier et spécialité.</div>
            </a>
        </div>
    </div>

</div>

</body>
</html>