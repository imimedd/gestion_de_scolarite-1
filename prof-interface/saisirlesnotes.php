<?php
session_start();
require_once("../connexion.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$annee       = date("Y") . " / " . (date("Y") + 1);
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
        /* ── Cards grille ── */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        /* ── Inputs notes dans les cards ── */
        .note-input {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #dde1ef;
            border-radius: 6px;
            font-size: 14px;
            text-align: center;
            font-family: 'IBM Plex Mono', monospace;
            background: #f8faff;
            transition: border-color 0.2s, background 0.2s;
            box-sizing: border-box;
        }

        .note-input:focus {
            outline: none;
            border-color: #378ADD;
            background: #fff;
        }

        /* ── Grille 2 colonnes à l'intérieur d'une card ── */
        .notes-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .note-field {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .note-field label {
            font-size: 11px;
            font-weight: 600;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* ── État vide ── */
        .empty-state {
            padding: 40px;
            text-align: center;
            color: #888;
            font-size: 15px;
        }

        /* ── Sidebar : infos prof ── */
        .sidebar-prof {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
        }

        .sidebar-prof span {
            color: #fff;
            font-size: 14px;
            font-weight: 600;
        }

        .sidebar-title {
            text-align: center;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            padding: 8px 20px 0;
        }

        .sidebar-logo {
            text-align: center;
            padding: 0 20px 10px;
        }

        .sidebar-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.15);
            margin: 10px 20px;
        }

        /* ── Bouton enregistrer aligné à droite ── */
        .submit-row {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
        }
    </style>
</head>
<body>

<!-- ===================== SIDEBAR ===================== -->
<div class="sidebar">
    <div class="sidebar-logo">
        <img src="img/usthb1.png" width="100" height="100" alt="USTHB">
    </div>
    <p class="sidebar-title">USTHB — Enseignant</p>
    <hr class="sidebar-divider">
    <div class="sidebar-prof">
        <img src="img/prof.png" width="50" height="50" alt="Profil" style="border-radius:50%;">
        <span><?= htmlspecialchars(($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? '')) ?></span>
    </div>
    <hr class="sidebar-divider">
    <a href="acceuille.php"         class="<?= $currentPage=='acceuille.php'        ?'active':'' ?>">Accueil</a>
    <a href="MesModules.php"        class="<?= $currentPage=='MesModules.php'       ?'active':'' ?>">Mes Modules</a>
    <a href="saisirlesnotes.php"    class="<?= $currentPage=='saisirlesnotes.php'   ?'active':'' ?>">Saisir les notes</a>
    <a href="listeDesEtudiants.php" class="<?= $currentPage=='listeDesEtudiants.php'?'active':'' ?>">Liste des étudiants</a>
    <a href="logout.php"            class="btn-deconnexion">Déconnexion</a>
</div>

<!-- ===================== MAIN ===================== -->
<div class="main">

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <div class="header-icon">✍️</div>
            <p style="color:#000; font-size:16px;">
                Saisir les notes — <span style="color:#888; font-weight:300;">Enseignant</span>
            </p>
        </div>
        <div class="year-badge"><?= $annee ?></div>
    </div>

    <div class="page-content">

        <!-- Alerte succès -->
        <?php if ($success): ?>
            <div class="alert alert-success">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                Notes enregistrées avec succès !
            </div>
        <?php endif; ?>

        <!-- Toolbar filtres -->
        <form method="GET" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="toolbar">

            <div class="toolbar-group">
                <label>Semestre</label>
                <select name="semestre" onchange="this.form.submit()" style="padding:8px 12px; border:1px solid #dde1ef; border-radius:8px;">
                    <?php foreach ($semestres_dispo as $s): ?>
                        <option value="<?= $s ?>" <?= $semestre_choisi == $s ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="toolbar-sep"></div>

            <div class="toolbar-group">
                <label>Module</label>
                <select name="module" onchange="this.form.submit()" style="padding:8px 12px; border:1px solid #dde1ef; border-radius:8px;">
                    <?php foreach ($modules as $m): ?>
                        <option value="<?= $m['id_module'] ?>" <?= $module_choisi_id == $m['id_module'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['nom_module']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="toolbar-sep"></div>

            <div class="toolbar-group">
                <label>Groupe</label>
                <select name="groupe" onchange="this.form.submit()" style="padding:8px 12px; border:1px solid #dde1ef; border-radius:8px;">
                    <?php if (empty($groupes)): ?>
                        <option value="">— Aucun —</option>
                    <?php else: foreach ($groupes as $g): ?>
                        <option value="<?= $g['id_groupe'] ?>" <?= $groupe_choisi_id == $g['id_groupe'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g['nom_groupe']) ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>

            <div class="toolbar-sep"></div>

            <div class="toolbar-group" style="flex:1;">
                <input type="text" name="recherche"
                       placeholder="Rechercher nom/matricule..."
                       value="<?= htmlspecialchars($recherche) ?>"
                       style="width:100%;">
            </div>

            <div class="toolbar-group">
                <button type="submit" class="btn btn-secondary">Rechercher</button>
            </div>

        </form>

        <!-- Contenu principal -->
        <?php if (empty($etudiants)): ?>
            <div class="table-card">
                <div class="empty-state">Aucun étudiant trouvé pour ces critères.</div>
            </div>
        <?php else: ?>

        <form method="POST" action="enregistrer_notes.php">
            <input type="hidden" name="module"   value="<?= $module_choisi_id ?>">
            <input type="hidden" name="groupe"   value="<?= $groupe_choisi_id ?>">
            <input type="hidden" name="semestre" value="<?= htmlspecialchars($semestre_choisi) ?>">

            <div class="cards-grid">
                <?php foreach ($etudiants as $etudiant):
                    $mat    = $etudiant['matricule'];
                    $cc1    = $notes_existantes[$mat]['cc1']    ?? null;
                    $cc2    = $notes_existantes[$mat]['cc2']    ?? null;
                    $tp     = $notes_existantes[$mat]['tp']     ?? null;
                    $examen = $notes_existantes[$mat]['examen'] ?? null;
                    $moy    = calculMoyenne([$cc1, $cc2, $tp, $examen]);

                    if ($moy === null) {
                        $badge_class = 'card-moy-empty';
                    } elseif ($moy >= 10) {
                        $badge_class = 'card-moy-pass';
                    } else {
                        $badge_class = 'card-moy-fail';
                    }
                ?>
                <div class="student-card">
                    <div class="card-header">
                        <div>
                            <div class="card-name"><?= htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']) ?></div>
                            <div class="card-mat"><?= htmlspecialchars($mat) ?></div>
                        </div>
                        <span class="card-moy-badge moy-cell-preview <?= $badge_class ?>">
                            <?= $moy !== null ? $moy . '/20' : '—/20' ?>
                        </span>
                    </div>

                    <div class="card-body">
                        <div class="notes-grid">
                            <div class="note-field">
                                <label>CC 1</label>
                                <input class="note-input" type="number"
                                       name="cc1[<?= $mat ?>]"
                                       min="0" max="20" step="0.25"
                                       placeholder="—"
                                       value="<?= $cc1 ?? '' ?>"
                                       oninput="updateMoyCard(this.closest('.student-card'))">
                            </div>
                            <div class="note-field">
                                <label>CC 2</label>
                                <input class="note-input" type="number"
                                       name="cc2[<?= $mat ?>]"
                                       min="0" max="20" step="0.25"
                                       placeholder="—"
                                       value="<?= $cc2 ?? '' ?>"
                                       oninput="updateMoyCard(this.closest('.student-card'))">
                            </div>
                            <div class="note-field">
                                <label>TP</label>
                                <input class="note-input" type="number"
                                       name="tp[<?= $mat ?>]"
                                       min="0" max="20" step="0.25"
                                       placeholder="—"
                                       value="<?= $tp ?? '' ?>"
                                       oninput="updateMoyCard(this.closest('.student-card'))">
                            </div>
                            <div class="note-field">
                                <label>Examen</label>
                                <input class="note-input" type="number"
                                       name="examen[<?= $mat ?>]"
                                       min="0" max="20" step="0.25"
                                       placeholder="—"
                                       value="<?= $examen ?? '' ?>"
                                       oninput="updateMoyCard(this.closest('.student-card'))">
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="submit-row">
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Enregistrer les notes
                </button>
            </div>
        </form>

        <?php endif; ?>
    </div><!-- /page-content -->
</div><!-- /main -->

<script>
function updateMoyCard(card) {
    const inputs = card.querySelectorAll('input[type="number"]');
    const vals   = [];
    inputs.forEach(inp => {
        const v = parseFloat(inp.value);
        if (!isNaN(v)) vals.push(v);
    });

    const badge = card.querySelector('.moy-cell-preview');
    if (vals.length === 0) {
        badge.textContent   = '—/20';
        badge.className     = 'card-moy-badge moy-cell-preview card-moy-empty';
        return;
    }

    const avg = (vals.reduce((a, b) => a + b, 0) / vals.length).toFixed(2);
    badge.textContent = avg + '/20';
    badge.className   = 'card-moy-badge moy-cell-preview ' + (avg >= 10 ? 'card-moy-pass' : 'card-moy-fail');
}
</script>
</body>
</html>