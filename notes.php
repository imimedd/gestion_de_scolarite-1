<?php
session_start();
require_once("database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ============== AJAX API ==============
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    $action = $_GET['ajax'];

    if ($action === 'get_student_info') {
        $id_etudiant = (int)$_GET['id_etudiant'];

        // Get group id
        $stmt = $pdo->prepare("SELECT id_groupe, moyenne, statut FROM etudiants WHERE id_etudiant = ?");
        $stmt->execute([$id_etudiant]);
        $etu_info = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$etu_info) {
            echo json_encode(['error' => 'Etudiant introuvable.']);
            exit;
        }

        $id_groupe = $etu_info['id_groupe'];

        // Get modules for this group
        $stmtMod = $pdo->prepare("
            SELECT m.id_module, m.nom_module, m.code_module 
            FROM module_groupe mg
            JOIN modules m ON mg.id_module = m.id_module
            WHERE mg.id_groupe = ?
        ");
        $stmtMod->execute([$id_groupe]);
        $modules = $stmtMod->fetchAll(PDO::FETCH_ASSOC);

        // Get existing notes for this student
        $stmtNotes = $pdo->prepare("
            SELECT e.id_module, e.type_eval, n.note 
            FROM notes n
            JOIN evaluations e ON n.id_evaluation = e.id_evaluation
            WHERE n.id_etudiant = ?
        ");
        $stmtNotes->execute([$id_etudiant]);
        $existing_notes = $stmtNotes->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'id_groupe' => $id_groupe,
            'moyenne_generale' => $etu_info['moyenne'],
            'statut' => $etu_info['statut'],
            'modules' => $modules,
            'notes' => $existing_notes
        ]);
        exit;
    }

    if ($action === 'save_notes' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $id_etudiant = (int)$input['id_etudiant'];
        $id_groupe = (int)$input['id_groupe'];
        $id_module = (int)$input['id_module'];
        $notes = $input['notes']; // array like ['Contrôle 1' => 12, 'Examen final' => 15...]

        try {
            $pdo->beginTransaction();

            foreach ($notes as $type_eval => $valeur_note) {
                if ($valeur_note === '' || $valeur_note === null) continue;

                // 1. Get or Create Evaluation
                $stmtEval = $pdo->prepare("SELECT id_evaluation FROM evaluations WHERE id_module=? AND id_groupe=? AND type_eval=?");
                $stmtEval->execute([$id_module, $id_groupe, $type_eval]);
                $id_eval = $stmtEval->fetchColumn();

                if (!$id_eval) {
                    $pdo->prepare("INSERT INTO evaluations (id_module, id_groupe, type_eval, date_eval) VALUES (?, ?, ?, CURDATE())")
                        ->execute([$id_module, $id_groupe, $type_eval]);
                    $id_eval = $pdo->lastInsertId();
                }

                // 2. Insert or Update Note
                $stmtCheck = $pdo->prepare("SELECT id_note FROM notes WHERE id_etudiant=? AND id_evaluation=?");
                $stmtCheck->execute([$id_etudiant, $id_eval]);
                $id_note = $stmtCheck->fetchColumn();

                if ($id_note) {
                    $pdo->prepare("UPDATE notes SET note=? WHERE id_note=?")->execute([$valeur_note, $id_note]);
                } else {
                    $pdo->prepare("INSERT INTO notes (id_etudiant, id_evaluation, note) VALUES (?, ?, ?)")
                        ->execute([$id_etudiant, $id_eval, $valeur_note]);
                }
            }

            // 3. Calcul automatique moyenne
            $stmtAvg = $pdo->prepare("SELECT AVG(note) as moyenne FROM notes WHERE id_etudiant=?");
            $stmtAvg->execute([$id_etudiant]);
            $moyenne = $stmtAvg->fetchColumn();

            $statut = ($moyenne !== null && $moyenne >= 10) ? "Admis" : (($moyenne !== null) ? "Ajourné" : null);

            $pdo->prepare("UPDATE etudiants SET moyenne=?, statut=? WHERE id_etudiant=?")
                ->execute([$moyenne, $statut, $id_etudiant]);

            $pdo->commit();
            echo json_encode(['success' => true, 'moyenne' => $moyenne, 'statut' => $statut]);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }
}
// ======================================

