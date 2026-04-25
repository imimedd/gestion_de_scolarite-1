<<<<<<< HEAD
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relevé de Notes-USTHB</title>
    <link rel="stylesheet" href="prof-interface/style.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-logo">
        <img src="images/logo.jpg" alt="USTHB">
    </div>
    <ul class="nav-links">
        <li><a href="index.php">Accueil</a></li>
        <li><a href="index.php#apropos">À propos</a></li>
    </ul>
    <a href="login.php" class="btn-connexion">Connexion</a>
</nav>
<?php
$etudiant = [
    'nom' => 'Doe',
    'prenom' => 'John',
    'matricule' => '123456',
    'niveau' => 'L2 INFO',
    'annee' => '2025/2026'
];

$notes = [
    ['module' => 'Algorithmique',  'note' => 12, 'coefficient' => 4],
    ['module' => 'Base de données','note' => 14, 'coefficient' => 3],
    ['module' => 'Prog. Web',      'note' => 18, 'coefficient' => 3],
    ['module' => 'Réseaux',        'note' => 15, 'coefficient' => 3],
    ['module' => 'Mathématiques',  'note' => 11, 'coefficient' => 4],
];

$total = 0;
$totalCoef = 0;
foreach ($notes as $n) {
    $total += $n['note'] * $n['coefficient'];
    $totalCoef += $n['coefficient'];
}
$moyenne = $total / $totalCoef;
?>

<div class="releve-container">

     <div class="releve-header">
        <img src="images/logo.jpg" alt="USTHB" class="releve-logo">
        <div class="releve-title">
            <h2>Relevé de Notes</h2>
            <p>Université des Sciences et de la Technologie Houari Boumediene</p>
        </div>
    </div>

    <div class="releve-info">
        <p><strong>Nom :</strong> <?php echo $etudiant['nom']; ?></p>
        <p><strong>Prénom :</strong> <?php echo $etudiant['prenom']; ?></p>
        <p><strong>Matricule :</strong> <?php echo $etudiant['matricule']; ?></p>
        <p><strong>Niveau :</strong> <?php echo $etudiant['niveau']; ?></p>
        <p><strong>Année :</strong> <?php echo $etudiant['annee']; ?></p>
    </div>

    <table class="releve-table">
        <thead>
            <tr>
                <th>Module</th>
                <th>Note</th>
                <th>Coefficient</th>
                <th>Note × Coef</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($notes as $n): ?>
            <tr>
                <td><?php echo $n['module']; ?></td>
                <td><?php echo $n['note']; ?> / 20</td>
                <td><?php echo $n['coefficient']; ?></td>
                <td><?php echo $n['note'] * $n['coefficient']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="releve-moyenne">
        <p>Moyenne générale :
            <span class="moyenne-val">
                <?php echo number_format($moyenne, 2); ?> / 20
            </span>
        </p>
        <p class="statut">
            <?php if ($moyenne >= 10): ?>
                <span class="admis">✅ Admis</span>
            <?php else: ?>
                <span class="refuse">❌ Ajourné</span>
            <?php endif; ?>
        </p>
    </div>

    <div class="releve-btns">
        <button onclick="window.print()" class="btn-print">🖨️ Imprimer</button>
        <a href="dashboard_etudiant.php" class="btn-retour">⬅️ Retour</a>
    </div>

</div>

<?php include 'footer.php'; ?>

</body>
=======
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relevé de Notes-USTHB</title>
    <link rel="stylesheet" href="prof-interface/style.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-logo">
        <img src="images/logo.jpg" alt="USTHB">
    </div>
    <ul class="nav-links">
        <li><a href="index.php">Accueil</a></li>
        <li><a href="index.php#apropos">À propos</a></li>
    </ul>
    <a href="login.php" class="btn-connexion">Connexion</a>
</nav>
<?php
$etudiant = [
    'nom' => 'Doe',
    'prenom' => 'John',
    'matricule' => '123456',
    'niveau' => 'L2 INFO',
    'annee' => '2025/2026'
];

$notes = [
    ['module' => 'Algorithmique',  'note' => 12, 'coefficient' => 4],
    ['module' => 'Base de données','note' => 14, 'coefficient' => 3],
    ['module' => 'Prog. Web',      'note' => 18, 'coefficient' => 3],
    ['module' => 'Réseaux',        'note' => 15, 'coefficient' => 3],
    ['module' => 'Mathématiques',  'note' => 11, 'coefficient' => 4],
];

$total = 0;
$totalCoef = 0;
foreach ($notes as $n) {
    $total += $n['note'] * $n['coefficient'];
    $totalCoef += $n['coefficient'];
}
$moyenne = $total / $totalCoef;
?>

<div class="releve-container">

     <div class="releve-header">
        <img src="images/logo.jpg" alt="USTHB" class="releve-logo">
        <div class="releve-title">
            <h2>Relevé de Notes</h2>
            <p>Université des Sciences et de la Technologie Houari Boumediene</p>
        </div>
    </div>

    <div class="releve-info">
        <p><strong>Nom :</strong> <?php echo $etudiant['nom']; ?></p>
        <p><strong>Prénom :</strong> <?php echo $etudiant['prenom']; ?></p>
        <p><strong>Matricule :</strong> <?php echo $etudiant['matricule']; ?></p>
        <p><strong>Niveau :</strong> <?php echo $etudiant['niveau']; ?></p>
        <p><strong>Année :</strong> <?php echo $etudiant['annee']; ?></p>
    </div>

    <table class="releve-table">
        <thead>
            <tr>
                <th>Module</th>
                <th>Note</th>
                <th>Coefficient</th>
                <th>Note × Coef</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($notes as $n): ?>
            <tr>
                <td><?php echo $n['module']; ?></td>
                <td><?php echo $n['note']; ?> / 20</td>
                <td><?php echo $n['coefficient']; ?></td>
                <td><?php echo $n['note'] * $n['coefficient']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="releve-moyenne">
        <p>Moyenne générale :
            <span class="moyenne-val">
                <?php echo number_format($moyenne, 2); ?> / 20
            </span>
        </p>
        <p class="statut">
            <?php if ($moyenne >= 10): ?>
                <span class="admis">✅ Admis</span>
            <?php else: ?>
                <span class="refuse">❌ Ajourné</span>
            <?php endif; ?>
        </p>
    </div>

    <div class="releve-btns">
        <button onclick="window.print()" class="btn-print">🖨️ Imprimer</button>
        <a href="dashboard_etudiant.php" class="btn-retour">⬅️ Retour</a>
    </div>

</div>

<?php include 'footer.php'; ?>

</body>
>>>>>>> 2932a0bf2df97e2007ef5a885fb58c4eb10562d5
</html>