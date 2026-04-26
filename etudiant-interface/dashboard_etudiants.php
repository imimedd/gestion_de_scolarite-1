<?php
session_start();

if (!isset($_SESSION['matricule']) || $_SESSION['role'] !== 'etudiant') {
    header("Location: ../index.php");
    exit();
}

require_once '../connexion.php';

$annee       = date("Y") . " / " . (date("Y") + 1);
$currentPage = basename($_SERVER['PHP_SELF']);
$matricule   = $_SESSION['matricule'];

// ── Infos étudiant ───────────────────────────────────────────────
$stmt = $pdo->prepare("SELECT * FROM etudiants WHERE matricule = ?");
$stmt->execute([$matricule]);
$etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$etudiant) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

$numero_etudiant = $etudiant['numero'];

// ── Notes depuis la BDD ──────────────────────────────────────────
$sql_notes = "
    SELECT
        m.nom_module                                              AS matiere,
        m.semestre,
        m.coef                                                   AS coefficient,
        MAX(CASE WHEN ev.type_eval = 'Contrôle 1'  THEN n.note END) AS cc1,
        MAX(CASE WHEN ev.type_eval = 'Contrôle 2'  THEN n.note END) AS cc2,
        MAX(CASE WHEN ev.type_eval = 'TP'           THEN n.note END) AS tp,
        MAX(CASE WHEN ev.type_eval = 'Examen final' THEN n.note END) AS examen
    FROM notes n
    JOIN evaluations ev ON ev.id_evaluation = n.id_evaluation
    JOIN modules m      ON m.id_module      = ev.id_module
    WHERE n.id_etudiant = ?
    GROUP BY m.id_module, m.nom_module, m.semestre, m.coef
    ORDER BY m.semestre, m.nom_module
";
$stmt2 = $pdo->prepare($sql_notes);
$stmt2->execute([$numero_etudiant]);
$toutes_notes = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$notes_par_semestre = [];
foreach ($toutes_notes as $n) {
    $notes_par_semestre[$n['semestre']][] = $n;
}

function calculMoyenne($notes) {
    $total = 0; $coefTotal = 0;
    foreach ($notes as $n) {
        $vals = array_filter(
            [$n['cc1'], $n['cc2'], $n['tp'], $n['examen']],
            fn($v) => $v !== null && $v !== ''
        );
        if (empty($vals)) continue;
        $moy_module = array_sum($vals) / count($vals);
        $total     += $moy_module * $n['coefficient'];
        $coefTotal += $n['coefficient'];
    }
    return $coefTotal > 0 ? round($total / $coefTotal, 2) : null;
}

