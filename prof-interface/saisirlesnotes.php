<?php
session_start();
require_once("../connexion.php");

$annee       = "2025 / 2026";
$currentPage = basename($_SERVER['PHP_SELF']);

// Semestres disponibles
$semestres_dispo = $pdo->query("SELECT DISTINCT semestre FROM modules ORDER BY semestre")->fetchAll(PDO::FETCH_COLUMN);

// Semestre choisi
$semestre_choisi = $_GET['semestre'] ?? ($semestres_dispo[0] ?? 'Semestre 1');

// Modules du semestre choisi
$stmt = $pdo->prepare("SELECT id_module, nom_module, coef FROM modules WHERE semestre = ? ORDER BY nom_module");
$stmt->execute([$semestre_choisi]);
$modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Module choisi
$module_choisi_id = $_GET['module'] ?? ($modules[0]['id_module'] ?? null);

// Groupes liés au module
$groupes = [];
if ($module_choisi_id) {
    $stmt = $pdo->prepare("
        SELECT g.id_groupe, g.nom_groupe 
        FROM groupes g
        JOIN module_groupe mg ON g.id_groupe = mg.id_groupe
        WHERE mg.id_module = ?
    ");
    $stmt->execute([$module_choisi_id]);
    $groupes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$groupe_choisi_id = $_GET['groupe'] ?? ($groupes[0]['id_groupe'] ?? null);
$recherche = $_GET['recherche'] ?? '';

// Étudiants du groupe
$etudiants = [];
if ($groupe_choisi_id) {
    $sql = "SELECT numero, matricule, nom, prenom FROM etudiants WHERE groupe_td = ?";
    $params = [$groupe_choisi_id];
    if (!empty($recherche)) {
        $sql .= " AND (nom LIKE ? OR prenom LIKE ? OR matricule LIKE ?)";
        $params[] = "%$recherche%"; $params[] = "%$recherche%"; $params[] = "%$recherche%";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Notes existantes
$notes_existantes = [];
if ($groupe_choisi_id && $module_choisi_id && !empty($etudiants)) {
    $stmt = $pdo->prepare("
        SELECT e.type_eval, n.note, et.matricule
        FROM evaluations e
        JOIN notes n ON n.id_evaluation = e.id_evaluation
        JOIN etudiants et ON et.numero = n.id_etudiant
        WHERE e.id_module = ? AND e.id_groupe = ?
    ");
    $stmt->execute([$module_choisi_id, $groupe_choisi_id]);
    $type_map = ['Contrôle 1'=>'cc1','Contrôle 2'=>'cc2','TP'=>'tp','Examen final'=>'examen'];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $key = $type_map[$row['type_eval']] ?? null;
        if ($key) $notes_existantes[$row['matricule']][$key] = $row['note'];
    }
}

function calculMoyenne($notes) {
    $valeurs = array_filter($notes, fn($v) => $v !== null && $v !== '');
    if (empty($valeurs)) return null;
    return round(array_sum($valeurs) / count($valeurs), 2);
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
    <style>
        .moyenne-cell{font-family:'IBM Plex Mono',monospace;font-weight:600;font-size:13px;padding:4px 10px;border-radius:20px;display:inline-block;}
        .moyenne-pass{background:#e6f4ea;color:#2d7a3a;}
        .moyenne-fail{background:#fee2e2;color:#b91c1c;}
        .moyenne-empty{background:#f1f5f9;color:#888;}
    </style>
</head>
<body>
<div class="sidebar">
    <div style="text-align:center;">
        <img src="img/usthb1.png" width="100" height="100"/>
        <p><font color="#ffffff"><font size="3"><center><b>usthb - Enseignant</b></center></font></font></p>
    </div>
    <br><hr>
    <div style="display:flex;align-items:center;">
        <img src="img/prof.png" width="90" height="90"/>
        <center><font color="#ffffff"><font size="3"><?= $_SESSION['prenom'].' '.$_SESSION['nom'] ?></font></font></center>
    </div>
    <hr><br>
    <a href="index.php" class="<?= $currentPage=='index.php'?'active':'' ?>">Accueil</a>
    <a href="MesModules.php" class="<?= $currentPage=='MesModules.php'?'active':'' ?>">Mes Modules</a>
    <a href="saisirlesnotes.php" class="<?= $currentPage=='saisirlesnotes.php'?'active':'' ?>">Saisir les notes</a>
    <a href="listeDesEtudiants.php" class="<?= $currentPage=='listeDesEtudiants.php'?'active':'' ?>">Liste des étudiants</a>
    <a href="logout.php" class="<?= $currentPage=='logout.php'?'active':'' ?>">Déconnexion</a>
</div>

<div class="main">
    <div class="header">
        <div class="header-left">
            <div class="header-icon">📄</div>
            <p style="color:#000;font-size:16px;">Saisir les notes — <span style="color:#888;font-weight:300;">Enseignant</span></p>
        </div>
        <div class="year-badge"><?= $annee ?></div>
    </div>
    <div class="content">
        <?php if($success): ?>
            <div class="success">✅ Notes enregistrées avec succès !</div>
        <?php endif; ?>

        <form method="GET" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
            <div style="display:flex;gap:24px;align-items:flex-end;flex-wrap:wrap;margin-bottom:20px;">

                <div style="display:flex;flex-direction:column;gap:4px;">
                    <div class="section-title">semestre</div>
                    <select name="semestre" onchange="this.form.submit()">
                        <?php foreach($semestres_dispo as $s): ?>
                            <option value="<?= $s ?>" <?= $semestre_choisi==$s?'selected':'' ?>><?= htmlspecialchars($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display:flex;flex-direction:column;gap:4px;">
                    <div class="section-title">module</div>
                    <select name="module" onchange="this.form.submit()">
                        <?php foreach($modules as $m): ?>
                            <option value="<?= $m['id_module'] ?>" <?= $module_choisi_id==$m['id_module']?'selected':'' ?>>
                                <?= htmlspecialchars($m['nom_module']) ?> (coef <?= $m['coef'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display:flex;flex-direction:column;gap:4px;">
                    <div class="section-title">groupe</div>
                    <select name="groupe" onchange="this.form.submit()">
                        <?php if(empty($groupes)): ?>
                            <option value="">— Aucun groupe —</option>
                        <?php else: foreach($groupes as $g): ?>
                            <option value="<?= $g['id_groupe'] ?>" <?= $groupe_choisi_id==$g['id_groupe']?'selected':'' ?>><?= htmlspecialchars($g['nom_groupe']) ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>

                <div style="display:flex;flex-direction:column;gap:4px;">
                    <div class="section-title">recherche</div>
                    <input type="text" name="recherche" placeholder="Nom ou matricule..."
                        value="<?= htmlspecialchars($recherche) ?>"
                        style="padding:8px;border-radius:4px;border:1px solid #ccc;font-size:14px;min-width:200px;">
                </div>

               

                <div style="display:flex;flex-direction:column;gap:4px;justify-content:flex-end;">
                    <button type="submit" style="padding:8px 16px;background:#1a2a4a;color:white;border:none;border-radius:4px;cursor:pointer;">Rechercher</button>
                </div>
            </div>
        </form>

        <?php if(empty($etudiants)): ?>
            <p class="no-data">Aucun étudiant trouvé.</p>
        <?php else: ?>
        <form method="POST" action="enregistrer_notes.php">
            <input type="hidden" name="module" value="<?= $module_choisi_id ?>">
            <input type="hidden" name="groupe" value="<?= $groupe_choisi_id ?>">
            <input type="hidden" name="semestre" value="<?= htmlspecialchars($semestre_choisi) ?>">
            <table>
                <thead>
                    <tr>
                        <th>#</th><th>Matricule</th><th>Nom</th><th>Prénom</th>
                        <th>Contrôle 1 /20</th><th>Contrôle 2 /20</th><th>TP /20</th><th>Examen /20</th>
                        <th>Moyenne module</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($etudiants as $i => $etudiant): ?>
                <?php
                    $mat    = $etudiant['matricule'];
                    $cc1    = $notes_existantes[$mat]['cc1']    ?? null;
                    $cc2    = $notes_existantes[$mat]['cc2']    ?? null;
                    $tp     = $notes_existantes[$mat]['tp']     ?? null;
                    $examen = $notes_existantes[$mat]['examen'] ?? null;
                    $moy    = calculMoyenne([$cc1,$cc2,$tp,$examen]);
                ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= htmlspecialchars($mat) ?></td>
                    <td><?= htmlspecialchars($etudiant['nom']) ?></td>
                    <td><?= htmlspecialchars($etudiant['prenom']) ?></td>
                    <td><input type="number" name="cc1[<?= $mat ?>]" min="0" max="20" step="0.25" placeholder="—" value="<?= $cc1??'' ?>" oninput="updateMoy(this.closest('tr'))"></td>
                    <td><input type="number" name="cc2[<?= $mat ?>]" min="0" max="20" step="0.25" placeholder="—" value="<?= $cc2??'' ?>" oninput="updateMoy(this.closest('tr'))"></td>
                    <td><input type="number" name="tp[<?= $mat ?>]"  min="0" max="20" step="0.25" placeholder="—" value="<?= $tp??'' ?>"  oninput="updateMoy(this.closest('tr'))"></td>
                    <td><input type="number" name="examen[<?= $mat ?>]" min="0" max="20" step="0.25" placeholder="—" value="<?= $examen??'' ?>" oninput="updateMoy(this.closest('tr'))"></td>
                    <td class="moy-cell">
                        <?php if($moy!==null): ?>
                            <span class="moyenne-cell <?= $moy>=10?'moyenne-pass':'moyenne-fail' ?>"><?= $moy ?> / 20</span>
                        <?php else: ?>
                            <span class="moyenne-cell moyenne-empty">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn-save">💾 Enregistrer les notes</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<script>
function updateMoy(row) {
    const inputs = row.querySelectorAll('input[type="number"]');
    const vals = [];
    inputs.forEach(inp => { const v=parseFloat(inp.value); if(!isNaN(v)) vals.push(v); });
    const cell = row.querySelector('.moy-cell');
    if(vals.length===0){ cell.innerHTML='<span class="moyenne-cell moyenne-empty">—</span>'; return; }
    const avg = (vals.reduce((a,b)=>a+b,0)/vals.length).toFixed(2);
    const cls = avg>=10?'moyenne-pass':'moyenne-fail';
    cell.innerHTML=`<span class="moyenne-cell ${cls}">${avg} / 20</span>`;
}
</script>
</body>
</html>