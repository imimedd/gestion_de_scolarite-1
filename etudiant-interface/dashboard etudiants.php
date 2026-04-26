<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - USTHB</title>
    <link rel="stylesheet" href="../prof-interface/style.css">
</head>
<body>
<?php
include '../config.php';

$matricule = '242431461919'; // à remplacer par $_SESSION['matricule']

// ── Infos étudiant ──────────────────────────────────────────
$stmt = mysqli_prepare($conn, "SELECT * FROM etudiants WHERE matricule = ?");
mysqli_stmt_bind_param($stmt, 's', $matricule);
mysqli_stmt_execute($stmt);
$etudiant = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$numero_etudiant = $etudiant['numero'];    // ex: 176
$groupe_td       = $etudiant['groupe_td']; // ex: 1

// ── Notes réelles depuis la BDD ─────────────────────────────
// Structure :
//   etudiants.numero → notes.id_etudiant
//   notes.id_evaluation → evaluations.id_evaluation
//   evaluations.id_module → modules.id_module
//   evaluations.id_groupe = groupe_td de l'étudiant
$sql_notes = "
    SELECT
        m.nom_module                                         AS matiere,
        m.semestre,
        m.coef                                              AS coefficient,
        MAX(CASE WHEN ev.type_eval = 'Contrôle 1'  THEN n.note END) AS cc1,
        MAX(CASE WHEN ev.type_eval = 'Contrôle 2'  THEN n.note END) AS cc2,
        MAX(CASE WHEN ev.type_eval = 'TP'           THEN n.note END) AS tp,
        MAX(CASE WHEN ev.type_eval = 'Examen final' THEN n.note END) AS examen
    FROM notes n
    JOIN evaluations ev ON ev.id_evaluation = n.id_evaluation
    JOIN modules m      ON m.id_module      = ev.id_module
    WHERE n.id_etudiant = ?
      AND ev.id_groupe  = ?
    GROUP BY m.id_module, m.nom_module, m.semestre, m.coef
    ORDER BY m.semestre, m.nom_module
";
$stmt2 = mysqli_prepare($conn, $sql_notes);
mysqli_stmt_bind_param($stmt2, 'ii', $numero_etudiant, $groupe_td);
mysqli_stmt_execute($stmt2);
$toutes_notes = mysqli_fetch_all(mysqli_stmt_get_result($stmt2), MYSQLI_ASSOC);

// Grouper par semestre
$notes_par_semestre = [];
foreach ($toutes_notes as $n) {
    $notes_par_semestre[$n['semestre']][] = $n;
}

// ── Calcul moyenne pondérée ─────────────────────────────────
function calculMoyenne($notes) {
    $total = 0; $coefTotal = 0;
    foreach ($notes as $n) {
        $vals = array_filter(
            [$n['cc1'], $n['cc2'], $n['tp'], $n['examen']],
            fn($v) => $v !== null && $v !== ''
        );
        if (empty($vals)) continue;
        $moy_module = array_sum($vals) / count($vals);
        $total      += $moy_module * $n['coefficient'];
        $coefTotal  += $n['coefficient'];
    }
    return $coefTotal > 0 ? round($total / $coefTotal, 2) : null;
}

$moyenne_generale = calculMoyenne($toutes_notes);
?>

<div class="sidebar">
    <div class="sidebar-logo" style="text-align:center; margin-bottom:20px;">
        <img src="../image/logo.jpg" alt="USTHB" style="width:80px; border-radius:50%;">
        <p style="color:#fff; font-weight:600; margin-top:10px;">Espace Étudiant</p>
    </div>
    <div class="sidebar-nav">
        <a href="dashboard etudiants.php" class="active">Tableau de bord</a>
        <a href="releve.php">Relevé de notes</a>
        <a href="../index.php" class="btn-deconnexion">Déconnexion</a>
    </div>
</div>

