<?php
require_once("connexion.php");

$module_id = $_POST['module'];
$groupe_id = $_POST['groupe'];

$cc1_list    = $_POST['cc1']    ?? [];
$cc2_list    = $_POST['cc2']    ?? [];
$tp_list     = $_POST['tp']     ?? [];
$examen_list = $_POST['examen'] ?? [];

// Les types d'évaluation correspondants
$types = [
    'cc1'    => 'Contrôle 1',
    'cc2'    => 'Contrôle 2',
    'tp'     => 'TP',
    'examen' => 'Examen final'
];

// Pour chaque type d'évaluation, trouver ou créer l'entrée dans la table evaluations
$eval_ids = [];
foreach ($types as $key => $type_eval) {
    $stmt = $pdo->prepare("SELECT id_evaluation FROM evaluations WHERE id_module = ? AND id_groupe = ? AND type_eval = ?");
    $stmt->execute([$module_id, $groupe_id, $type_eval]);
    $eval = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($eval) {
        $eval_ids[$key] = $eval['id_evaluation'];
    } else {
        // Créer l'évaluation si elle n'existe pas
        $stmt = $pdo->prepare("INSERT INTO evaluations (id_module, id_groupe, type_eval, date_eval) VALUES (?, ?, ?, CURDATE())");
        $stmt->execute([$module_id, $groupe_id, $type_eval]);
        $eval_ids[$key] = $pdo->lastInsertId();
    }
}

// Associer chaque liste de notes à son type
$listes = [
    'cc1'    => $cc1_list,
    'cc2'    => $cc2_list,
    'tp'     => $tp_list,
    'examen' => $examen_list
];

// Pour chaque type et chaque étudiant, insérer ou mettre à jour la note
foreach ($listes as $key => $liste) {
    $id_evaluation = $eval_ids[$key];

    foreach ($liste as $matricule => $note_value) {
        // Ignorer les notes vides
        if ($note_value === '' || $note_value === null) {
            continue;
        }

        // Récupérer l'id_etudiant à partir du matricule
        $stmt = $pdo->prepare("SELECT id_etudiant FROM etudiants WHERE matricule = ?");
        $stmt->execute([$matricule]);
        $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$etudiant) {
            continue; // Étudiant non trouvé, on passe
        }

        $id_etudiant = $etudiant['id_etudiant'];

        // Vérifier si une note existe déjà pour cet étudiant et cette évaluation
        $stmt = $pdo->prepare("SELECT id_note FROM notes WHERE id_etudiant = ? AND id_evaluation = ?");
        $stmt->execute([$id_etudiant, $id_evaluation]);
        $existe = $stmt->fetch();

        if ($existe) {
            // UPDATE
            $stmt = $pdo->prepare("UPDATE notes SET note = ? WHERE id_etudiant = ? AND id_evaluation = ?");
            $stmt->execute([$note_value, $id_etudiant, $id_evaluation]);
        } else {
            // INSERT
            $stmt = $pdo->prepare("INSERT INTO notes (id_etudiant, id_evaluation, note) VALUES (?, ?, ?)");
            $stmt->execute([$id_etudiant, $id_evaluation, $note_value]);
        }
    }
}

header("Location: saisirlesnotes.php?module=$module_id&groupe=$groupe_id&success=1");
exit();