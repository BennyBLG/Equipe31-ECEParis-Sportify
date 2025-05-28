<?php
session_start();

// Utilisateurs de test (à remplacer par BDD plus tard)
$users_test = [
    // Administrateurs
    'admin@sportify.com' => ['password' => 'admin123', 'role' => 'admin', 'nom' => 'ADMINISTRATEUR', 'prenom' => 'Sportify'],
    
    // Coachs
    'guy.dumais@sportify.com' => ['password' => 'coach123', 'role' => 'coach', 'nom' => 'DUMAIS', 'prenom' => 'Guy', 'specialite' => 'Musculation'],
    'marie.martin@sportify.com' => ['password' => 'coach123', 'role' => 'coach', 'nom' => 'MARTIN', 'prenom' => 'Marie', 'specialite' => 'Fitness'],
    'paul.bernard@sportify.com' => ['password' => 'coach123', 'role' => 'coach', 'nom' => 'BERNARD', 'prenom' => 'Paul', 'specialite' => 'Tennis'],
    
    // Clients
    'client@test.com' => ['password' => 'client123', 'role' => 'client', 'nom' => 'DUPONT', 'prenom' => 'Jean', 'adresse' => '123 Rue Test, Paris', 'carte_etudiant' => 'ETU2025001'],
    'marie.client@test.com' => ['password' => 'client123', 'role' => 'client', 'nom' => 'DURAND', 'prenom' => 'Marie', 'adresse' => '456 Avenue Test, Lyon', 'carte_etudiant' => 'ETU2025002']
];

$error_message = '';
$success_message = '';

// Traitement de la connexion
if ($_POST && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if (isset($users_test[$email]) && $users_test[$email]['password'] === $password) {
        // Connexion réussie
        $user = $users_test[$email];
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_info'] = array_merge(['email' => $email], $user);
        
        // Redirection selon le rôle
        switch ($user['role']) {
            case 'admin':
                header('Location: dashboard_admin.php');
                break;
            case 'coach':
                header('Location: dashboard_coach.php');
                break;
            case 'client':
                header('Location: dashboard_client.php');
                break;
        }
        exit;
    } else {
        $error_message = 'Email ou mot de passe incorrect.';
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
    } elseif (isset($users_test[$email])) {
        $error_message = 'Cet email est déjà utilisé.';
    } else {
        // Inscription réussie (simulation)
        $success_message = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Compte - Sportify</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
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
                    <h3>🧪 Comptes de test</h3>
                    <div class="test-accounts">
                        <div class="test-account">
                            <h4>👤 Client</h4>
                            <p><strong>Email:</strong> client@test.com</p>
                            <p><strong>Mot de passe:</strong> client123</p>
                        </div>
                        <div class="test-account">
                            <h4>🏋️‍♂️ Coach</h4>
                            <p><strong>Email:</strong> guy.dumais@sportify.com</p>
                            <p><strong>Mot de passe:</strong> coach123</p>
                        </div>
                        <div class="test-account">
                            <h4>🛡️ Admin</h4>
                            <p><strong>Email:</strong> admin@sportify.com</p>
                            <p><strong>Mot de passe:</strong> admin123</p>
                        </div>
                    </div>
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