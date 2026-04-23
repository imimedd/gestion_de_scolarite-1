<?php
session_start();
require_once("connexion.php");

$annee       = "2025 / 2026";
$currentPage = basename($_SERVER['PHP_SELF']);

// Récupérer les modules depuis la BDD
$modules = $pdo->query("SELECT id_module, nom_module FROM modules")->fetchAll(PDO::FETCH_ASSOC);

// Module choisi
$module_choisi_id = $_GET['module'] ?? ($modules[0]['id_module'] ?? 1);

// Récupérer les groupes liés au module choisi
$stmt = $pdo->prepare("
    SELECT g.id_groupe, g.nom_groupe 
    FROM groupes g
    JOIN module_groupe mg ON g.id_groupe = mg.id_groupe
    WHERE mg.id_module = ?
");
$stmt->execute([$module_choisi_id]);
$groupes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Groupe choisi
$groupe_choisi_id = $_GET['groupe'] ?? ($groupes[0]['id_groupe'] ?? null);

// Recherche
$recherche = $_GET['recherche'] ?? '';

// Récupérer les étudiants du groupe choisi
$etudiants = [];
if ($groupe_choisi_id) {
    $sql = "SELECT matricule, nom, prenom FROM etudiants WHERE groupe_td = ?";
    $params = [$groupe_choisi_id];

    if (!empty($recherche)) {
        $sql .= " AND (nom LIKE ? OR prenom LIKE ? OR matricule LIKE ?)";
        $params[] = "%$recherche%";
        $params[] = "%$recherche%";
        $params[] = "%$recherche%";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Charger les notes existantes depuis la BDD
$notes_existantes = [];
if ($groupe_choisi_id && !empty($etudiants)) {
    // Récupérer les évaluations pour ce module et ce groupe
   $stmt = $pdo->prepare("
    SELECT e.id_evaluation, e.type_eval, n.id_etudiant, n.note, et.matricule
    FROM evaluations e
    JOIN notes n ON n.id_evaluation = e.id_evaluation
    JOIN etudiants et ON et.numero = n.id_etudiant
    WHERE e.id_module = ? AND e.id_groupe = ?
");
    $stmt->execute([$module_choisi_id, $groupe_choisi_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Organiser les notes par matricule et type
    $type_map = [
        'Contrôle 1'   => 'cc1',
        'Contrôle 2'   => 'cc2',
        'TP'           => 'tp',
        'Examen final' => 'examen'
    ];

    foreach ($rows as $row) {
        $mat = $row['matricule'];
        $key = $type_map[$row['type_eval']] ?? null;
        if ($key) {
            $notes_existantes[$mat][$key] = $row['note'];
        }
    }
}

$success = isset($_GET['success']) && $_GET['success'] == 1;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Saisir les notes — Enseignant</title>
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

<!-- MAIN -->
<div class="main">

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <div class="header-icon">📄</div>
            <p style="color: #000; font-size: 16px;">Saisir les notes — <span style="color: #888; font-weight: 300;">Enseignant</span></p>
        </div>
        <div class="year-badge"><?php echo $annee; ?></div>
    </div>

    <!-- FILTRES -->
    <div class="content">

        <?php if ($success): ?>
            <div class="success">✅ Notes enregistrées avec succès !</div>
        <?php endif; ?>

        <form method="GET" action="">
            <div style="display: flex; gap: 24px; align-items: flex-end; flex-wrap: wrap;">

                <!-- MODULE -->
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <div class="section-title">modules</div>
                    <select name="module" onchange="this.form.submit()">
                        <?php foreach($modules as $m): ?>
                            <option value="<?= $m['id_module'] ?>" <?= $module_choisi_id == $m['id_module'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['nom_module']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- GROUPE -->
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <div class="section-title">groupe</div>
                    <select name="groupe" onchange="this.form.submit()">
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
                        placeholder="Nom ou matricule..."
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

        <!-- TABLEAU ÉTUDIANTS -->
        <?php if (empty($etudiants)): ?>
            <p class="no-data">Aucun étudiant trouvé.</p>
        <?php else: ?>
        <form method="POST" action="enregistrer_notes.php">
            <input type="hidden" name="module" value="<?= $module_choisi_id ?>">
            <input type="hidden" name="groupe" value="<?= $groupe_choisi_id ?>">

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Matricule</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Contrôle 1 /20</th>
                        <th>Contrôle 2 /20</th>
                        <th>TP /20</th>
                        <th>Examen /20</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($etudiants as $i => $etudiant): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($etudiant['matricule']) ?></td>
                        <td><?= htmlspecialchars($etudiant['nom']) ?></td>
                        <td><?= htmlspecialchars($etudiant['prenom']) ?></td>
                        <td><input type="number" name="cc1[<?= $etudiant['matricule'] ?>]" min="0" max="20" step="0.25" placeholder="—" value="<?= $notes_existantes[$etudiant['matricule']]['cc1'] ?? '0' ?>"></td>
                        <td><input type="number" name="cc2[<?= $etudiant['matricule'] ?>]" min="0" max="20" step="0.25" placeholder="—" value="<?= $notes_existantes[$etudiant['matricule']]['cc2'] ?? '0' ?>"></td>
                        <td><input type="number" name="tp[<?= $etudiant['matricule'] ?>]" min="0" max="20" step="0.25" placeholder="—" value="<?= $notes_existantes[$etudiant['matricule']]['tp'] ?? '0' ?>"></td>
                        <td><input type="number" name="examen[<?= $etudiant['matricule'] ?>]" min="0" max="20" step="0.25" placeholder="—" value="<?= $notes_existantes[$etudiant['matricule']]['examen'] ?? '0' ?>"></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button type="submit" class="btn-save">💾 Enregistrer les notes</button>
        </form>
        <?php endif; ?>

    </div>

</div>

</body>
</html>