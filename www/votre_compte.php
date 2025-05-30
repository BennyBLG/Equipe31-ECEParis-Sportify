<?php
session_start();

// Configuration de la base de données (directement dans le fichier)
define('DB_HOST', 'localhost');
define('DB_NAME', 'sportify');
define('DB_USER', 'root');
define('DB_PASS', ''); // Mot de passe vide pour WAMP par défaut

// Fonction de connexion à la base de données
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        // Si la BDD ne fonctionne pas, utiliser les données de test
        return null;
    }
}

// Fonction pour hasher les mots de passe
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Fonction pour vérifier les mots de passe
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Données de test (en cas de problème avec la BDD)
$users_test = [
    'admin@sportify.com' => ['password' => 'admin123', 'role' => 'admin', 'nom' => 'ADMINISTRATEUR', 'prenom' => 'Sportify'],
    'guy.dumais@sportify.com' => ['password' => 'coach123', 'role' => 'coach', 'nom' => 'DUMAIS', 'prenom' => 'Guy', 'specialite' => 'Musculation'],
    'marie.martin@sportify.com' => ['password' => 'coach123', 'role' => 'coach', 'nom' => 'MARTIN', 'prenom' => 'Marie', 'specialite' => 'Fitness'],
    'paul.bernard@sportify.com' => ['password' => 'coach123', 'role' => 'coach', 'nom' => 'BERNARD', 'prenom' => 'Paul', 'specialite' => 'Tennis'],
    'client@test.com' => ['password' => 'client123', 'role' => 'client', 'nom' => 'DUPONT', 'prenom' => 'Jean', 'adresse' => '123 Rue Test, Paris', 'carte_etudiant' => 'ETU2025001'],
    'marie.client@test.com' => ['password' => 'client123', 'role' => 'client', 'nom' => 'DURAND', 'prenom' => 'Marie', 'adresse' => '456 Avenue Test, Lyon', 'carte_etudiant' => 'ETU2025002']
];

$error_message = '';
$success_message = '';
$using_database = false;

// Traitement de la connexion
if ($_POST && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Essayer d'abord avec la base de données
    $pdo = getDBConnection();
    
    if ($pdo !== null) {
        try {
            // Utiliser la vraie base de données
            $stmt = $pdo->prepare("SELECT u.*, c.specialite FROM users u 
                                  LEFT JOIN coachs c ON u.id = c.user_id 
                                  WHERE u.email = ? AND u.statut = 'actif'");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && verifyPassword($password, $user['password'])) {
                // Connexion réussie avec BDD
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_info'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'nom' => $user['nom'],
                    'prenom' => $user['prenom'],
                    'telephone' => $user['telephone'],
                    'specialite' => $user['specialite'] ?? null
                ];
                $using_database = true;
                $success_message = 'Connexion réussie avec la base de données !';
            } else {
                $error_message = 'Email ou mot de passe incorrect (BDD).';
            }
        } catch (PDOException $e) {
            // Si erreur BDD, essayer avec les données de test
            $pdo = null;
        }
    }
    
    // Si pas de BDD ou erreur, utiliser les données de test
    if ($pdo === null && !$using_database) {
        if (isset($users_test[$email]) && $users_test[$email]['password'] === $password) {
            // Connexion réussie avec données de test
            $user = $users_test[$email];
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_info'] = array_merge(['email' => $email], $user);
            $success_message = 'Connexion réussie avec données de test !';
        } else {
            $error_message = 'Email ou mot de passe incorrect.';
        }
    }
    
    // Redirection si connexion réussie
    if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']) {
        $user = $_SESSION['user_info'];
        
        $redirect_files = [
            'admin' => 'dashboard_admin.php',
            'coach' => 'dashboard_coach.php',
            'client' => 'dashboard_client.php'
        ];
        
        $target_file = $redirect_files[$user['role']];
        
        // Redirection directe vers le fichier (même s'il n'existe pas encore)
        header('Location: ' . $target_file);
        exit;
    }
}

// Traitement de la déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    $success_message = 'Vous avez été déconnecté avec succès.';
}

