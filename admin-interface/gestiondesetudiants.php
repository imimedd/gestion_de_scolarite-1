<?php
session_start();
require_once("connexion.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$editMode = false;
if (isset($_GET['edit'])) {
    $editMode = true;
    $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE numero=?");
    $stmt->execute([$_GET['edit']]);
    $etudiant = $stmt->fetch();
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM etudiants WHERE numero=?")->execute([$_GET['delete']]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matricule  = $_POST['matricule'];
    $nom        = $_POST['nom'];
    $prenom     = $_POST['prenom'];
    $palier     = $_POST['palier'];
    $specialite = $_POST['specialite'];
    $section    = $_POST['section'];
    $etat       = $_POST['etat'];
    $groupe_td  = $_POST['groupe_td'];

    if (!empty($_POST['numero'])) {
        $pdo->prepare("UPDATE etudiants SET matricule=?, nom=?, prenom=?, palier=?, specialite=?, section=?, etat=?, groupe_td=? WHERE numero=?")
            ->execute([$matricule, $nom, $prenom, $palier, $specialite, $section, $etat, $groupe_td, $_POST['numero']]);
    } else {
        $pdo->prepare("INSERT INTO etudiants (matricule, nom, prenom, palier, specialite, section, etat, groupe_td) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")
            ->execute([$matricule, $nom, $prenom, $palier, $specialite, $section, $etat, $groupe_td]);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Étudiants</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <!-- même sidebar que les autres pages -->
</div>

<div class="main">
    <div class="content">

        <h2><?= $editMode ? "Modifier" : "Ajouter" ?> étudiant</h2>

        <form method="POST">
            <input type="hidden" name="numero" value="<?= $editMode ? $etudiant['numero'] : '' ?>">
            <input type="text" name="matricule" placeholder="Matricule" value="<?= $editMode ? $etudiant['matricule'] : '' ?>" required>
            <input type="text" name="nom" placeholder="Nom" value="<?= $editMode ? $etudiant['nom'] : '' ?>" required>
            <input type="text" name="prenom" placeholder="Prénom" value="<?= $editMode ? $etudiant['prenom'] : '' ?>" required>
            <input type="text" name="palier" placeholder="Palier (ex: L2)" value="<?= $editMode ? $etudiant['palier'] : '' ?>" required>
            <input type="text" name="specialite" placeholder="Spécialité (ex: ISIL)" value="<?= $editMode ? $etudiant['specialite'] : '' ?>" required>
            <input type="text" name="section" placeholder="Section (ex: C)" value="<?= $editMode ? $etudiant['section'] : '' ?>" required>
            <input type="text" name="etat" placeholder="État (ex: ADM)" value="<?= $editMode ? $etudiant['etat'] : '' ?>" required>
            <input type="number" name="groupe_td" placeholder="Groupe TD" value="<?= $editMode ? $etudiant['groupe_td'] : '' ?>" required>
            <button type="submit"><?= $editMode ? "Modifier" : "Ajouter" ?></button>
        </form>

        <hr>

        <!-- RECHERCHE -->
        <form method="GET">
            <input type="text" name="search" placeholder="Rechercher par nom ou matricule...">
            <button type="submit">Rechercher</button>
        </form>

        <!-- TABLEAU -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Matricule</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Palier</th>
                    <th>Spécialité</th>
                    <th>Section</th>
                    <th>État</th>
                    <th>Groupe TD</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($_GET['search'])) {
                $search = "%" . $_GET['search'] . "%";
                $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE nom LIKE ? OR matricule LIKE ?");
                $stmt->execute([$search, $search]);
            } else {
                $stmt = $pdo->query("SELECT * FROM etudiants");
            }

            $count = 0;
            while ($row = $stmt->fetch()) {
                $count++;
                echo "<tr>
                    <td>{$row['numero']}</td>
                    <td>{$row['matricule']}</td>
                    <td>{$row['nom']}</td>
                    <td>{$row['prenom']}</td>
                    <td>{$row['palier']}</td>
                    <td>{$row['specialite']}</td>
                    <td>{$row['section']}</td>
                    <td>{$row['etat']}</td>
                    <td>{$row['groupe_td']}</td>
                    <td>
                        <a href='?edit={$row['numero']}'>Modifier</a>
                        <a href='?delete={$row['numero']}'>Supprimer</a>
                    </td>
                </tr>";
            }

            if ($count == 0) {
                echo "<tr><td colspan='10'>Aucun étudiant trouvé.</td></tr>";
            }
            ?>
            </tbody>
        </table>

    </div>
</div>

</body>
</html>