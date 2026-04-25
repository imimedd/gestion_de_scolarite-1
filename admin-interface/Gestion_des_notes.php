<?php
session_start();
require_once("../connexion.php");

$success_message = null;
$error_message   = null;
$editMode        = false;
$note_row        = null;

// Statistiques
$nb_notes     = $pdo->query("SELECT COUNT(*) FROM notes")->fetchColumn();
$nb_etudiants = $pdo->query("SELECT COUNT(DISTINCT id_etudiant) FROM notes")->fetchColumn();
$moyenne_gen  = $pdo->query("SELECT ROUND(AVG(note), 2) FROM notes WHERE note > 0")->fetchColumn();

// DELETE
if (isset($_GET['delete'])) {
    try {
        $pdo->prepare("DELETE FROM notes WHERE id_note=?")->execute([$_GET['delete']]);
        $success_message = "Note supprimée avec succès.";
        $nb_notes     = $pdo->query("SELECT COUNT(*) FROM notes")->fetchColumn();
        $nb_etudiants = $pdo->query("SELECT COUNT(DISTINCT id_etudiant) FROM notes")->fetchColumn();
        $moyenne_gen  = $pdo->query("SELECT ROUND(AVG(note), 2) FROM notes WHERE note > 0")->fetchColumn();
    } catch (PDOException $e) {
        $error_message = "Erreur lors de la suppression.";
    }
}

// EDIT mode
if (isset($_GET['edit'])) {
    $editMode = true;
    $stmt = $pdo->prepare("
        SELECT n.*, ev.id_module
        FROM notes n
        JOIN evaluations ev ON n.id_evaluation = ev.id_evaluation
        WHERE n.id_note = ?
    ");
    $stmt->execute([$_GET['edit']]);
    $note_row = $stmt->fetch();
}

// INSERT / UPDATE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_etudiant   = trim($_POST['id_etudiant']);
    $id_evaluation = trim($_POST['id_evaluation']);
    $note_val      = trim($_POST['note']);
    try {
        if (!empty($_POST['id_note'])) {
            $pdo->prepare("UPDATE notes SET id_etudiant=?, id_evaluation=?, note=? WHERE id_note=?")
                ->execute([$id_etudiant, $id_evaluation, $note_val, $_POST['id_note']]);
            $success_message = "Note modifiée avec succès.";
        } else {
            $check = $pdo->prepare("SELECT COUNT(*) FROM notes WHERE id_etudiant=? AND id_evaluation=?");
            $check->execute([$id_etudiant, $id_evaluation]);
            if ($check->fetchColumn() > 0) {
                $error_message = "Une note existe déjà pour cet étudiant et cette évaluation.";
            } else {
                $pdo->prepare("INSERT INTO notes (id_etudiant, id_evaluation, note) VALUES (?, ?, ?)")
                    ->execute([$id_etudiant, $id_evaluation, $note_val]);
                $success_message = "Note ajoutée avec succès.";
            }
        }
        $editMode = false;
        $nb_notes     = $pdo->query("SELECT COUNT(*) FROM notes")->fetchColumn();
        $nb_etudiants = $pdo->query("SELECT COUNT(DISTINCT id_etudiant) FROM notes")->fetchColumn();
        $moyenne_gen  = $pdo->query("SELECT ROUND(AVG(note), 2) FROM notes WHERE note > 0")->fetchColumn();
    } catch (PDOException $e) {
        $error_message = "Erreur : " . $e->getMessage();
    }
}