// Étudiants pour la liste principale
$etudiants = $pdo->query("SELECT id_etudiant, matricule, nom, prenom FROM etudiants ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Notes — Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a2a4a;
            --primary-light: #2c3e6a;
            --accent: #f39c12;
            --bg-body: #f4f6fb;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'IBM Plex Sans', sans-serif; }

        body {
            background-color: var(--bg-body);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background: var(--primary);
            color: white;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
        }

        .sidebar h2 { text-align: center; font-size: 1.5rem; margin-bottom: 30px; letter-spacing: 1px; }

        .sidebar a {
            padding: 15px 25px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .sidebar a:hover, .sidebar a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--accent);
        }

        .main-content {
            flex: 1;
            padding: 40px;
            height: 100vh;
            overflow-y: auto;
        }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { color: var(--primary); font-size: 1.8rem; }

        .card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 24px;
        }

        .grid-container {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 24px;
        }

        @media (max-width: 900px) { .grid-container { grid-template-columns: 1fr; } }

        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem; }
        .form-control {
            width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: white;
        }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(26, 42, 74, 0.1); }
        
        .btn {
            padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 8px;
            font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.3s;
        }
        .btn:hover { background: var(--primary-light); }
        .btn:disabled { background: #cbd5e1; cursor: not-allowed; }

        .note-input-group { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px; }
        
        #moyenne-display {
            font-size: 2rem; font-weight: 700; color: var(--primary); margin-top: 10px;
        }
        
        .badge {
            padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; vertical-align: middle; margin-left: 10px;
        }
        .badge.admis { background: #dcfce7; color: #166534; }
        .badge.ajourne { background: #fee2e2; color: #b91c1c; }

        /* ANIMATIONS */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn 0.4s ease forwards; }

        #notes-container { display: none; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Portal</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="etudiants.php">Étudiants</a>
    <a href="admin-interface/Gestion_des_enseignants.php">Enseignants</a>
    <a href="modules.php">Modules</a>
    <a href="notes.php" class="active">Gestion des Notes</a>
    <a href="inscriptions.php">Inscriptions</a>
    <a href="logout.php">Déconnexion</a>
</div>

<div class="main-content">
    <div class="header">
        <h1>Saisie & Gestion des Notes</h1>
    </div>

    <div class="grid-container">
        
        <!-- SÉLECTION ÉTUDIANT -->
        <div class="card">
            <h2 style="font-size: 1.25rem; margin-bottom: 20px; color: var(--primary);">1. Sélection de l'étudiant</h2>
            
            <div class="form-group">
                <label>Rechercher un étudiant</label>
                <select id="etudiant-select" class="form-control" onchange="loadStudentData()">
                    <option value="">-- Choisir un étudiant --</option>
                    <?php foreach($etudiants as $e): ?>
                        <option value="<?= $e['id_etudiant'] ?>">
                            <?= htmlspecialchars($e['matricule'] . ' - ' . $e['nom'] . ' ' . $e['prenom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="student-stats" style="margin-top: 30px; display: none;" class="fade-in">
                <p style="color: var(--text-muted); font-size: 0.9rem;">Moyenne Générale Actuelle</p>
                <div id="moyenne-display">--/20</div>
                <div id="statut-display" style="margin-top: 10px;"></div>
            </div>
        </div>

        <!-- SAISIE DES NOTES -->
        <div class="card fade-in" id="notes-container">
            <h2 style="font-size: 1.25rem; margin-bottom: 20px; color: var(--primary);">2. Sélection Module & Saisie</h2>
            
            <div class="form-group">
                <label>Module / Matière</label>
                <select id="module-select" class="form-control" onchange="loadModuleInputs()">
                    <option value="">-- Choisir un module --</option>
                </select>
            </div>

            <div id="inputs-container" style="display: none; margin-top: 20px;">
                <hr style="border: 0; border-top: 1px solid #e2e8f0; margin-bottom: 20px;">
                <h3 style="font-size: 1rem; color: var(--text-dark); margin-bottom: 10px;">Évaluations</h3>
                
                <div class="note-input-group">
                    <div class="form-group">
                        <label>Contrôle 1 (CC1)</label>
                        <input type="number" id="note-cc1" class="form-control" min="0" max="20" step="0.25" placeholder="Sur 20">
                    </div>
                    <div class="form-group">
                        <label>Contrôle 2 (CC2)</label>
                        <input type="number" id="note-cc2" class="form-control" min="0" max="20" step="0.25" placeholder="Sur 20">
                    </div>
                    <div class="form-group">
                        <label>Note de TP</label>
                        <input type="number" id="note-tp" class="form-control" min="0" max="20" step="0.25" placeholder="Sur 20">
                    </div>
                    <div class="form-group">
                        <label>Examen Final</label>
                        <input type="number" id="note-examen" class="form-control" min="0" max="20" step="0.25" placeholder="Sur 20">
                    </div>
                </div>

                <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
                    <button class="btn" id="btn-save" onclick="saveNotes()">Enregistrer & Calculer Moyen</button>
                </div>
                <p id="save-status" style="margin-top: 10px; font-weight: 600; text-align: right;"></p>
            </div>
        </div>

    </div>
</div>

<script>
    let currentStudentData = null;

    async function loadStudentData() {
        const id_etudiant = document.getElementById('etudiant-select').value;
        const container = document.getElementById('notes-container');
        const stats = document.getElementById('student-stats');
        
        if (!id_etudiant) {
            container.style.display = 'none';
            stats.style.display = 'none';
            return;
        }

        const res = await fetch(`notes.php?ajax=get_student_info&id_etudiant=${id_etudiant}`);
        const data = await res.json();
        
        if (data.error) { alert(data.error); return; }

        currentStudentData = data;
        
        // Update stats
        updateStatsUI(data.moyenne_generale, data.statut);
        stats.style.display = 'block';

        // Update module select
        const modSelect = document.getElementById('module-select');
        modSelect.innerHTML = '<option value="">-- Choisir un module --</option>';
        data.modules.forEach(m => {
            modSelect.innerHTML += `<option value="${m.id_module}">${m.code_module} - ${m.nom_module}</option>`;
        });

        container.style.display = 'block';
        document.getElementById('inputs-container').style.display = 'none';
    }

    function updateStatsUI(moyenne, statut) {
        if (moyenne !== null && moyenne !== undefined) {
            document.getElementById('moyenne-display').innerText = parseFloat(moyenne).toFixed(2) + ' / 20';
            const statutEl = document.getElementById('statut-display');
            if (statut === 'Admis') {
                statutEl.innerHTML = `<span class="badge admis">✅ Admis</span>`;
            } else {
                statutEl.innerHTML = `<span class="badge ajourne">❌ Ajourné</span>`;
            }
        } else {
            document.getElementById('moyenne-display').innerText = '-- / 20';
            document.getElementById('statut-display').innerHTML = '';
        }
    }

    function loadModuleInputs() {
        const id_module = document.getElementById('module-select').value;
        const inputsContainer = document.getElementById('inputs-container');

        if (!id_module) {
            inputsContainer.style.display = 'none';
            return;
        }

        // Reset inputs
        document.getElementById('note-cc1').value = '';
        document.getElementById('note-cc2').value = '';
        document.getElementById('note-tp').value = '';
        document.getElementById('note-examen').value = '';

        // Fill existing notes
        const moduleNotes = currentStudentData.notes.filter(n => n.id_module == id_module);
        
        moduleNotes.forEach(n => {
            if (n.type_eval === 'Contrôle 1') document.getElementById('note-cc1').value = n.note;
            if (n.type_eval === 'Contrôle 2') document.getElementById('note-cc2').value = n.note;
            if (n.type_eval === 'TP') document.getElementById('note-tp').value = n.note;
            if (n.type_eval === 'Examen final') document.getElementById('note-examen').value = n.note;
        });

        inputsContainer.style.display = 'block';
        document.getElementById('save-status').innerText = '';
    }

    async function saveNotes() {
        const id_etudiant = document.getElementById('etudiant-select').value;
        const id_module = document.getElementById('module-select').value;
        
        if (!id_etudiant || !id_module) return;

        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerText = 'Enregistrement...';

        const payload = {
            id_etudiant: id_etudiant,
            id_groupe: currentStudentData.id_groupe,
            id_module: id_module,
            notes: {
                'Contrôle 1': document.getElementById('note-cc1').value,
                'Contrôle 2': document.getElementById('note-cc2').value,
                'TP': document.getElementById('note-tp').value,
                'Examen final': document.getElementById('note-examen').value
            }
        };

        try {
            const res = await fetch('notes.php?ajax=save_notes', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            if (data.success) {
                document.getElementById('save-status').innerText = '✅ Notes enregistrées avec succès !';
                document.getElementById('save-status').style.color = 'green';
                
                // Update average in UI
                updateStatsUI(data.moyenne, data.statut);
                
                // Refresh local data cache so switching back and forth keeps the new values
                payload.notes_keyval = [];
                for (let type in payload.notes) {
                    if (payload.notes[type] !== '') {
                        currentStudentData.notes = currentStudentData.notes.filter(n => !(n.id_module == id_module && n.type_eval == type));
                        currentStudentData.notes.push({ id_module: id_module, type_eval: type, note: payload.notes[type] });
                    }
                }

            } else {
                alert('Erreur: ' + data.error);
            }
        } catch (e) {
            alert('Erreur réseau.');
        }

        btn.disabled = false;
        btn.innerText = 'Enregistrer & Calculer Moyen';
    }
</script>

</body>
</html>
