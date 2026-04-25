<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relevé de Notes-USTHB</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-logo">
        <img src="image/logo.jpg" alt="USTHB">
    </div>
    <ul class="nav-links">
        <li><a href="index.php">Accueil</a></li>
        <li><a href="index.php#apropos">À propos</a></li>
    </ul>
    <a href="login.php" class="btn-connexion">Connexion</a>
</nav>
<?php
include 'config.php';

$matricule = '242431461919'; // Matricule de l'étudiant connecté (exemple)

$sql = "SELECT * FROM etudiants WHERE matricule = '$matricule'";
$result = mysqli_query($conn, $sql);
$etudiant = mysqli_fetch_assoc($result);

$notes = [
    ['matiere' => 'Mathématiques', 'note' => 15, 'coefficient' => 3],
    ['matiere' => 'Informatique', 'note' => 18, 'coefficient' => 4],
    ['matiere' => 'Physique', 'note' => 12, 'coefficient' => 2],
    ['matiere' => 'Chimie', 'note' => 14, 'coefficient' => 2]
];
$total = 0;
$totalcoef = 0;
foreach ($notes as $n) {
    $total += $n['note'] * $n['coefficient'];
    $totalcoef += $n['coefficient'];
}
$moyenne = $total / $totalcoef;
?>
 <div class="dashboard">
    <div class="dashboard-header">
        <h2>Bonjour, <?php echo $etudiant['prenom']; ?> 👋</h2>
        <p>Voici votre espace étudiant</p>
    </div>
    <div class="dashboard-cards">
    <div class="card-dash">
        <h3>Informations personnelles</h3>
        <p><strong>Nom:</strong> <?php echo $etudiant['nom']; ?></p>
        <p><strong>Prénom:</strong> <?php echo $etudiant['prenom']; ?></p>
        <p><strong>Matricule:</strong> <?php echo $etudiant['matricule']; ?></p>
        <p><strong>Spécialité:</strong> <?php echo $etudiant['specialite']; ?></p>
        <p><strong>Niveau:</strong> <?php echo $etudiant['palier']; ?></p>
        <p><strong>Section:</strong> <?php echo $etudiant['section']; ?></p>
        <p><strong>Groupe TD:</strong> <?php echo $etudiant['groupe_td']; ?></p>
        <p><strong>Etat:</strong> <?php echo $etudiant['etat']; ?></p>
    </div>

    <!-- Carte 2: Moyenne générale -->
    <div class="card-dash">
        <h3>Moyenne générale</h3>
        <p class="moyenn-score"><?php echo number_format($moyenne,2);?> / 20</p>
        <p class="status">
            <?php if ($moyenne >= 10):?>
              <span class="admis">Admis</span>
            <?php else: ?>
              <span class="refuse">Ajourné</span>
            <?php endif; ?>
        </p>
    </div>
    <!-- Carte 3: Notes par matière -->
     <div class="card-dash">
        <h3>Notes</h3>
        <div class="tabs">
            <button class="tab active" onclick="showTab('s1')">Semestre 1</button>
            <button class="tab" onclick="showTab('s2')">Semestre 2</button>
        </div>
        <div id="s1" class="tab-content">
            <table class="notes-table">
                <tr>
                    <th>Matière</th>
                    <th>Note</th>
                    <th>Coefficient</th>
                </tr>
                <?php foreach ($notes as $n): ?>
                <tr>
                    <td><?php echo $n['matiere']; ?></td>
                    <td><?php echo $n['note']; ?></td>
                    <td><?php echo $n['coefficient']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div id="s2" class="tab-content" style="display:none;">
            <table class="notes-table">
                <tr>
                    <th>Matière</th>
                    <th>Note</th>
                    <th>Coefficient</th>
                </tr>
                <!-- Exemple de notes pour le semestre 2 -->
                <tr>
                    <td>Anglais</td>
                    <td>16</td>
                    <td>2</td>
                </tr>
                <tr>
                    <td>Gestion de projet</td>
                    <td>14</td>
                    <td>3</td>
                </tr>
            </table>
        </div>
     </div>
    </div>
  <div class="btn-download">
       <a href="releve.php" class="btn-download">Télécharger le relevé de notes</a> 
  </div>
</div>
<?php include 'footer.php'; ?>
 <script src="script.js"></script>
</body>
</html>
