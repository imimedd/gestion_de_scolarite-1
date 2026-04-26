<?php
session_start();
require_once("connexion.php");

$error_message = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $identifiant = trim($_POST['email']);
    $password = $_POST['password'];
    
    $login_attempted = false;

    // 1. Si c'est un matricule (que des chiffres) → étudiant
    if (is_numeric($identifiant)) {
        try {
            $sqlEtu = "SELECT * FROM etudiants WHERE matricule = ?";
            $stmtEtu = $pdo->prepare($sqlEtu);
            $stmtEtu->execute([$identifiant]);
            $etudiant = $stmtEtu->fetch();

            if ($etudiant) {
                $login_attempted = true;
                if ($password == $etudiant['password']) {
                    $_SESSION['matricule'] = $etudiant['matricule'];
                    $_SESSION['role'] = 'etudiant';
                    header("Location: etudiant-interface/dashboard_etudiants.php");
                    exit();
                } else {
                    $error_message = "Mot de passe incorrect";
                }
            } else {
                $error_message = "Étudiant non trouvé";
            }
        } catch (PDOException $e) {
            $error_message = "Erreur base de données";
        }
    }

    // 2. Sinon c'est un email → enseignant
    if (!$login_attempted) {
        try {
            $sqlEns = "SELECT * FROM enseignants WHERE email = ?";
            $stmtEns = $pdo->prepare($sqlEns);
            $stmtEns->execute([$identifiant]);
            $enseignant = $stmtEns->fetch();

            if ($enseignant) {
                $login_attempted = true;
                if ($password == $enseignant['password']) {
                    $_SESSION['user_id'] = $enseignant['matricule'];
                    $_SESSION['role'] = 'enseignant';
                    $_SESSION['nom'] = trim($enseignant['nom']);
                    $_SESSION['prenom'] = trim($enseignant['prenom']);
                    header("Location: prof-interface/index.php");
                    exit();
                } else {
                    $error_message = "Mot de passe incorrect";
                }
            }
        } catch (PDOException $e) {}
    }

    // 3. Sinon → admin
    if (!$login_attempted) {
        try {
            $sql = "SELECT * FROM administrateurs WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$identifiant]);
            $user = $stmt->fetch();

            if ($user) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'] ?? 'admin';
                    header("Location: admin-interface/acceuille1.php");
                    exit();
                } else {
                    $error_message = "Mot de passe incorrect";
                }
            } else {
                $error_message = "Utilisateur non trouvé";
            }
        } catch (PDOException $e) {
            $error_message = "Utilisateur non trouvé";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — USTHB</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a2a4a;
            --primary-light: #2c3e6a;
            --accent: #f39c12;
            --bg-start: #f4f6fb;
            --bg-end: #e2e8f0;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'IBM Plex Sans', sans-serif;
        }

        body {
             background-image: url('image/usthb2.png');
            background-size: cover;        
            background-repeat: no-repeat; 
            background-position: center;  
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
          
        }

        .login-container {
                background: rgba(240, 240, 240, 0.10); /* was 25 */
                border: 0.3px solid rgba(255, 255, 255, 0.25); /* was 64 */
                backdrop-filter: blur(5px);
                padding: 2.5rem;
                border-radius: 16px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.08),
                            0 1px 3px rgba(0,0,0,0.05);
                width: 100%;
                max-width: 400px;
                filter: saturate(75%);
                transition: transform 0.3s ease;
            }
        .login-container:hover {
            transform: translateY(-5px);
        }

        .logo-area {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo-area img {
            width: 80px;
            height: auto;
            margin-bottom: 1rem;
        }

        .logo-area h1 {
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .logo-area p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            font-size: 0.9rem;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 1rem;
            background: #f8fafc;
            color: var(--text-dark);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-light);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(26, 42, 74, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26, 42, 74, 0.2);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }

        .footer-text {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.85rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="logo-area">
        <h1>Connexion</h1>
        <p>Accès au portail de gestion de scolarité</p>
    </div>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-error">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="email">Matricule / Email</label>
            <input type="text" id="email" name="email" class="form-control" placeholder="prenom.nom@usthb.dz" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-login">
            Se connecter
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="M12 5l7 7-7 7"></path></svg>
        </button>
    </form>
    
    <div class="footer-text">
        &copy; <?= date("Y") ?> USTHB. Tous droits réservés.
    </div>
</div>

</body>
</html>