// Traitement de l'inscription client
if ($_POST && isset($_POST['register'])) {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $adresse = trim($_POST['adresse']);
    $carte_etudiant = trim($_POST['carte_etudiant']);
    
    if ($password !== $confirm_password) {
        $error_message = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($password) < 6) {
        $error_message = 'Le mot de passe doit contenir au moins 6 caractères.';
    } else {
        $pdo = getDBConnection();
        
        if ($pdo !== null) {
            try {
                // Vérifier si l'email existe déjà
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                
                if ($stmt->fetch()) {
                    $error_message = 'Cet email est déjà utilisé.';
                } else {
                    // Commencer une transaction
                    $pdo->beginTransaction();
                    
                    // Insérer l'utilisateur
                    $stmt = $pdo->prepare("INSERT INTO users (email, password, role, nom, prenom) VALUES (?, ?, 'client', ?, ?)");
                    $stmt->execute([$email, hashPassword($password), $nom, $prenom]);
                    $user_id = $pdo->lastInsertId();
                    
                    // Insérer les informations client
                    $stmt = $pdo->prepare("INSERT INTO clients (user_id, adresse, carte_etudiant) VALUES (?, ?, ?)");
                    $stmt->execute([$user_id, $adresse, $carte_etudiant]);
                    
                    $pdo->commit();
                    $success_message = 'Inscription réussie dans la base de données ! Vous pouvez maintenant vous connecter.';
                }
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error_message = 'Erreur lors de l\'inscription. Veuillez réessayer.';
            }
        } else {
            // Pas de BDD disponible
            if (isset($users_test[$email])) {
                $error_message = 'Cet email est déjà utilisé (données de test).';
            } else {
                $success_message = 'Inscription simulée réussie ! (Base de données non disponible)';
            }
        }
    }
}

