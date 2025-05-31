<?php
session_start();

// Vérification de l'authentification et du rôle coach
if (!isset($_SESSION["user_logged_in"]) || $_SESSION["role"] !== "coach") {
    header("Location: votre_compte.php");
    exit();
}

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'sportify');
define('DB_USER', 'root');
define('DB_PASS', '');

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
        return null;
    }
}

// Récupération des informations utilisateur
$user_info = $_SESSION["user_info"];
$message = "";
$message_type = "";

// Tentative de connexion à la BDD
$pdo = getDBConnection();
$using_database = ($pdo !== null);

// Données simulées si pas de BDD
$coach_info_fallback = [
    'id' => 1,
    'specialite' => $user_info['specialite'] ?? 'Musculation',
    'bureau' => 'Bureau 12',
    'experience_annees' => 5,
    'description' => 'Coach expérimenté et passionné',
    'note_moyenne' => 4.8,
    'nb_evaluations' => 127
];

$consultations_fallback = [
    [
        'id' => 1,
        'client_nom' => 'DUPONT',
        'client_prenom' => 'Jean',
        'client_email' => 'client@test.com',
        'client_telephone' => '+33234567891',
        'activite_nom' => 'Musculation',
        'date_rdv' => '2025-06-02',
        'heure_debut' => '14:00',
        'heure_fin' => '15:00',
        'lieu' => 'Studio 1',
        'statut' => 'confirme',
        'prix' => 35.00,
        'notes_coach' => '',
        'carte_etudiant' => 'ETU2025001'
    ],
    [
        'id' => 2,
        'client_nom' => 'MARTIN',
        'client_prenom' => 'Sophie',
        'client_email' => 'sophie.martin@email.com',
        'client_telephone' => '+33612345678',
        'activite_nom' => 'Musculation',
        'date_rdv' => '2025-06-03',
        'heure_debut' => '10:00',
        'heure_fin' => '11:00',
        'lieu' => 'Studio 1',
        'statut' => 'planifie',
        'prix' => 35.00,
        'notes_coach' => '',
        'carte_etudiant' => 'ETU2025003'
    ],
    [
        'id' => 3,
        'client_nom' => 'DURAND',
        'client_prenom' => 'Marie',
        'client_email' => 'marie.client@test.com',
        'client_telephone' => '+33123456789',
        'activite_nom' => 'Cours Collectifs',
        'date_rdv' => '2025-06-05',
        'heure_debut' => '16:30',
        'heure_fin' => '17:15',
        'lieu' => 'Studio 2',
        'statut' => 'confirme',
        'prix' => 20.00,
        'notes_coach' => '',
        'carte_etudiant' => 'ETU2025002'
    ]
];

$stats_fallback = [
    'consultations_semaine' => 8,
    'consultations_mois' => 32,
    'clients_actifs' => 15,
    'note_moyenne' => 4.8
];