<div class="main">
    <div class="header">
        <div class="header-left">
            <div class="header-icon">📊</div>
            <p style="color:#000; font-size:16px;">Tableau de bord</p>
        </div>
        <div class="year-badge">2025 / 2026</div>
    </div>

    <div class="page-content">
        <div class="dashboard">

            <div class="dashboard-header">
                <h2>Bonjour, <?= htmlspecialchars($etudiant['prenom'] ?? 'Étudiant') ?> 👋</h2>
                <p>Voici votre espace étudiant</p>
            </div>

            <div class="dashboard-cards">

                <!-- Carte 1 : Infos personnelles -->
                <div class="card-dash">
                    <h3>Informations personnelles</h3>
                    <p><strong>Nom :</strong> <?= htmlspecialchars($etudiant['nom']) ?></p>
                    <p><strong>Prénom :</strong> <?= htmlspecialchars($etudiant['prenom']) ?></p>
                    <p><strong>Matricule :</strong> <?= htmlspecialchars($etudiant['matricule']) ?></p>
                    <p><strong>Spécialité :</strong> <?= htmlspecialchars($etudiant['specialite']) ?></p>
                    <p><strong>Niveau :</strong> <?= htmlspecialchars($etudiant['palier']) ?></p>
                    <p><strong>Section :</strong> <?= htmlspecialchars($etudiant['section']) ?></p>
                    <p><strong>Groupe TD :</strong> <?= htmlspecialchars($etudiant['groupe_td']) ?></p>
                    <p><strong>État :</strong> <?= htmlspecialchars($etudiant['etat']) ?></p>
                </div>

                <!-- Carte 2 : Moyenne générale -->
                <div class="card-dash">
                    <h3>Moyenne générale</h3>
                    <?php if ($moyenne_generale !== null): ?>
                        <p class="moyenn-score"><?= number_format($moyenne_generale, 2) ?> / 20</p>
                        <p class="status">
                            <?php if ($moyenne_generale >= 10): ?>
                                <span class="admis">Admis</span>
                            <?php else: ?>
                                <span class="refuse">Ajourné</span>
                            <?php endif; ?>
                        </p>
                        <?php foreach ($notes_par_semestre as $sem => $notes_sem): ?>
                            <?php $moy_sem = calculMoyenne($notes_sem); ?>
                            <?php if ($moy_sem !== null): ?>
                                <p style="margin-top:10px; font-size:13px; color:#555;">
                                    <?= htmlspecialchars($sem) ?> : <strong><?= number_format($moy_sem, 2) ?></strong>
                                </p>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color:#888; margin-top:10px;">Aucune note disponible.</p>
                    <?php endif; ?>
                </div>

                <!-- Carte 3 : Notes par semestre (onglets dynamiques) -->
                <div class="card-dash">
                    <h3>Notes</h3>
                    <?php if (empty($notes_par_semestre)): ?>
                        <p style="color:#888; margin-top:10px;">Aucune note enregistrée.</p>
                    <?php else: ?>
                        <div class="tabs">
                            <?php $first = true; foreach ($notes_par_semestre as $sem => $unused): ?>
                                <button class="tab <?= $first ? 'active' : '' ?>"
                                        onclick="showTab('sem_<?= md5($sem) ?>', this)">
                                    <?= htmlspecialchars($sem) ?>
                                </button>
                            <?php $first = false; endforeach; ?>
                        </div>

                        <?php $first = true; foreach ($notes_par_semestre as $sem => $notes_sem): ?>
                        <div id="sem_<?= md5($sem) ?>" class="tab-content" <?= $first ? '' : 'style="display:none;"' ?>>
                            <table class="notes-table">
                                <tr>
                                    <th>Matière</th>
                                    <th>CC1</th>
                                    <th>CC2</th>
                                    <th>TP</th>
                                    <th>Examen</th>
                                    <th>Coef</th>
                                </tr>
                                <?php foreach ($notes_sem as $n): ?>
                                <tr>
                                    <td><?= htmlspecialchars($n['matiere']) ?></td>
                                    <td><?= $n['cc1']    !== null ? number_format($n['cc1'],   2) : '—' ?></td>
                                    <td><?= $n['cc2']    !== null ? number_format($n['cc2'],   2) : '—' ?></td>
                                    <td><?= $n['tp']     !== null ? number_format($n['tp'],    2) : '—' ?></td>
                                    <td><?= $n['examen'] !== null ? number_format($n['examen'],2) : '—' ?></td>
                                    <td><?= $n['coefficient'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                        <?php $first = false; endforeach; ?>
                    <?php endif; ?>
                </div>

            </div><!-- /dashboard-cards -->

            <div class="btn-download">
                <a href="releve.php" class="btn-download">Télécharger le relevé de notes</a>
            </div>

        </div><!-- /dashboard -->
    </div><!-- /page-content -->

    <?php include '../footer.php'; ?>
</div><!-- /main -->

<script>
function showTab(id, btn) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab').forEach(el => el.classList.remove('active'));
    document.getElementById(id).style.display = 'block';
    btn.classList.add('active');
}
</script>
</body>
</html>