// Vérifier l'état de la base de données
$db_status = getDBConnection() !== null ? 'connected' : 'unavailable';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Compte - Sportify</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .top-bar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 2rem;
        }

        nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        nav a:hover {
            background: #007bff;
            color: white;
            transform: translateY(-2px);
        }

        .hero {
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.9), rgba(118, 75, 162, 0.9));
            color: white;
            text-align: center;
            padding: 120px 0 80px;
            margin-top: 60px;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .db-status {
            position: fixed;
            top: 70px;
            right: 20px;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            z-index: 1001;
        }

        .db-status.connected {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .db-status.unavailable {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .role-selection {
            padding: 80px 0;
        }

        .role-selection h2 {
            text-align: center;
            color: white;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .role-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .role-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .role-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .role-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .role-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .role-card p {
            color: #666;
            margin-bottom: 1.5rem;
        }

        .role-card ul {
            list-style: none;
            text-align: left;
            margin-bottom: 2rem;
        }

        .role-card li {
            padding: 0.5rem 0;
            color: #555;
        }

        .btn {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 123, 255, 0.3);
        }

        .login-section, .register-section {
            padding: 80px 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .login-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            max-width: 1000px;
            margin: 0 auto;
            align-items: start;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h2 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #666;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .btn-lg {
            width: 100%;
            padding: 1rem;
            font-size: 1.1rem;
        }

        .test-info, .register-info {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .test-info h3, .register-info h3 {
            color: #333;
            margin-bottom: 1rem;
        }

        .test-accounts {
            display: grid;
            gap: 1rem;
        }

        .test-account {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            border-left: 4px solid #007bff;
        }

        .test-account h4 {
            color: #333;
            margin-bottom: 0.5rem;
        }

        .test-account p {
            margin: 0.25rem 0;
            color: #666;
            font-size: 0.9rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
        }

        .login-footer a {
            color: #007bff;
            text-decoration: none;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        footer {
            background: rgba(0, 0, 0, 0.8);
            color: white;
            text-align: center;
            padding: 2rem 0;
        }

        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            nav ul {
                flex-direction: column;
                gap: 1rem;
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Statut de la base de données -->
    <div class="db-status <?php echo $db_status; ?>">
        <?php if ($db_status === 'connected'): ?>
            🗃️ BDD Connectée
        <?php else: ?>
            🧪 Mode Test
        <?php endif; ?>
    </div>

    <!-- Navigation -->
    <div class="top-bar">
        <div class="header-container">
            <nav>
                <ul>
                    <li><a href="accueil.php">Accueil</a></li>
                    <li><a href="tout_parcourir.php">Tout Parcourir</a></li>
                    <li><a href="recherche.php">Recherche</a></li>
                    <li><a href="rendez_vous.php">Rendez-vous</a></li>
                    <li><a href="votre_compte.php">Votre Compte</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Section Hero -->
    <section class="hero hero-secondary">
        <div class="hero-content">
            <h1>👤 Votre Compte</h1>
            <p class="hero-subtitle">Connectez-vous à votre espace personnel Sportify</p>
        </div>
    </section>

    <!-- Section de sélection de rôle -->
    <section class="role-selection">
        <div class="container">
            <h2>Quel est votre profil ?</h2>
            <div class="role-cards">
                <div class="role-card" data-role="client">
                    <div class="role-icon">👥</div>
                    <h3>Client Sportify</h3>
                    <p>Réservez vos séances avec nos coachs professionnels</p>
                    <ul>
                        <li>✓ Consulter les coachs disponibles</li>
                        <li>✓ Réserver des créneaux</li>
                        <li>✓ Historique des séances</li>
                        <li>✓ Communication avec les coachs</li>
                    </ul>
                    <button class="btn btn-primary" onclick="showLoginForm('client')">Se connecter</button>
                </div>

                <div class="role-card" data-role="coach">
                    <div class="role-icon">🏋️‍♂️</div>
                    <h3>Coach Sportif</h3>
                    <p>Gérez vos clients et vos disponibilités</p>
                    <ul>
                        <li>✓ Voir vos consultations</li>
                        <li>✓ Gérer votre planning</li>
                        <li>✓ Communiquer avec les clients</li>
                        <li>✓ Mettre à jour votre profil</li>
                    </ul>
                    <button class="btn btn-primary" onclick="showLoginForm('coach')">Se connecter</button>
                </div>

                <div class="role-card" data-role="admin">
                    <div class="role-icon">🛡️</div>
                    <h3>Administrateur</h3>
                    <p>Administration complète de la plateforme</p>
                    <ul>
                        <li>✓ Gestion des coachs</li>
                        <li>✓ Génération CV XML</li>
                        <li>✓ Configuration salle de sport</li>
                        <li>✓ Statistiques et rapports</li>
                    </ul>
                    <button class="btn btn-primary" onclick="showLoginForm('admin')">Se connecter</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Formulaire de connexion -->
    <section class="login-section" id="login-section" style="display: none;">
        <div class="container">
            <div class="login-container">
                <div class="login-card">
                    <div class="login-header">
                        <h2 id="login-title">Connexion</h2>
                        <p id="login-subtitle">Accédez à votre espace personnel</p>
                    </div>

                    <?php if ($error_message): ?>
                        <div class="alert alert-error">
                            <span class="alert-icon">❌</span>
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success_message): ?>
                        <div class="alert alert-success">
                            <span class="alert-icon">✅</span>
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="login-form">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="votre.email@exemple.com" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <input type="password" id="password" name="password" placeholder="••••••••" required>
                        </div>

                        <div class="form-group">
                            <button type="submit" name="login" class="btn btn-primary btn-lg">
                                <span>🔓</span> Se connecter
                            </button>
                        </div>
                    </form>

                    <div class="login-footer">
                        <p id="register-link-container">
                            <a href="#" onclick="showRegisterForm()" id="register-link">Pas encore de compte ? S'inscrire</a>
                        </p>
                        <p><a href="#" onclick="hideLoginForm()">← Retour à la sélection</a></p>
                    </div>
                </div>

                <!-- Informations de test -->
                <div class="test-info">
                    <h3><?php echo $db_status === 'connected' ? '🔑 Comptes de la BDD' : '🧪 Comptes de test'; ?></h3>
                    <div class="test-accounts">
                        <div class="test-account">
                            <h4>🛡️ Admin</h4>
                            <p><strong>Email:</strong> admin@sportify.com</p>
                            <p><strong>Mot de passe:</strong> <?php echo $db_status === 'connected' ? 'password123' : 'admin123'; ?></p>
                        </div>
                        <div class="test-account">
                            <h4>🏋️‍♂️ Coach</h4>
                            <p><strong>Email:</strong> guy.dumais@sportify.com</p>
                            <p><strong>Mot de passe:</strong> <?php echo $db_status === 'connected' ? 'password123' : 'coach123'; ?></p>
                        </div>
                        <div class="test-account">
                            <h4>👤 Client</h4>
                            <p><strong>Email:</strong> client@test.com</p>
                            <p><strong>Mot de passe:</strong> <?php echo $db_status === 'connected' ? 'password123' : 'client123'; ?></p>
                        </div>
                    </div>
                    <p><small><?php echo $db_status === 'connected' ? '✅ Connexion BDD active' : '⚠️ BDD indisponible - Mode test activé'; ?></small></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Formulaire d'inscription client -->
    <section class="register-section" id="register-section" style="display: none;">
        <div class="container">
            <div class="login-container">
                <div class="login-card">
                    <div class="login-header">
                        <h2>Créer un compte client</h2>
                        <p>Rejoignez la communauté Sportify</p>
                    </div>

                    <form method="POST" action="" class="register-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="prenom">Prénom</label>
                                <input type="text" id="prenom" name="prenom" placeholder="Jean" required>
                            </div>
                            <div class="form-group">
                                <label for="nom">Nom</label>
                                <input type="text" id="nom" name="nom" placeholder="DUPONT" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email_register">Email</label>
                            <input type="email" id="email_register" name="email" placeholder="jean.dupont@email.com" required>
                        </div>

                        <div class="form-group">
                            <label for="adresse">Adresse complète</label>
                            <textarea id="adresse" name="adresse" placeholder="123 Rue de la Paix, 75001 Paris" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="carte_etudiant">Numéro de carte étudiante</label>
                            <input type="text" id="carte_etudiant" name="carte_etudiant" placeholder="ETU2025XXX" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="password_register">Mot de passe</label>
                                <input type="password" id="password_register" name="password" placeholder="••••••••" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirmer mot de passe</label>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" name="register" class="btn btn-primary btn-lg">
                                <span>📝</span> Créer mon compte
                            </button>
                        </div>
                    </form>

                    <div class="login-footer">
                        <p><a href="#" onclick="showLoginForm('client')">← Retour à la connexion</a></p>
                    </div>
                </div>

                <div class="register-info">
                    <h3>🎯 Pourquoi rejoindre Sportify ?</h3>
                    <ul>
                        <li>✅ Accès à des coachs professionnels certifiés</li>
                        <li>✅ Réservation en ligne 24h/24</li>
                        <li>✅ Suivi personnalisé de vos progrès</li>
                        <li>✅ Communication directe avec vos coachs</li>
                        <li>✅ Tarifs préférentiels étudiants</li>
                        <li>✅ Annulation gratuite jusqu'à 24h avant</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Sportify - Votre plateforme sportive</p>
        </div>
    </footer>

    <script>
        function showLoginForm(role) {
            document.getElementById('login-section').style.display = 'block';
            document.querySelector('.role-selection').style.display = 'none';
            
            const titles = {
                'client': 'Connexion Client',
                'coach': 'Connexion Coach',
                'admin': 'Connexion Administrateur'
            };
            
            const subtitles = {
                'client': 'Accédez à votre espace de réservation',
                'coach': 'Gérez vos consultations et clients',
                'admin': 'Administration de la plateforme'
            };
            
            document.getElementById('login-title').textContent = titles[role];
            document.getElementById('login-subtitle').textContent = subtitles[role];
            
            // Masquer le lien d'inscription pour admin et coach
            const registerLink = document.getElementById('register-link-container');
            if (role === 'client') {
                registerLink.style.display = 'block';
            } else {
                registerLink.style.display = 'none';
            }
            
            // Scroll vers le formulaire
            document.getElementById('login-section').scrollIntoView({behavior: 'smooth'});
        }

        function hideLoginForm() {
            document.getElementById('login-section').style.display = 'none';
            document.getElementById('register-section').style.display = 'none';
            document.querySelector('.role-selection').style.display = 'block';
        }

        function showRegisterForm() {
            document.getElementById('login-section').style.display = 'none';
            document.getElementById('register-section').style.display = 'block';
            document.getElementById('register-section').scrollIntoView({behavior: 'smooth'});
        }

        // Animation des cartes de rôle
        document.addEventListener('DOMContentLoaded', function() {
            const roleCards = document.querySelectorAll('.role-card');
            roleCards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('animate-fade-in');
                }, index * 200);
            });
        });

        // Validation du formulaire d'inscription
        const registerForm = document.querySelector('.register-form');
        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                const password = document.getElementById('password_register').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Les mots de passe ne correspondent pas.');
                    return false;
                }
                
                if (password.length < 6) {
                    e.preventDefault();
                    alert('Le mot de passe doit contenir au moins 6 caractères.');
                    return false;
                }
            });
        }
    </script>
</body>
</html>