$moyenne_generale = calculMoyenne($toutes_notes);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord — USTHB</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../prof-interface/style.css">
    <style>
        .info-card {
            background: #fff;
            border: 1px solid #dde1ef;
            border-radius: 12px;
            padding: 24px 28px;
            margin-bottom: 24px;
        }
        .info-card h3 {
            font-size: 14px;
            font-weight: 700;
            color: #1a2a4a;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eef0f7;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 12px 32px;
        }
        .info-grid p {
            font-size: 14px;
            color: #444;
            margin: 0;
        }
        .info-grid p strong {
            color: #1a2a4a;
        }
        .moyenne-big {
            font-size: 48px;
            font-weight: 700;
            color: #1a2a4a;
            font-family: 'IBM Plex Mono', monospace;
            line-height: 1;
        }
        .moyenne-sub {
            font-size: 14px;
            color: #888;
            margin-top: 4px;
        }
        .statut-badge {
            display: inline-block;
            margin-top: 12px;
            padding: 5px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        .statut-admis  { background: #dcfce7; color: #16a34a; }
        .statut-ajourn { background: #fee2e2; color: #dc2626; }
        .sem-moyenne {
            font-size: 13px;
            color: #555;
            margin-top: 10px;
        }
        .sem-moyenne strong { color: #1a2a4a; }
        .tabs-bar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }
        .tab-btn {
            padding: 6px 16px;
            border-radius: 20px;
            border: 1px solid #dde1ef;
            background: #f4f6fb;
            color: #555;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'IBM Plex Sans', sans-serif;
            transition: all .2s;
        }
        .tab-btn.active, .tab-btn:hover {
            background: #1a2a4a;
            color: #fff;
            border-color: #1a2a4a;
        }
        .cards-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }
        @media (max-width: 900px) { .cards-row { grid-template-columns: 1fr; } }
        .btn-download-main {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 22px;
            background: #1a2a4a;
            color: #fff;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: background .2s;
        }
        .btn-download-main:hover { background: #2c3e6a; }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div style="text-align:center;">
        <img src="../image/logo.jpg" width="90" height="90" style="border-radius:50%;"/>
        <p style="color:#ffffff;font-size:14px;font-weight:bold;text-align:center;margin-top:8px;">USTHB — Étudiant</p>
    </div>
    <br><hr>
    <div style="display:flex;align-items:center;gap:10px;padding:0 15px;">
        <span style="color:#ffffff;font-size:14px;">
            <?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?>
        </span>
    </div>
    <hr><br>
    <a href="dashboard_etudiants.php" class="<?= $currentPage == 'dashboard_etudiants.php' ? 'active' : '' ?>">Tableau de bord</a>
    <a href="releve.php"              class="<?= $currentPage == 'releve.php' ? 'active' : '' ?>">Relevé de notes</a>
    <a href="../logout.php" class="btn-deconnexion">Déconnexion</a>
</div>

<!-- MAIN -->
<div class="main">

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <div class="header-icon">📊</div>
            <p style="color:#000;font-size:16px;">Tableau de bord — <span style="color:#888;font-weight:300;">Étudiant</span></p>
        </div>
        <div class="year-badge"><?= $annee ?></div>
    </div>

    <!-- CONTENU -->
    <div class="page-content">

        <!-- Ligne 1 : Infos perso + Moyenne -->
        <div class="cards-row">

            <!-- Infos personnelles -->
            <div class="info-card">
                <h3>👤 Informations personnelles</h3>
                <div class="info-grid">
                    <p><strong>Nom :</strong> <?= htmlspecialchars($etudiant['nom']) ?></p>
                    <p><strong>Prénom :</strong> <?= htmlspecialchars($etudiant['prenom']) ?></p>
                    <p><strong>Matricule :</strong> <?= htmlspecialchars($etudiant['matricule']) ?></p>
                    <p><strong>Spécialité :</strong> <?= htmlspecialchars($etudiant['specialite']) ?></p>
                    <p><strong>Niveau :</strong> <?= htmlspecialchars($etudiant['palier']) ?></p>
                    <p><strong>Section :</strong> <?= htmlspecialchars($etudiant['section']) ?></p>
                    <p><strong>Groupe TD :</strong> <?= htmlspecialchars($etudiant['groupe_td']) ?></p>
                    <p><strong>État :</strong> <?= htmlspecialchars($etudiant['etat']) ?></p>
                </div>
            </div>

            <!-- Moyenne générale -->
            <div class="info-card" style="text-align:center;">
                <h3>📈 Moyenne générale</h3>
                <?php if ($moyenne_generale !== null): ?>
                    <div class="moyenne-big"><?= number_format($moyenne_generale, 2) ?></div>
                    <div class="moyenne-sub">/ 20</div>
                    <span class="statut-badge <?= $moyenne_generale >= 10 ? 'statut-admis' : 'statut-ajourn' ?>">
                        <?= $moyenne_generale >= 10 ? '✅ Admis' : '❌ Ajourné' ?>
                    </span>
                    <div style="margin-top:16px;">
                        <?php foreach ($notes_par_semestre as $sem => $notes_sem): ?>
                            <?php $moy_sem = calculMoyenne($notes_sem); ?>
                            <?php if ($moy_sem !== null): ?>
                                <p class="sem-moyenne"><?= htmlspecialchars($sem) ?> : <strong><?= number_format($moy_sem, 2) ?></strong></p>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="color:#888;margin-top:20px;">Aucune note disponible.</p>
                <?php endif; ?>
            </div>

        </div>

        <!-- Ligne 2 : Tableau des notes -->
        <div class="table-card">
            <div class="table-card-header">
                📋 Notes par matière
            </div>

            <?php if (empty($notes_par_semestre)): ?>
                <div class="empty-state">Aucune note enregistrée.</div>
            <?php else: ?>
                <!-- Onglets semestres -->
                <div style="padding:16px 20px 0;">
                    <div class="tabs-bar">
                        <?php $first = true; foreach ($notes_par_semestre as $sem => $unused): ?>
                            <button class="tab-btn <?= $first ? 'active' : '' ?>"
                                    onclick="showTab('sem_<?= md5($sem) ?>', this)">
                                <?= htmlspecialchars($sem) ?>
                            </button>
                        <?php $first = false; endforeach; ?>
                    </div>
                </div>

                <?php $first = true; foreach ($notes_par_semestre as $sem => $notes_sem): ?>
                <div id="sem_<?= md5($sem) ?>" class="tab-content" <?= $first ? '' : 'style="display:none;"' ?>>
                    <table class="table1">
                        <thead>
                            <tr>
                                <th>Matière</th>
                                <th>CC1</th>
                                <th>CC2</th>
                                <th>TP</th>
                                <th>Examen</th>
                                <th>Coef</th>
                                <th>Moyenne</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notes_sem as $n):
                                $vals = array_filter([$n['cc1'], $n['cc2'], $n['tp'], $n['examen']], fn($v) => $v !== null);
                                $moy_mod = count($vals) > 0 ? array_sum($vals) / count($vals) : null;
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($n['matiere']) ?></strong></td>
                                <td><?= $n['cc1']    !== null ? number_format($n['cc1'],    2) : '<span style="color:#bbb">—</span>' ?></td>
                                <td><?= $n['cc2']    !== null ? number_format($n['cc2'],    2) : '<span style="color:#bbb">—</span>' ?></td>
                                <td><?= $n['tp']     !== null ? number_format($n['tp'],     2) : '<span style="color:#bbb">—</span>' ?></td>
                                <td><?= $n['examen'] !== null ? number_format($n['examen'], 2) : '<span style="color:#bbb">—</span>' ?></td>
                                <td><span class="groupe-tag"><?= $n['coefficient'] ?></span></td>
                                <td>
                                    <?php if ($moy_mod !== null): ?>
                                        <strong style="color:<?= $moy_mod >= 10 ? '#16a34a' : '#dc2626' ?>">
                                            <?= number_format($moy_mod, 2) ?>
                                        </strong>
                                    <?php else: ?>
                                        <span style="color:#bbb">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php $first = false; endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Bouton relevé -->
        <div style="margin-top:8px;">
            <a href="releve.php" class="btn-download-main">📄 Télécharger le relevé de notes</a>
        </div>

    </div><!-- /page-content -->

    <?php include '../footer.php'; ?>
</div><!-- /main -->

<script>
function showTab(id, btn) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById(id).style.display = 'block';
    btn.classList.add('active');
}
</script>
</body>
</html>