// Données formulaire
$etudiants   = $pdo->query("SELECT numero, matricule, nom, prenom FROM etudiants ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
$modules_all = $pdo->query("SELECT id_module, nom_module, coef, semestre FROM modules ORDER BY semestre, nom_module")->fetchAll(PDO::FETCH_ASSOC);
$evaluations = $pdo->query("
    SELECT ev.id_evaluation, ev.type_eval, ev.date_eval, m.nom_module, m.id_module
    FROM evaluations ev JOIN modules m ON ev.id_module = m.id_module
    ORDER BY m.nom_module, ev.type_eval
")->fetchAll(PDO::FETCH_ASSOC);
$evaluations_json = json_encode($evaluations);

// Semestres
$semestres_dispo = $pdo->query("SELECT DISTINCT semestre FROM modules ORDER BY semestre")->fetchAll(PDO::FETCH_COLUMN);
$semestre_choisi = $_GET['semestre'] ?? ($semestres_dispo[0] ?? '');
$search_term     = $_GET['search'] ?? '';

// Modules du semestre
$stmt = $pdo->prepare("SELECT id_module, nom_module, coef FROM modules WHERE semestre = ? ORDER BY nom_module");
$stmt->execute([$semestre_choisi]);
$modules_semestre = $stmt->fetchAll(PDO::FETCH_ASSOC);
$mod_ids = array_column($modules_semestre, 'id_module');

// Notes tab[id_etudiant][id_module][type] = note
$notes_tab = [];
$type_map  = ['Contrôle 1'=>'cc1','Contrôle 2'=>'cc2','TP'=>'tp','Examen final'=>'examen'];
if (!empty($mod_ids)) {
    $ph   = implode(',', array_fill(0, count($mod_ids), '?'));
    $stmt = $pdo->prepare("
        SELECT n.id_etudiant, ev.id_module, ev.type_eval, n.note
        FROM notes n JOIN evaluations ev ON n.id_evaluation = ev.id_evaluation
        WHERE ev.id_module IN ($ph)
    ");
    $stmt->execute($mod_ids);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $key = $type_map[$row['type_eval']] ?? strtolower($row['type_eval']);
        $notes_tab[$row['id_etudiant']][$row['id_module']][$key] = $row['note'];
    }
}

// Étudiants avec notes dans ce semestre
$etudiants_semestre = array_values(array_filter($etudiants, fn($e) => !empty($notes_tab[$e['numero']])));
if (!empty($search_term)) {
    $etudiants_semestre = array_values(array_filter($etudiants_semestre, fn($e) =>
        stripos($e['nom'],$search_term)!==false ||
        stripos($e['prenom'],$search_term)!==false ||
        stripos($e['matricule'],$search_term)!==false
    ));
}

// Moyennes pondérées
$moyennes_ponderees = [];
foreach ($etudiants as $et) {
    $id = $et['numero']; $sp = 0; $sc = 0;
    foreach ($modules_semestre as $mod) {
        $vals = array_filter($notes_tab[$id][$mod['id_module']] ?? [], fn($v)=>$v!==null);
        if (empty($vals)) continue;
        $sp += (array_sum($vals)/count($vals)) * $mod['coef'];
        $sc += $mod['coef'];
    }
    if ($sc > 0) {
        $moyennes_ponderees[] = [
            'matricule'    => $et['matricule'],
            'nom'          => $et['nom'],
            'prenom'       => $et['prenom'],
            'moy_generale' => round($sp/$sc, 2),
        ];
    }
}
usort($moyennes_ponderees, fn($a,$b) => $b['moy_generale'] <=> $a['moy_generale']);

// Notes map pour JS
$notes_map = [];
foreach ($pdo->query("
    SELECT n.id_etudiant, ev.id_module, n.note, n.id_evaluation
    FROM notes n JOIN evaluations ev ON n.id_evaluation = ev.id_evaluation
")->fetchAll(PDO::FETCH_ASSOC) as $n) {
    $key = $n['id_etudiant'].'-'.$n['id_module'];
    $notes_map[$key][] = ['note'=>(float)$n['note'],'id_evaluation'=>$n['id_evaluation']];
}

$currentPage = basename($_SERVER['PHP_SELF']);
$annee = "2025 / 2026";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Notes — USTHB</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../prof-interface/style.css">
</head>
<body>

<div class="sidebar">
    <div style="text-align:center;">
        <img src="img/usthb1.png" width="100" height="100"/>
        <p><font color="#ffffff"><font size="3"><center><b>usthb - Administrateur</b></center></font></font></p>
    </div>
    <br><hr>
    <div style="display:flex;align-items:center;">
        <img src="img/prof.png" width="90" height="90"/>
        <center><font color="#ffffff"><font size="3"><?= htmlspecialchars(($_SESSION['prenom']??'').' '.($_SESSION['nom']??'')) ?></font></font></center>
    </div>
    <hr><br>
    <a href="acceuille1.php"              class="<?= $currentPage=='acceuille1.php'?'active':'' ?>">Accueil</a>
    <a href="Gestion_des_modules.php"     class="<?= $currentPage=='Gestion_des_modules.php'?'active':'' ?>">Gérer les modules</a>
    <a href="Gestion_des_notes.php"           class="<?= $currentPage=='Gestion_des_notes.php'?'active':'' ?>">Gérer les notes</a>
    <a href="Gestion_des_enseignants.php" class="<?= $currentPage=='Gestion_des_enseignants.php'?'active':'' ?>">Gérer les enseignants</a>
    <a href="gestiondesetudiants.php"     class="<?= $currentPage=='gestiondesetudiants.php'?'active':'' ?>">Gérer les étudiants</a>
    <a href="logout.php"                  class="<?= $currentPage=='logout.php'?'active':'' ?>">Déconnexion</a>
</div>

<div class="main">
    <div class="header">
        <div class="header-left">
            <div class="header-icon">📝</div>
            <p style="color:#000;font-size:16px;">Gestion des notes — <span style="color:#888;font-weight:300;">Administrateur</span></p>
        </div>
        <div class="year-badge"><?= $annee ?></div>
    </div>

    <div class="dashboard-content">

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="section-title">statistiques</div>
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-label">Notes enregistrées</div>
                <div class="stat-value"><?= $nb_notes ?></div>
                <div class="stat-sub">Total dans la BDD</div>
            </div>
            <div class="stat-card teal">
                <div class="stat-label">Étudiants notés</div>
                <div class="stat-value"><?= $nb_etudiants ?></div>
                <div class="stat-sub">Au moins une note</div>
            </div>
            <div class="stat-card purple">
                <div class="stat-label">Moyenne générale</div>
                <div class="stat-value"><?= $moyenne_gen ?? '—' ?></div>
                <div class="stat-sub">Toutes évaluations</div>
            </div>
        </div>

        <!-- Formulaire -->
        <div class="section-title"><?= $editMode ? 'modifier la note' : 'ajouter une note' ?></div>
        <div class="form-card">
            <h2><?= $editMode ? '✏️ Modifier la note' : '➕ Ajouter une note' ?></h2>
            <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" id="noteForm">
                <input type="hidden" name="id_note" value="<?= $editMode ? htmlspecialchars($note_row['id_note']) : '' ?>">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Étudiant</label>
                        <select name="id_etudiant" id="sel_etudiant" required onchange="updateAverage()">
                            <option value="">-- Choisir --</option>
                            <?php foreach ($etudiants as $e): ?>
                                <option value="<?= $e['numero'] ?>" <?= ($editMode && $note_row['id_etudiant']==$e['numero'])?'selected':'' ?>>
                                    <?= htmlspecialchars($e['matricule'].' — '.$e['nom'].' '.$e['prenom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Module</label>
                        <select id="sel_module" onchange="filterEvaluations()">
                            <option value="">-- Choisir un module --</option>
                            <?php foreach ($modules_all as $m): ?>
                                <option value="<?= $m['id_module'] ?>" <?= ($editMode && $note_row['id_module']==$m['id_module'])?'selected':'' ?>>
                                    <?= htmlspecialchars($m['nom_module']) ?> (<?= htmlspecialchars($m['semestre']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Évaluation</label>
                        <select name="id_evaluation" id="sel_evaluation" required onchange="updateAverage()" disabled>
                            <option value="">-- Choisir d'abord un module --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Note (sur 20)</label>
                        <input type="number" name="note" id="inp_note" min="0" max="20" step="0.25"
                               placeholder="ex: 14.5"
                               value="<?= $editMode ? htmlspecialchars($note_row['note']) : '' ?>"
                               required oninput="updateAverage()">
                    </div>
                </div>
                <div class="avg-preview hidden" id="avg_preview">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                    Nouvelle moyenne pour ce module : <span class="avg-val" id="avg_val">—</span> / 20
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <?= $editMode ? 'Enregistrer les modifications' : 'Ajouter la note' ?>
                    </button>
                    <?php if ($editMode): ?>
                        <a href="gestion_notes.php" class="btn btn-secondary">Annuler</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Toolbar : semestre + recherche -->
        <div class="section-title">notes par étudiant</div>
        <div class="toolbar">
            <div class="toolbar-group">
                <label>Semestre :</label>
                <div class="sem-chips">
                    <?php foreach ($semestres_dispo as $s): ?>
                        <a href="?semestre=<?= urlencode($s) ?><?= $search_term ? '&search='.urlencode($search_term) : '' ?>"
                           class="sem-chip <?= $semestre_choisi==$s?'active':'' ?>">
                            <?= htmlspecialchars($s) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="toolbar-sep"></div>
            <form method="GET" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="toolbar-group" style="margin:0;">
                <input type="hidden" name="semestre" value="<?= htmlspecialchars($semestre_choisi) ?>">
                <label>🔍</label>
                <input type="text" name="search" placeholder="Nom ou matricule..."
                       value="<?= htmlspecialchars($search_term) ?>">
                <button type="submit" class="btn btn-primary" style="padding:8px 14px;">Chercher</button>
                <?php if ($search_term): ?>
                    <a href="?semestre=<?= urlencode($semestre_choisi) ?>" class="btn btn-secondary" style="padding:8px 12px;">✕</a>
                <?php endif; ?>
            </form>
            <span class="toolbar-info">
                <?= count($modules_semestre) ?> module(s) &nbsp;·&nbsp; <?= count($etudiants_semestre) ?> étudiant(s)
            </span>
        </div>

        <!-- Onglets -->
        <div class="tabs-wrapper">
            <div class="tab-bar">
                <button class="tab-btn active" onclick="showTab('cards',this)">🎓 Notes par étudiant</button>
                <button class="tab-btn" onclick="showTab('moyennes',this)">📊 Classement & moyennes</button>
            </div>

            <!-- ONGLET 1 : Cards -->
            <div class="tab-panel active" id="tab-cards">
                <?php if (empty($modules_semestre)): ?>
                    <div class="empty-state">Aucun module pour ce semestre.</div>
                <?php elseif (empty($etudiants_semestre)): ?>
                    <div class="empty-state">Aucun étudiant avec des notes pour ce semestre.</div>
                <?php else: ?>
                <div class="cards-grid">
                <?php foreach ($etudiants_semestre as $et):
                    $id = $et['numero']; $sp = 0; $sc = 0;
                    foreach ($modules_semestre as $mod) {
                        $vals = array_filter($notes_tab[$id][$mod['id_module']] ?? [], fn($v)=>$v!==null);
                        if (empty($vals)) continue;
                        $sp += (array_sum($vals)/count($vals)) * $mod['coef'];
                        $sc += $mod['coef'];
                    }
                    $moy_card = $sc > 0 ? round($sp/$sc,2) : null;
                ?>
                <div class="student-card">
                    <div class="card-header">
                        <div>
                            <div class="card-name"><?= htmlspecialchars($et['nom'].' '.$et['prenom']) ?></div>
                            <div class="card-mat"><?= htmlspecialchars($et['matricule']) ?></div>
                        </div>
                        <?php if ($moy_card !== null): ?>
                            <span class="card-moy-badge <?= $moy_card>=10?'card-moy-pass':'card-moy-fail' ?>"><?= $moy_card ?>/20</span>
                        <?php else: ?>
                            <span class="card-moy-badge card-moy-empty">—/20</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                    <?php foreach ($modules_semestre as $mod):
                        $mid  = $mod['id_module'];
                        $mn   = $notes_tab[$id][$mid] ?? [];
                        $cc1  = $mn['cc1']    ?? null;
                        $cc2  = $mn['cc2']    ?? null;
                        $tp   = $mn['tp']     ?? null;
                        $exam = $mn['examen'] ?? null;
                        $vals = array_filter([$cc1,$cc2,$tp,$exam], fn($v)=>$v!==null);
                        if (empty($vals)) continue;
                        $moy_mod = round(array_sum($vals)/count($vals), 2);
                        $chip = fn($lbl,$v) => $v!==null
                            ? '<div class="note-chip"><span class="note-chip-label">'.$lbl.'</span><span class="note-chip-val '.($v>=10?'pass':'fail').'">'.$v.'</span></div>'
                            : '<div class="note-chip"><span class="note-chip-label">'.$lbl.'</span><span class="note-chip-val empty">—</span></div>';
                    ?>
                    <div class="mod-block">
                        <div class="mod-block-title">
                            <?= htmlspecialchars($mod['nom_module']) ?>
                            <span class="mod-coef">coef <?= $mod['coef'] ?></span>
                        </div>
                        <div class="notes-row">
                            <?= $chip('CC1',$cc1) ?>
                            <?= $chip('CC2',$cc2) ?>
                            <?= $chip('TP',$tp) ?>
                            <?= $chip('Exam',$exam) ?>
                            <div class="note-chip moy-chip">
                                <span class="note-chip-label">Moy.</span>
                                <span class="note-chip-val <?= $moy_mod>=10?'pass':'fail' ?>"><?= $moy_mod ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- ONGLET 2 : Classement -->
            <div class="tab-panel" id="tab-moyennes">
                <?php if (empty($moyennes_ponderees)): ?>
                    <div class="empty-state">Aucune donnée disponible pour ce semestre.</div>
                <?php else: ?>
                <table class="moy-table">
                    <thead>
                        <tr>
                            <th style="text-align:center;">Rang</th>
                            <th>Matricule</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th style="text-align:center;">Moy. pondérée</th>
                            <th style="text-align:center;">Résultat</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($moyennes_ponderees as $rank => $mp): ?>
                    <tr>
                        <td class="rank-cell">
                            <?= $rank===0?'🥇':($rank===1?'🥈':($rank===2?'🥉':'#'.($rank+1))) ?>
                        </td>
                        <td style="font-family:'IBM Plex Mono',monospace;font-size:13px;"><?= htmlspecialchars($mp['matricule']) ?></td>
                        <td><?= htmlspecialchars($mp['nom']) ?></td>
                        <td><?= htmlspecialchars($mp['prenom']) ?></td>
                        <td style="text-align:center;">
                            <span class="<?= $mp['moy_generale']>=10?'badge-pass':'badge-fail' ?>"><?= $mp['moy_generale'] ?> / 20</span>
                        </td>
                        <td style="text-align:center;">
                            <?php if ($mp['moy_generale']>=10): ?>
                                <span class="badge-pass">✓ Admis</span>
                            <?php else: ?>
                                <span class="badge-fail">✗ Ajourné</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<script>
const ALL_EVALUATIONS = <?= $evaluations_json ?>;
const NOTES_DATA = <?= json_encode($notes_map) ?>;

function filterEvaluations() {
    const moduleId = document.getElementById('sel_module').value;
    const selEval  = document.getElementById('sel_evaluation');
    selEval.innerHTML = '<option value="">-- Choisir une évaluation --</option>';
    if (!moduleId) { selEval.disabled=true; hideAvg(); return; }
    const filtered = ALL_EVALUATIONS.filter(ev => String(ev.id_module)===String(moduleId));
    filtered.forEach(ev => {
        const opt=document.createElement('option');
        opt.value=ev.id_evaluation;
        opt.textContent=ev.type_eval+(ev.date_eval?' ('+ev.date_eval+')':'');
        selEval.appendChild(opt);
    });
    selEval.disabled=filtered.length===0;
    updateAverage();
}

function updateAverage() {
    const idEtudiant=document.getElementById('sel_etudiant').value;
    const idModule=document.getElementById('sel_module').value;
    const noteVal=parseFloat(document.getElementById('inp_note').value);
    const idEval=document.getElementById('sel_evaluation').value;
    if (!idEtudiant||!idModule||isNaN(noteVal)){hideAvg();return;}
    const key=idEtudiant+'-'+idModule;
    const existing=NOTES_DATA[key]||[];
    let notes=existing.filter(n=>String(n.id_evaluation)!==String(idEval)).map(n=>n.note);
    notes.push(noteVal);
    const avg=notes.reduce((a,b)=>a+b,0)/notes.length;
    document.getElementById('avg_val').textContent=avg.toFixed(2);
    document.getElementById('avg_preview').classList.remove('hidden');
}

function hideAvg(){document.getElementById('avg_preview').classList.add('hidden');}

function showTab(name,btn){
    document.querySelectorAll('.tab-panel').forEach(p=>p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
    document.getElementById('tab-'+name).classList.add('active');
    btn.classList.add('active');
}

(function init(){
    <?php if($editMode&&$note_row): ?>
    document.getElementById('sel_module').value='<?= $note_row['id_module'] ?>';
    filterEvaluations();
    setTimeout(()=>{
        document.getElementById('sel_evaluation').value='<?= $note_row['id_evaluation'] ?>';
        updateAverage();
    },50);
    <?php endif; ?>
})();
</script>
</body>
</html>