// Récupération des informations du coach
if ($using_database) {
    try {
        // Récupérer les infos du coach connecté
        $stmt = $pdo->prepare("
            SELECT c.*, u.nom, u.prenom, u.email, u.telephone
            FROM coachs c 
            INNER JOIN users u ON c.user_id = u.id 
            WHERE u.email = ?
        ");
        $stmt->execute([$user_info['email']]);
        $coach_info = $stmt->fetch();
        
        if (!$coach_info) {
            $coach_info = array_merge($coach_info_fallback, [
                'nom' => $user_info['nom'],
                'prenom' => $user_info['prenom'],
                'email' => $user_info['email']
            ]);
            $using_database = false;
        }
    } catch (PDOException $e) {
        $coach_info = array_merge($coach_info_fallback, [
            'nom' => $user_info['nom'],
            'prenom' => $user_info['prenom'],
            'email' => $user_info['email']
        ]);
        $using_database = false;
    }
} else {
    $coach_info = array_merge($coach_info_fallback, [
        'nom' => $user_info['nom'],
        'prenom' => $user_info['prenom'],
        'email' => $user_info['email']
    ]);
}

// Récupération des consultations
if ($using_database && isset($coach_info['id'])) {
    try {
        $stmt = $pdo->prepare("
            SELECT r.*, 
                   u.nom as client_nom, u.prenom as client_prenom, u.email as client_email, u.telephone as client_telephone,
                   a.nom as activite_nom,
                   cl.carte_etudiant
            FROM rendezvous r
            INNER JOIN clients cl ON r.client_id = cl.id
            INNER JOIN users u ON cl.user_id = u.id
            LEFT JOIN activites a ON r.activite_id = a.id
            WHERE r.coach_id = ? AND r.statut != 'annule'
            ORDER BY r.date_rdv, r.heure_debut
        ");
        $stmt->execute([$coach_info['id']]);
        $consultations = $stmt->fetchAll();
        
        // Statistiques
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(CASE WHEN WEEK(date_rdv) = WEEK(NOW()) AND YEAR(date_rdv) = YEAR(NOW()) THEN 1 END) as consultations_semaine,
                COUNT(CASE WHEN MONTH(date_rdv) = MONTH(NOW()) AND YEAR(date_rdv) = YEAR(NOW()) THEN 1 END) as consultations_mois,
                COUNT(DISTINCT client_id) as clients_actifs
            FROM rendezvous 
            WHERE coach_id = ? AND statut != 'annule'
        ");
        $stmt->execute([$coach_info['id']]);
        $stats_data = $stmt->fetch();
        
        $stats = [
            'consultations_semaine' => $stats_data['consultations_semaine'] ?? 0,
            'consultations_mois' => $stats_data['consultations_mois'] ?? 0,
            'clients_actifs' => $stats_data['clients_actifs'] ?? 0,
            'note_moyenne' => $coach_info['note_moyenne'] ?? 0
        ];
        
    } catch (PDOException $e) {
        $consultations = $consultations_fallback;
        $stats = $stats_fallback;
        $using_database = false;
    }
} else {
    $consultations = $consultations_fallback;
    $stats = $stats_fallback;
}

// Traitement de mise à jour des notes
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_notes'])) {
    $consultation_id = (int)$_POST['consultation_id'];
    $notes = trim($_POST['notes_coach']);
    
    if ($using_database) {
        try {
            $stmt = $pdo->prepare("UPDATE rendezvous SET notes_coach = ? WHERE id = ? AND coach_id = ?");
            $stmt->execute([$notes, $consultation_id, $coach_info['id']]);
            
            $message = "Notes mises à jour avec succès !";
            $message_type = "success";
            
            // Recharger les consultations
            $stmt = $pdo->prepare("
                SELECT r.*, 
                       u.nom as client_nom, u.prenom as client_prenom, u.email as client_email, u.telephone as client_telephone,
                       a.nom as activite_nom,
                       cl.carte_etudiant
                FROM rendezvous r
                INNER JOIN clients cl ON r.client_id = cl.id
                INNER JOIN users u ON cl.user_id = u.id
                LEFT JOIN activites a ON r.activite_id = a.id
                WHERE r.coach_id = ? AND r.statut != 'annule'
                ORDER BY r.date_rdv, r.heure_debut
            ");
            $stmt->execute([$coach_info['id']]);
            $consultations = $stmt->fetchAll();
            
        } catch (PDOException $e) {
            $message = "Erreur lors de la mise à jour : " . $e->getMessage();
            $message_type = "error";
        }
    } else {
        $message = "Mise à jour simulée ! (Base de données non disponible)";
        $message_type = "success";
    }
}

// Traitement de changement de statut
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['change_status'])) {
    $consultation_id = (int)$_POST['consultation_id'];
    $new_status = $_POST['new_status'];
    
    if ($using_database) {
        try {
            $stmt = $pdo->prepare("UPDATE rendezvous SET statut = ? WHERE id = ? AND coach_id = ?");
            $stmt->execute([$new_status, $consultation_id, $coach_info['id']]);
            
            $message = "Statut mis à jour avec succès !";
            $message_type = "success";
            
            // Recharger les consultations
            $stmt = $pdo->prepare("
                SELECT r.*, 
                       u.nom as client_nom, u.prenom as client_prenom, u.email as client_email, u.telephone as client_telephone,
                       a.nom as activite_nom,
                       cl.carte_etudiant
                FROM rendezvous r
                INNER JOIN clients cl ON r.client_id = cl.id
                INNER JOIN users u ON cl.user_id = u.id
                LEFT JOIN activites a ON r.activite_id = a.id
                WHERE r.coach_id = ? AND r.statut != 'annule'
                ORDER BY r.date_rdv, r.heure_debut
            ");
            $stmt->execute([$coach_info['id']]);
            $consultations = $stmt->fetchAll();
            
        } catch (PDOException $e) {
            $message = "Erreur lors de la mise à jour : " . $e->getMessage();
            $message_type = "error";
        }
    } else {
        $message = "Changement de statut simulé ! (Base de données non disponible)";
        $message_type = "success";
    }
}

// Séparer les consultations par statut
$consultations_courantes = [];
$consultations_a_venir = [];
$consultations_terminees = [];

foreach ($consultations as $consultation) {
    $date_rdv = new DateTime($consultation['date_rdv']);
    $aujourd_hui = new DateTime();
    
    if ($consultation['statut'] === 'termine') {
        $consultations_terminees[] = $consultation;
    } elseif ($date_rdv->format('Y-m-d') === $aujourd_hui->format('Y-m-d')) {
        $consultations_courantes[] = $consultation;
    } elseif ($date_rdv > $aujourd_hui) {
        $consultations_a_venir[] = $consultation;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Coach - Sportify</title>
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
            max-width: 1400px;
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #007bff;
        }

        nav ul {
            list-style: none;
            display: flex;
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

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-badge {
            background: #28a745;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #c82333;
        }

        .mode-status {
            position: fixed;
            top: 70px;
            right: 20px;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            z-index: 1001;
        }

        .mode-status.database {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .mode-status.fallback {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .hero {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.9), rgba(118, 75, 162, 0.9));
            color: white;
            text-align: center;
            padding: 120px 0 60px;
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

        .coach-info {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 1rem 2rem;
            margin-top: 2rem;
            display: inline-block;
        }

        .dashboard-section {
            padding: 60px 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .stat-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #666;
            font-size: 1.1rem;
        }

        .section-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .consultation-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #28a745;
            transition: all 0.3s ease;
        }

        .consultation-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .consultation-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .client-info {
            flex: 1;
        }

        .client-name {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .consultation-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-planifie {
            background: #fff3cd;
            color: #856404;
        }

        .status-confirme {
            background: #d4edda;
            color: #155724;
        }

        .status-en_cours {
            background: #cce5ff;
            color: #004085;
        }

        .status-termine {
            background: #e2e3e5;
            color: #383d41;
        }

        .communication-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .btn {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #212529;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.8rem;
        }

        .notes-section {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e1e5e9;
        }

        .notes-form {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .notes-form textarea {
            flex: 1;
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            resize: vertical;
            min-height: 80px;
        }

        .notes-form textarea:focus {
            outline: none;
            border-color: #28a745;
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
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

        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 20px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .chatroom {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1rem;
            margin-top: 1rem;
            border: 2px solid #e9ecef;
        }

        .chatroom-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #666;
            font-style: italic;
        }

        footer {
            background: rgba(0, 0, 0, 0.8);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 4rem;
        }

        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 1rem;
            }
            
            nav ul {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .consultation-header {
                flex-direction: column;
                gap: 1rem;
            }
            
            .consultation-details {
                grid-template-columns: 1fr;
            }
            
            .communication-buttons {
                justify-content: center;
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
    <!-- Statut du mode -->
    <div class="mode-status <?php echo $using_database ? 'database' : 'fallback'; ?>">
        <?php if ($using_database): ?>
            🗃️ BDD Connectée
        <?php else: ?>
            🧪 Mode Test
        <?php endif; ?>
    </div>

    <!-- Navigation -->
    <div class="top-bar">
        <div class="header-container">
            <div class="logo">🏋️‍♂️ Sportify Coach</div>
            <nav>
                <ul>
                    <li><a href="accueil.php">Accueil</a></li>
                    <li><a href="tout_parcourir.php">Tout Parcourir</a></li>
                    <li><a href="recherche.php">Recherche</a></li>
                    <li><a href="rendez_vous.php">Rendez-vous</a></li>
                </ul>
            </nav>
            <div class="user-info">
                <div class="user-badge">
                    🏋️‍♂️ <?php echo htmlspecialchars($coach_info['prenom'] . ' ' . $coach_info['nom']); ?>
                </div>
                <a href="votre_compte.php?logout" class="logout-btn">🚪 Déconnexion</a>
            </div>
        </div>
    </div>

    <!-- Section Hero -->
    <section class="hero">
        <div class="hero-content">
            <h1>🏋️‍♂️ Espace Coach</h1>
            <p class="hero-subtitle">Gérez vos consultations et communiquez avec vos clients</p>
            <div class="coach-info">
                <strong>Coach :</strong> <?php echo htmlspecialchars($coach_info['prenom'] . ' ' . $coach_info['nom']); ?><br>
                <strong>Email :</strong> <?php echo htmlspecialchars($coach_info['email']); ?><br>
                <strong>Spécialité :</strong> <?php echo htmlspecialchars($coach_info['specialite']); ?>
            </div>
        </div>
    </section>

    <!-- Dashboard principal -->
    <section class="dashboard-section">
        <div class="container">
            
            <!-- Messages -->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <span class="alert-icon"><?php echo $message_type === 'success' ? '✅' : '❌'; ?></span>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Statistiques -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📅</div>
                    <div class="stat-number"><?php echo $stats['consultations_semaine']; ?></div>
                    <div class="stat-label">Consultations cette semaine</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-number"><?php echo $stats['consultations_mois']; ?></div>
                    <div class="stat-label">Consultations ce mois</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-number"><?php echo $stats['clients_actifs']; ?></div>
                    <div class="stat-label">Clients actifs</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⭐</div>
                    <div class="stat-number"><?php echo number_format($stats['note_moyenne'], 1); ?></div>
                    <div class="stat-label">Note moyenne</div>
                </div>
            </div>

            <!-- Consultations courantes (aujourd'hui) -->
            <?php if (!empty($consultations_courantes)): ?>
            <div class="section-card">
                <h2 class="section-title">
                    🔥 Consultations d'aujourd'hui
                </h2>
                
                <?php foreach ($consultations_courantes as $consultation): ?>
                <div class="consultation-card" style="border-left-color: #ffc107;">
                    <div class="consultation-header">
                        <div class="client-info">
                            <div class="client-name">
                                👤 <?php echo htmlspecialchars($consultation['client_prenom'] . ' ' . $consultation['client_nom']); ?>
                            </div>
                            <span class="status-badge status-<?php echo $consultation['statut']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $consultation['statut'])); ?>
                            </span>
                        </div>
                        <div class="communication-buttons">
                            <button class="btn btn-primary btn-sm" onclick="openChatroom('<?php echo $consultation['client_email']; ?>', '<?php echo htmlspecialchars($consultation['client_prenom'] . ' ' . $consultation['client_nom']); ?>', 'email')">
                                📧 Email
                            </button>
                            <button class="btn btn-secondary btn-sm" onclick="openChatroom('<?php echo $consultation['client_email']; ?>', '<?php echo htmlspecialchars($consultation['client_prenom'] . ' ' . $consultation['client_nom']); ?>', 'text')">
                                💬 Texto
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="openChatroom('<?php echo $consultation['client_email']; ?>', '<?php echo htmlspecialchars($consultation['client_prenom'] . ' ' . $consultation['client_nom']); ?>', 'audio')">
                                🎧 Audio
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="openChatroom('<?php echo $consultation['client_email']; ?>', '<?php echo htmlspecialchars($consultation['client_prenom'] . ' ' . $consultation['client_nom']); ?>', 'video')">
                                📹 Vidéo
                            </button>
                        </div>
                    </div>
                    
                    <div class="consultation-details">
                        <div class="detail-item">
                            <strong>🏃‍♂️ Activité :</strong> <?php echo htmlspecialchars($consultation['activite_nom'] ?? 'Non définie'); ?>
                        </div>
                        <div class="detail-item">
                            <strong>🕒 Horaire :</strong> <?php echo $consultation['heure_debut']; ?> - <?php echo $consultation['heure_fin']; ?>
                        </div>
                        <div class="detail-item">
                            <strong>📍 Lieu :</strong> <?php echo htmlspecialchars($consultation['lieu']); ?>
                        </div>
                        <div class="detail-item">
                            <strong>💰 Prix :</strong> <?php echo number_format($consultation['prix'], 2); ?>€
                        </div>
                        <div class="detail-item">
                            <strong>📞 Téléphone :</strong> <?php echo htmlspecialchars($consultation['client_telephone'] ?? 'Non renseigné'); ?>
                        </div>
                        <div class="detail-item">
                            <strong>🎓 Carte étudiant :</strong> <?php echo htmlspecialchars($consultation['carte_etudiant'] ?? 'Non renseignée'); ?>
                        </div>
                    </div>
                    
                    <!-- Changement de statut -->
                    <div style="margin-top: 1rem;">
                        <form method="POST" style="display: inline-block;">
                            <input type="hidden" name="consultation_id" value="<?php echo $consultation['id']; ?>">
                            <select name="new_status" onchange="this.form.submit()" style="padding: 0.5rem; border-radius: 5px; border: 1px solid #ddd;">
                                <option value="planifie" <?php echo $consultation['statut'] === 'planifie' ? 'selected' : ''; ?>>Planifié</option>
                                <option value="confirme" <?php echo $consultation['statut'] === 'confirme' ? 'selected' : ''; ?>>Confirmé</option>
                                <option value="en_cours" <?php echo $consultation['statut'] === 'en_cours' ? 'selected' : ''; ?>>En cours</option>
                                <option value="termine" <?php echo $consultation['statut'] === 'termine' ? 'selected' : ''; ?>>Terminé</option>
                            </select>
                            <input type="hidden" name="change_status" value="1">
                        </form>
                    </div>
                    
                    <!-- Notes du coach -->
                    <div class="notes-section">
                        <h4>📝 Notes de consultation :</h4>
                        <form method="POST" class="notes-form">
                            <input type="hidden" name="consultation_id" value="<?php echo $consultation['id']; ?>">
                            <textarea name="notes_coach" placeholder="Ajoutez vos notes sur cette consultation..."><?php echo htmlspecialchars($consultation['notes_coach'] ?? ''); ?></textarea>
                            <button type="submit" name="update_notes" class="btn">💾 Sauvegarder</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Consultations à venir -->
            <?php if (!empty($consultations_a_venir)): ?>
            <div class="section-card">
                <h2 class="section-title">
                    📅 Consultations à venir
                </h2>
                
                <?php foreach ($consultations_a_venir as $consultation): ?>
                <div class="consultation-card" style="border-left-color: #007bff;">
                    <div class="consultation-header">
                        <div class="client-info">
                            <div class="client-name">
                                👤 <?php echo htmlspecialchars($consultation['client_prenom'] . ' ' . $consultation['client_nom']); ?>
                            </div>
                            <span class="status-badge status-<?php echo $consultation['statut']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $consultation['statut'])); ?>
                            </span>
                        </div>
                        <div class="communication-buttons">
                            <button class="btn btn-primary btn-sm" onclick="openChatroom('<?php echo $consultation['client_email']; ?>', '<?php echo htmlspecialchars($consultation['client_prenom'] . ' ' . $consultation['client_nom']); ?>', 'email')">
                                📧 Email
                            </button>
                            <button class="btn btn-secondary btn-sm" onclick="openChatroom('<?php echo $consultation['client_email']; ?>', '<?php echo htmlspecialchars($consultation['client_prenom'] . ' ' . $consultation['client_nom']); ?>', 'text')">
                                💬 Texto
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="openChatroom('<?php echo $consultation['client_email']; ?>', '<?php echo htmlspecialchars($consultation['client_prenom'] . ' ' . $consultation['client_nom']); ?>', 'audio')">
                                🎧 Audio
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="openChatroom('<?php echo $consultation['client_email']; ?>', '<?php echo htmlspecialchars($consultation['client_prenom'] . ' ' . $consultation['client_nom']); ?>', 'video')">
                                📹 Vidéo
                            </button>
                        </div>
                    </div>
                    
                    <div class="consultation-details">
                        <div class="detail-item">
                            <strong>📅 Date :</strong> <?php echo date('d/m/Y', strtotime($consultation['date_rdv'])); ?>
                        </div>
                        <div class="detail-item">
                            <strong>🏃‍♂️ Activité :</strong> <?php echo htmlspecialchars($consultation['activite_nom'] ?? 'Non définie'); ?>
                        </div>
                        <div class="detail-item">
                            <strong>🕒 Horaire :</strong> <?php echo $consultation['heure_debut']; ?> - <?php echo $consultation['heure_fin']; ?>
                        </div>
                        <div class="detail-item">
                            <strong>📍 Lieu :</strong> <?php echo htmlspecialchars($consultation['lieu']); ?>
                        </div>
                        <div class="detail-item">
                            <strong>💰 Prix :</strong> <?php echo number_format($consultation['prix'], 2); ?>€
                        </div>
                        <div class="detail-item">
                            <strong>📞 Téléphone :</strong> <?php echo htmlspecialchars($consultation['client_telephone'] ?? 'Non renseigné'); ?>
                        </div>
                        <div class="detail-item">
                            <strong>🎓 Carte étudiant :</strong> <?php echo htmlspecialchars($consultation['carte_etudiant'] ?? 'Non renseignée'); ?>
                        </div>
                    </div>
                    
                    <!-- Changement de statut -->
                    <div style="margin-top: 1rem;">
                        <form method="POST" style="display: inline-block;">
                            <input type="hidden" name="consultation_id" value="<?php echo $consultation['id']; ?>">
                            <select name="new_status" onchange="this.form.submit()" style="padding: 0.5rem; border-radius: 5px; border: 1px solid #ddd;">
                                <option value="planifie" <?php echo $consultation['statut'] === 'planifie' ? 'selected' : ''; ?>>Planifié</option>
                                <option value="confirme" <?php echo $consultation['statut'] === 'confirme' ? 'selected' : ''; ?>>Confirmé</option>
                                <option value="en_cours" <?php echo $consultation['statut'] === 'en_cours' ? 'selected' : ''; ?>>En cours</option>
                                <option value="termine" <?php echo $consultation['statut'] === 'termine' ? 'selected' : ''; ?>>Terminé</option>
                            </select>
                            <input type="hidden" name="change_status" value="1">
                        </form>
                    </div>
                    
                    <!-- Notes du coach -->
                    <div class="notes-section">
                        <h4>📝 Notes de consultation :</h4>
                        <form method="POST" class="notes-form">
                            <input type="hidden" name="consultation_id" value="<?php echo $consultation['id']; ?>">
                            <textarea name="notes_coach" placeholder="Ajoutez vos notes sur cette consultation..."><?php echo htmlspecialchars($consultation['notes_coach'] ?? ''); ?></textarea>
                            <button type="submit" name="update_notes" class="btn">💾 Sauvegarder</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Consultations terminées récentes -->
            <?php if (!empty($consultations_terminees)): ?>
            <div class="section-card">
                <h2 class="section-title">
                    ✅ Consultations terminées récentes
                </h2>
                
                <?php 
                // Afficher seulement les 5 dernières consultations terminées
                $consultations_terminees_recentes = array_slice($consultations_terminees, -5);
                foreach ($consultations_terminees_recentes as $consultation): 
                ?>
                <div class="consultation-card" style="border-left-color: #6c757d; opacity: 0.8;">
                    <div class="consultation-header">
                        <div class="client-info">
                            <div class="client-name">
                                👤 <?php echo htmlspecialchars($consultation['client_prenom'] . ' ' . $consultation['client_nom']); ?>
                            </div>
                            <span class="status-badge status-<?php echo $consultation['statut']; ?>">
                                ✅ Terminé
                            </span>
                        </div>
                        <div class="communication-buttons">
                            <button class="btn btn-primary btn-sm" onclick="openChatroom('<?php echo $consultation['client_email']; ?>', '<?php echo htmlspecialchars($consultation['client_prenom'] . ' ' . $consultation['client_nom']); ?>', 'email')">
                                📧 Email
                            </button>
                            <button class="btn btn-secondary btn-sm" onclick="openChatroom('<?php echo $consultation['client_email']; ?>', '<?php echo htmlspecialchars($consultation['client_prenom'] . ' ' . $consultation['client_nom']); ?>', 'text')">
                                💬 Texto
                            </button>
                        </div>
                    </div>
                    
                    <div class="consultation-details">
                        <div class="detail-item">
                            <strong>📅 Date :</strong> <?php echo date('d/m/Y', strtotime($consultation['date_rdv'])); ?>
                        </div>
                        <div class="detail-item">
                            <strong>🏃‍♂️ Activité :</strong> <?php echo htmlspecialchars($consultation['activite_nom'] ?? 'Non définie'); ?>
                        </div>
                        <div class="detail-item">
                            <strong>🕒 Horaire :</strong> <?php echo $consultation['heure_debut']; ?> - <?php echo $consultation['heure_fin']; ?>
                        </div>
                        <div class="detail-item">
                            <strong>💰 Prix :</strong> <?php echo number_format($consultation['prix'], 2); ?>€
                        </div>
                    </div>
                    
                    <!-- Notes du coach (lecture seule pour les consultations terminées) -->
                    <?php if (!empty($consultation['notes_coach'])): ?>
                    <div class="notes-section">
                        <h4>📝 Notes de consultation :</h4>
                        <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; font-style: italic;">
                            <?php echo nl2br(htmlspecialchars($consultation['notes_coach'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Message si aucune consultation -->
            <?php if (empty($consultations_courantes) && empty($consultations_a_venir) && empty($consultations_terminees)): ?>
            <div class="section-card">
                <div class="empty-state">
                    <h3>📅 Aucune consultation programmée</h3>
                    <p>Vous n'avez actuellement aucune consultation dans votre agenda.</p>
                    <p>Les nouvelles réservations apparaîtront automatiquement ici.</p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Actions rapides -->
            <div class="section-card">
                <h2 class="section-title">
                    ⚡ Actions Rapides
                </h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <button class="btn" onclick="showAvailabilityManager()">📅 Gérer mes disponibilités</button>
                    <button class="btn btn-secondary" onclick="showProfileManager()">👤 Modifier mon profil</button>
                    <button class="btn btn-primary" onclick="showClientsOverview()">👥 Vue d'ensemble clients</button>
                    <button class="btn btn-warning" onclick="showMonthlyReport()">📊 Rapport mensuel</button>
                </div>
            </div>

        </div>
    </section>

    <!-- Modal Chatroom -->
    <div id="chatroomModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeChatroom()">&times;</span>
            <div id="chatroomContent">
                <!-- Contenu chargé dynamiquement -->
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Sportify - Espace Coach</p>
        </div>
    </footer>

    <script>
        // Fonction pour ouvrir la chatroom
        function openChatroom(clientEmail, clientName, type) {
            const chatroomContent = document.getElementById('chatroomContent');
            
            const typeNames = {
                'email': '📧 Email',
                'text': '💬 Chat Texto',
                'audio': '🎧 Appel Audio',
                'video': '📹 Vidéoconférence'
            };
            
            const typeDescriptions = {
                'email': 'Envoyez un email à votre client',
                'text': 'Chat en temps réel avec votre client',
                'audio': 'Appel audio via votre navigateur',
                'video': 'Vidéoconférence en HD'
            };
            
            chatroomContent.innerHTML = `
                <div class="chatroom">
                    <div class="chatroom-header">
                        <h2>${typeNames[type]} - ${clientName}</h2>
                        <span style="color: #666; font-size: 0.9rem;">${clientEmail}</span>
                    </div>
                    
                    <div style="background: white; border-radius: 10px; padding: 1.5rem; margin: 1rem 0;">
                        <h3 style="margin-bottom: 1rem; color: #28a745;">${typeDescriptions[type]}</h3>
                        
                        ${type === 'email' ? `
                            <form style="display: grid; gap: 1rem;">
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Sujet :</label>
                                    <input type="text" placeholder="Objet de l'email" style="width: 100%; padding: 0.75rem; border: 2px solid #e1e5e9; border-radius: 8px;">
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Message :</label>
                                    <textarea rows="6" placeholder="Votre message..." style="width: 100%; padding: 0.75rem; border: 2px solid #e1e5e9; border-radius: 8px; resize: vertical;"></textarea>
                                </div>
                                <button type="button" class="btn" onclick="sendEmail('${clientEmail}', '${clientName}')">
                                    📧 Envoyer l'email
                                </button>
                            </form>
                        ` : ''}
                        
                        ${type === 'text' ? `
                            <div style="background: #f8f9fa; height: 300px; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; overflow-y: auto; border: 1px solid #dee2e6;">
                                <div style="text-align: center; color: #666; margin-top: 50%; font-style: italic;">
                                    💬 Conversation avec ${clientName}<br>
                                    <small>Les messages apparaîtront ici en temps réel</small>
                                </div>
                            </div>
                            <div style="display: flex; gap: 1rem;">
                                <input type="text" placeholder="Tapez votre message..." style="flex: 1; padding: 0.75rem; border: 2px solid #e1e5e9; border-radius: 8px;" onkeypress="if(event.key==='Enter') sendTextMessage('${clientEmail}', '${clientName}', this.value)">
                                <button class="btn" onclick="sendTextMessage('${clientEmail}', '${clientName}', document.querySelector('input[placeholder=\\'Tapez votre message...\\']').value)">
                                    💬 Envoyer
                                </button>
                            </div>
                        ` : ''}
                        
                        ${type === 'audio' ? `
                            <div style="text-align: center; padding: 2rem;">
                                <div style="font-size: 4rem; margin-bottom: 1rem;">🎧</div>
                                <p style="margin-bottom: 2rem;">Appel audio avec ${clientName}</p>
                                <div style="display: flex; gap: 1rem; justify-content: center;">
                                    <button class="btn btn-success" onclick="startAudioCall('${clientEmail}', '${clientName}')">
                                        📞 Démarrer l'appel
                                    </button>
                                    <button class="btn btn-danger" onclick="endCall()">
                                        📵 Raccrocher
                                    </button>
                                </div>
                                <div style="margin-top: 2rem; padding: 1rem; background: #e3f2fd; border-radius: 8px;">
                                    <small>🔒 Communication sécurisée via WebRTC</small>
                                </div>
                            </div>
                        ` : ''}
                        
                        ${type === 'video' ? `
                            <div style="text-align: center; padding: 2rem;">
                                <div style="background: #000; border-radius: 10px; margin-bottom: 1rem; height: 250px; display: flex; align-items: center; justify-content: center; color: white;">
                                    <div>
                                        <div style="font-size: 3rem; margin-bottom: 1rem;">📹</div>
                                        <p>Vidéoconférence avec ${clientName}</p>
                                        <small>La vidéo apparaîtra ici</small>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 1rem; justify-content: center;">
                                    <button class="btn btn-success" onclick="startVideoCall('${clientEmail}', '${clientName}')">
                                        📹 Démarrer la vidéo
                                    </button>
                                    <button class="btn btn-warning" onclick="toggleMute()">
                                        🔇 Muet
                                    </button>
                                    <button class="btn btn-danger" onclick="endCall()">
                                        📵 Raccrocher
                                    </button>
                                </div>
                                <div style="margin-top: 2rem; padding: 1rem; background: #e3f2fd; border-radius: 8px;">
                                    <small>🔒 Vidéoconférence HD sécurisée</small>
                                </div>
                            </div>
                        ` : ''}
                    </div>
                    
                    <div style="margin-top: 1rem; padding: 1rem; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
                        <h4 style="color: #856404; margin-bottom: 0.5rem;">💡 Système de communication intégré</h4>
                        <p style="color: #856404; font-size: 0.9rem; margin: 0;">
                            Cette interface permet de communiquer directement avec vos clients via différents moyens. 
                            Toutes les conversations sont sauvegardées et sécurisées.
                        </p>
                    </div>
                </div>
            `;
            
            document.getElementById('chatroomModal').style.display = 'block';
        }

        function closeChatroom() {
            document.getElementById('chatroomModal').style.display = 'none';
        }

        // Fonctions de communication (simulation)
        function sendEmail(clientEmail, clientName) {
            alert(`Email envoyé à ${clientName} (${clientEmail}) !\n\nDans une vraie implémentation :\n• L'email serait envoyé via SMTP\n• Une copie serait sauvegardée\n• Le client recevrait une notification`);
        }

        function sendTextMessage(clientEmail, clientName, message) {
            if (message.trim()) {
                alert(`Message envoyé à ${clientName} :\n"${message}"\n\nDans une vraie implémentation :\n• Le message apparaîtrait dans le chat\n• Le client recevrait une notification push\n• L'historique serait sauvegardé`);
                document.querySelector('input[placeholder="Tapez votre message..."]').value = '';
            }
        }

        function startAudioCall(clientEmail, clientName) {
            alert(`Appel audio démarré avec ${clientName} !\n\nDans une vraie implémentation :\n• Connexion WebRTC établie\n• Qualité audio optimisée\n• Enregistrement possible`);
        }

        function startVideoCall(clientEmail, clientName) {
            alert(`Vidéoconférence démarrée avec ${clientName} !\n\nDans une vraie implémentation :\n• Flux vidéo HD activé\n• Chat intégré disponible\n• Partage d'écran possible`);
        }

        function endCall() {
            alert('Appel terminé !\n\nDans une vraie implémentation :\n• Connexion fermée proprement\n• Durée d\'appel enregistrée\n• Rapport de qualité généré');
        }

        function toggleMute() {
            alert('Micro désactivé/activé !\n\nFonctionnalité de contrôle audio en temps réel.');
        }

        // Fonctions pour les actions rapides
        function showAvailabilityManager() {
            alert('Gestionnaire de disponibilités\n\nCette fonctionnalité permettra au coach de :\n\n• Définir ses créneaux disponibles\n• Modifier son planning hebdomadaire\n• Bloquer des créneaux pour congés\n• Synchroniser avec un calendrier externe\n\nÀ implémenter dans la prochaine version.');
        }

        function showProfileManager() {
            alert('Gestionnaire de profil coach\n\nPermet de modifier :\n\n• Informations personnelles\n• Photo de profil\n• Vidéo de présentation\n• Description et spécialités\n• Tarifs et services\n\nÀ implémenter prochainement.');
        }

        function showClientsOverview() {
            alert('Vue d\'ensemble des clients\n\nAffichage de :\n\n• Liste de tous vos clients\n• Historique des consultations\n• Notes et évaluations\n• Statistiques de progression\n• Moyens de contact rapide\n\nÀ développer dans la v2.0');
        }

        function showMonthlyReport() {
            alert('Rapport mensuel du coach\n\nInclut :\n\n• Nombre de consultations\n• Revenus générés\n• Évaluations clients\n• Temps de travail\n• Objectifs atteints\n\nGénération automatique à implémenter.');
        }

        // Fermer les modals en cliquant à l'extérieur
        window.onclick = function(event) {
            const chatroomModal = document.getElementById('chatroomModal');
            
            if (event.target == chatroomModal) {
                chatroomModal.style.display = "none";
            }
        }

        // Animation des cartes au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('animate-fade-in');
                }, index * 100);
            });
            
            const consultationCards = document.querySelectorAll('.consultation-card');
            consultationCards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('animate-fade-in');
                }, (index + 4) * 100);
            });
        });

        // Auto-refresh des consultations (optionnel)
        function refreshConsultations() {
            console.log('Refresh des consultations...');
            // Ici on pourrait recharger les données via AJAX
        }

        // Actualiser toutes les 2 minutes
        setInterval(refreshConsultations, 120000);

        // Message de bienvenue pour le coach
        console.log(`
        🏋️‍♂️ ESPACE COACH SPORTIFY
        ==========================
        
        Connecté en tant que : ${document.querySelector('.user-badge').textContent}
        
        Fonctionnalités disponibles :
        ✅ Vue d'ensemble des consultations
        ✅ Communication multi-canal (email, texto, audio, vidéo)
        ✅ Gestion des notes de consultation
        ✅ Modification des statuts
        ✅ Statistiques personnelles
        
        En développement :
        🔄 Gestion des disponibilités
        🔄 Profil coach avancé
        🔄 Chat temps réel
        🔄 Appels audio/vidéo WebRTC
        `);
    </script>
</body>
</html>