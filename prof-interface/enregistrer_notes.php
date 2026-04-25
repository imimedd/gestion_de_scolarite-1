<?php
session_start();
require_once("../connexion.php");

$module_id = $_POST['module'];
$groupe_id = $_POST['groupe'];

$cc1_list    = $_POST['cc1']    ?? [];
$cc2_list    = $_POST['cc2']    ?? [];
$tp_list     = $_POST['tp']     ?? [];
$examen_list = $_POST['examen'] ?? [];

$types = [
    'cc1'    => 'Contrôle 1',
    'cc2'    => 'Contrôle 2',
    'tp'     => 'TP',
    'examen' => 'Examen final'
];

$eval_ids = [];
foreach ($types as $key => $type_eval) {
    $stmt = $pdo->prepare("SELECT id_evaluation FROM evaluations WHERE id_module = ? AND id_groupe = ? AND type_eval = ?");
    $stmt->execute([$module_id, $groupe_id, $type_eval]);
    $eval = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($eval) {
        $eval_ids[$key] = $eval['id_evaluation'];
    } else {
        $stmt = $pdo->prepare("INSERT INTO evaluations (id_module, id_groupe, type_eval, date_eval) VALUES (?, ?, ?, CURDATE())");
        $stmt->execute([$module_id, $groupe_id, $type_eval]);
        $eval_ids[$key] = $pdo->lastInsertId();
    }
}

$listes = [
    'cc1'    => $cc1_list,
    'cc2'    => $cc2_list,
    'tp'     => $tp_list,
    'examen' => $examen_list
];

foreach ($listes as $key => $liste) {
    $id_evaluation = $eval_ids[$key];

    foreach ($liste as $matricule => $note_value) {
        if ($note_value === '' || $note_value === null) {
            continue;
        }

        // CORRECTION : la colonne s'appelle "numero" et non "id_etudiant"
        $stmt = $pdo->prepare("SELECT numero FROM etudiants WHERE matricule = ?");
        $stmt->execute([$matricule]);
        $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$etudiant) {
            continue;
        }

        $id_etudiant = $etudiant['numero'];

        $stmt = $pdo->prepare("SELECT id_note FROM notes WHERE id_etudiant = ? AND id_evaluation = ?");
        $stmt->execute([$id_etudiant, $id_evaluation]);
        $existe = $stmt->fetch();

        if ($existe) {
            $stmt = $pdo->prepare("UPDATE notes SET note = ? WHERE id_etudiant = ? AND id_evaluation = ?");
            $stmt->execute([$note_value, $id_etudiant, $id_evaluation]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO notes (id_etudiant, id_evaluation, note) VALUES (?, ?, ?)");
            $stmt->execute([$id_etudiant, $id_evaluation, $note_value]);
        }
    }
}

// CORRECTION : rester sur saisirlesnotes.php avec un message de succès
header("Location: saisirlesnotes.php?module=$module_id&groupe=$groupe_id&success=1");
exit();