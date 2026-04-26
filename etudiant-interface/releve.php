<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relevé de Notes-USTHB</title>
    <link rel="stylesheet" href="../prof-interface/style.css?v=<?php echo time(); ?>">
</head>
<body>
<div class="sidebar">
    <div class="sidebar-logo" style="text-align: center; margin-bottom: 20px;">
        <img src="../image/logo.jpg" alt="USTHB" style="width: 80px; border-radius: 50%;">
        <p style="color: #fff; font-weight: 600; margin-top: 10px;">Espace Étudiant</p>
    </div>
    <div class="sidebar-nav">
        <a href="dashboard etudiants.php">Tableau de bord</a>
        <a href="releve.php" class="active">Relevé de notes</a>
        <a href="../index.php" class="btn-deconnexion">Déconnexion</a>
    </div>
</div>

<div class="main">
    <div class="header">
        <div class="header-left">
            <div class="header-icon">📄</div>
            <p style="color:#000; font-size:16px;">Relevé de notes</p>
        </div>
        <div class="year-badge">2025 / 2026</div>
    </div>
    <div class="page-content">
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
        <img src="../image/logo.jpg" alt="USTHB" class="releve-logo">
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
        <a href="dashboard etudiants.php" class="btn-retour">⬅️ Retour</a>
    </div>

</div>

    </div>
    <?php include '../footer.php'; ?>
</div>

</body>
</html>