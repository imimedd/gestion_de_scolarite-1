<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USTHB-gestion de la scolarité</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-logo">
            <img src="image/logo.jpg" alt="USTHB"> 
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="apropose.php">A propos</a></li>
        </ul>
        <a href="login.php" class="btn-connexion">Connexion</a>
    </nav>
    <main>
        <section class="hero">
            <div class="hero-content">
            <h1>Bienvenue à l'USTHB</h1>
            <p>Système intégré de gestion de la scolarité de l'USTHB.
Étudiants, enseignants et administrateurs — accédez à vos espaces, 
consultez les notes, gérez les modules et suivez les résultats académiques</p>
                <div class="login-cards">
                  <a href="login.php?role=etudiant" class="login-card">
                     <img src="image/etudiant.jpg" alt="Étudiant">
                     <h3>Étudiant</h3>
                   </a>
                  <a href="login.php?role=enseignant" class="login-card">
                      <img src="image/enseignant.jpg" alt="Enseignant">
                     <h3>Enseignant</h3>
                   </a>
                  <a href="login.php?role=admin" class="login-card">
                      <img src="image/admin.jpg" alt="Administrateur">
                      <h3>Administrateur</h3>
                  </a>
                </div>
        </section>
        <section class="projet-info" id="apropos">
    <p>Projet N° 31 — Réalisé par :</p>
    <p>  SLIMANI IMAD ·BOUKTITE	MOHAMED ADAM · MEDDOUR IMENE· BOUKHALFA	LINA HADIL</p>
</section>
        <?php include 'footer.php'; ?>
    </main>
</body>
</html>