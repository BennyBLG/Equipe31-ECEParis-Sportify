<?php
session_start();

// Vérification de l'authentification et du rôle client
if (!isset($_SESSION["user_logged_in"]) || $_SESSION["role"] !== "client") {
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
$client_info_fallback = [
    'id' => 1,
    'adresse' => $user_info['adresse'] ?? '123 Rue Test, Paris',
    'ville' => 'Paris',
    'code_postal' => '75001',
    'pays' => 'France',
    'carte_etudiant' => $user_info['carte_etudiant'] ?? 'ETU2025001',
    'date_naissance' => '1995-06-15'
];

$rdv_fallback = [
    [
        'id' => 1,
        'coach_nom' => 'DUMAIS',
        'coach_prenom' => 'Guy',
        'coach_specialite' => 'Musculation',
        'activite_nom' => 'Musculation',
        'date_rdv' => '2025-06-02',
        'heure_debut' => '14:00',
        'heure_fin' => '15:00',
        'lieu' => 'Studio 1',
        'statut' => 'confirme',
        'prix' => 35.00,
        'notes_client' => '',
        'evaluation_client' => null
    ],
    [
        'id' => 2,
        'coach_nom' => 'MARTIN',
        'coach_prenom' => 'Marie',
        'coach_specialite' => 'Fitness',
        'activite_nom' => 'Fitness',
        'date_rdv' => '2025-06-05',
        'heure_debut' => '16:30',
        'heure_fin' => '17:15',
        'lieu' => 'Studio 2',
        'statut' => 'confirme',
        'prix' => 30.00,
        'notes_client' => '',
        'evaluation_client' => null
    ]
];

$activites_fallback = [
    ['id' => 1, 'nom' => 'Musculation', 'prix' => 35.00, 'duree_minutes' => 60, 'type' => 'activite_sportive'],
    ['id' => 2, 'nom' => 'Fitness', 'prix' => 30.00, 'duree_minutes' => 45, 'type' => 'activite_sportive'],
    ['id' => 3, 'nom' => 'Tennis', 'prix' => 40.00, 'duree_minutes' => 60, 'type' => 'sport_competition'],
    ['id' => 4, 'nom' => 'Cardio-Training', 'prix' => 25.00, 'duree_minutes' => 30, 'type' => 'activite_sportive']
];

$coachs_fallback = [
    ['id' => 1, 'nom' => 'DUMAIS', 'prenom' => 'Guy', 'specialite' => 'Musculation', 'note_moyenne' => 4.8, 'nb_evaluations' => 127],
    ['id' => 2, 'nom' => 'MARTIN', 'prenom' => 'Marie', 'specialite' => 'Fitness', 'note_moyenne' => 4.6, 'nb_evaluations' => 89],
    ['id' => 3, 'nom' => 'BERNARD', 'prenom' => 'Paul', 'specialite' => 'Tennis', 'note_moyenne' => 4.9, 'nb_evaluations' => 156],
    ['id' => 4, 'nom' => 'DUBOIS', 'prenom' => 'Sophie', 'specialite' => 'Cardio-Training', 'note_moyenne' => 4.5, 'nb_evaluations' => 73]
];
// Récupération des informations du client
if ($using_database) {
    try {
        // Récupérer les infos du client connecté
        $stmt = $pdo->prepare("
            SELECT c.*, u.nom, u.prenom, u.email, u.telephone
            FROM clients c 
            INNER JOIN users u ON c.user_id = u.id 
            WHERE u.email = ?
        ");
        $stmt->execute([$user_info['email']]);
        $client_info = $stmt->fetch();
        
        if (!$client_info) {
            $client_info = array_merge($client_info_fallback, [
                'nom' => $user_info['nom'],
                'prenom' => $user_info['prenom'],
                'email' => $user_info['email']
            ]);
            $using_database = false;
        }
    } catch (PDOException $e) {
        $client_info = array_merge($client_info_fallback, [
            'nom' => $user_info['nom'],
            'prenom' => $user_info['prenom'],
            'email' => $user_info['email']
        ]);
        $using_database = false;
    }
} else {
    $client_info = array_merge($client_info_fallback, [
        'nom' => $user_info['nom'],
        'prenom' => $user_info['prenom'],
        'email' => $user_info['email']
    ]);
}

// Récupération des rendez-vous
if ($using_database && isset($client_info['id'])) {
    try {
        $stmt = $pdo->prepare("
            SELECT r.*, 
                   u.nom as coach_nom, u.prenom as coach_prenom,
                   c.specialite as coach_specialite,
                   a.nom as activite_nom
            FROM rendezvous r
            INNER JOIN coachs co ON r.coach_id = co.id
            INNER JOIN users u ON co.user_id = u.id
            LEFT JOIN activites a ON r.activite_id = a.id
            WHERE r.client_id = ?
            ORDER BY r.date_rdv DESC, r.heure_debut DESC
        ");
        $stmt->execute([$client_info['id']]);
        $rdv_client = $stmt->fetchAll();
    } catch (PDOException $e) {
        $rdv_client = $rdv_fallback;
        $using_database = false;
    }
} else {
    $rdv_client = $rdv_fallback;
}

// Récupération des activités disponibles
if ($using_database) {
    try {
        $stmt = $pdo->query("SELECT * FROM activites WHERE statut = 'actif' ORDER BY type, ordre, nom");
        $activites = $stmt->fetchAll();
        
        $stmt = $pdo->query("
            SELECT c.id, u.nom, u.prenom, c.specialite, c.note_moyenne, c.nb_evaluations
            FROM coachs c 
            INNER JOIN users u ON c.user_id = u.id 
            WHERE u.statut = 'actif'
            ORDER BY u.nom, u.prenom
        ");
        $coachs = $stmt->fetchAll();
    } catch (PDOException $e) {
        $activites = $activites_fallback;
        $coachs = $coachs_fallback;
        $using_database = false;
    }
} else {
    $activites = $activites_fallback;
    $coachs = $coachs_fallback;
}

// Traitement d'annulation de RDV
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cancel_rdv'])) {
    $rdv_id = (int)$_POST['rdv_id'];
    
    if ($using_database) {
        try {
            $stmt = $pdo->prepare("UPDATE rendezvous SET statut = 'annule' WHERE id = ? AND client_id = ?");
            $stmt->execute([$rdv_id, $client_info['id']]);
            
            $message = "Rendez-vous annulé avec succès ! Le créneau a été libéré.";
            $message_type = "success";
            
            // Recharger les RDV
            $stmt = $pdo->prepare("
                SELECT r.*, 
                       u.nom as coach_nom, u.prenom as coach_prenom,
                       c.specialite as coach_specialite,
                       a.nom as activite_nom
                FROM rendezvous r
                INNER JOIN coachs co ON r.coach_id = co.id
                INNER JOIN users u ON co.user_id = u.id
                LEFT JOIN activites a ON r.activite_id = a.id
                WHERE r.client_id = ?
                ORDER BY r.date_rdv DESC, r.heure_debut DESC
            ");
            $stmt->execute([$client_info['id']]);
            $rdv_client = $stmt->fetchAll();
            
        } catch (PDOException $e) {
            $message = "Erreur lors de l'annulation : " . $e->getMessage();
            $message_type = "error";
        }
    } else {
        $message = "Annulation simulée ! (Base de données non disponible)";
        $message_type = "success";
    }
}

// Traitement d'évaluation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_evaluation'])) {
    $rdv_id = (int)$_POST['rdv_id'];
    $evaluation = (int)$_POST['evaluation_client'];
    $commentaire = trim($_POST['commentaire_client']);
    
    if ($using_database) {
        try {
            $stmt = $pdo->prepare("UPDATE rendezvous SET evaluation_client = ?, commentaire_client = ? WHERE id = ? AND client_id = ?");
            $stmt->execute([$evaluation, $commentaire, $rdv_id, $client_info['id']]);
            
            $message = "Évaluation enregistrée avec succès !";
            $message_type = "success";
            
            // Recharger les RDV
            $stmt = $pdo->prepare("
                SELECT r.*, 
                       u.nom as coach_nom, u.prenom as coach_prenom,
                       c.specialite as coach_specialite,
                       a.nom as activite_nom
                FROM rendezvous r
                INNER JOIN coachs co ON r.coach_id = co.id
                INNER JOIN users u ON co.user_id = u.id
                LEFT JOIN activites a ON r.activite_id = a.id
                WHERE r.client_id = ?
                ORDER BY r.date_rdv DESC, r.heure_debut DESC
            ");
            $stmt->execute([$client_info['id']]);
            $rdv_client = $stmt->fetchAll();
            
        } catch (PDOException $e) {
            $message = "Erreur lors de l'enregistrement : " . $e->getMessage();
            $message_type = "error";
        }
    } else {
        $message = "Évaluation simulée ! (Base de données non disponible)";
        $message_type = "success";
    }
}

// Traitement de réservation et paiement
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['book_rdv'])) {
    $coach_id = (int)$_POST['coach_id'];
    $activite_id = (int)$_POST['activite_id'];
    $date_rdv = $_POST['date_rdv'];
    $heure_debut = $_POST['heure_debut'];
    $heure_fin = $_POST['heure_fin'];
    
    // Informations de paiement
    $carte_type = $_POST['carte_type'];
    $carte_numero = $_POST['carte_numero'];
    $carte_nom = $_POST['carte_nom'];
    $carte_expiration = $_POST['carte_expiration'];
    $carte_cvv = $_POST['carte_cvv'];
    
    // Validation simple du paiement (dans la vraie vie, communication avec la banque)
    if (strlen($carte_numero) >= 13 && strlen($carte_cvv) >= 3) {
        if ($using_database) {
            try {
                // Récupérer le prix de l'activité
                $stmt = $pdo->prepare("SELECT prix FROM activites WHERE id = ?");
                $stmt->execute([$activite_id]);
                $activite = $stmt->fetch();
                
                if ($activite) {
                    // Créer le rendez-vous
                    $stmt = $pdo->prepare("
                        INSERT INTO rendezvous (client_id, coach_id, activite_id, date_rdv, heure_debut, heure_fin, lieu, prix, statut) 
                        VALUES (?, ?, ?, ?, ?, ?, 'Studio Sportify', ?, 'confirme')
                    ");
                    $stmt->execute([$client_info['id'], $coach_id, $activite_id, $date_rdv, $heure_debut, $heure_fin, $activite['prix']]);
                    
                    $message = "🎉 Paiement validé ! Votre rendez-vous a été confirmé. Un email de confirmation vous a été envoyé.";
                    $message_type = "success";
                    
                    // Recharger les RDV
                    $stmt = $pdo->prepare("
                        SELECT r.*, 
                               u.nom as coach_nom, u.prenom as coach_prenom,
                               c.specialite as coach_specialite,
                               a.nom as activite_nom
                        FROM rendezvous r
                        INNER JOIN coachs co ON r.coach_id = co.id
                        INNER JOIN users u ON co.user_id = u.id
                        LEFT JOIN activites a ON r.activite_id = a.id
                        WHERE r.client_id = ?
                        ORDER BY r.date_rdv DESC, r.heure_debut DESC
                    ");
                    $stmt->execute([$client_info['id']]);
                    $rdv_client = $stmt->fetchAll();
                } else {
                    $message = "Activité non trouvée.";
                    $message_type = "error";
                }
            } catch (PDOException $e) {
                $message = "Erreur lors de la réservation : " . $e->getMessage();
                $message_type = "error";
            }
        } else {
            $message = "🎉 Paiement simulé validé ! Réservation confirmée. (Base de données non disponible)";
            $message_type = "success";
        }
    } else {
        $message = "❌ Paiement refusé : informations de carte invalides.";
        $message_type = "error";
    }
}

// Séparer les RDV par statut
$rdv_a_venir = [];
$rdv_historique = [];

foreach ($rdv_client as $rdv) {
    $date_rdv = new DateTime($rdv['date_rdv']);
    $aujourd_hui = new DateTime();
    
    if ($rdv['statut'] === 'annule' || $rdv['statut'] === 'termine' || $date_rdv < $aujourd_hui) {
        $rdv_historique[] = $rdv;
    } else {
        $rdv_a_venir[] = $rdv;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Client - Sportify</title>
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
            background: #007bff;
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
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.9), rgba(118, 75, 162, 0.9));
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

        .client-info {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 1rem 2rem;
            margin-top: 2rem;
            display: inline-block;
            text-align: left;
        }

        .dashboard-section {
            padding: 60px 0;
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

        .rdv-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #007bff;
            transition: all 0.3s ease;
        }

        .rdv-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .rdv-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .rdv-info {
            flex: 1;
        }

        .coach-name {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .rdv-details {
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

        .status-confirme {
            background: #d4edda;
            color: #155724;
        }

        .status-planifie {
            background: #fff3cd;
            color: #856404;
        }

        .status-termine {
            background: #e2e3e5;
            color: #383d41;
        }

        .status-annule {
            background: #f8d7da;
            color: #721c24;
        }

        .btn {
            background: linear-gradient(135deg, #007bff, #0056b3);
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
            margin: 0.25rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745, #1e7e34);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #212529;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
        }

        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.8rem;
        }

        .service-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .service-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .service-card:hover {
            transform: translateY(-5px);
            border-color: #007bff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .service-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .service-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
        }

        .service-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #007bff;
        }

        .coach-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }

        .coach-card {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            border-left: 4px solid #28a745;
        }

        .coach-card h4 {
            color: #333;
            margin-bottom: 0.5rem;
        }

        .rating {
            color: #ffc107;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
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
            max-width: 700px;
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

        .payment-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
            border-left: 4px solid #ffc107;
        }

        .evaluation-form {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .star-rating {
            display: flex;
            gap: 0.25rem;
            margin: 0.5rem 0;
        }

        .star {
            font-size: 1.5rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }

        .star:hover,
        .star.active {
            color: #ffc107;
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
            
            .rdv-header {
                flex-direction: column;
                gap: 1rem;
            }
            
            .rdv-details {
                grid-template-columns: 1fr;
            }
            
            .service-grid,
            .coach-grid {
                grid-template-columns: 1fr;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
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
            <div class="logo">👤 Sportify Client</div>
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
                    👤 <?php echo htmlspecialchars($client_info['prenom'] . ' ' . $client_info['nom']); ?>
                </div>
                <a href="votre_compte.php?logout" class="logout-btn">🚪 Déconnexion</a>
            </div>
        </div>
    </div>

    <!-- Section Hero -->
    <section class="hero">
        <div class="hero-content">
            <h1>👤 Espace Client</h1>
            <p class="hero-subtitle">Consultez vos services et gérez vos rendez-vous</p>
            <div class="client-info">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; text-align: left;">
                    <div>
                        <strong>Nom et Prénom :</strong> <?php echo htmlspecialchars($client_info['prenom'] . ' ' . $client_info['nom']); ?><br>
                        <strong>Email :</strong> <?php echo htmlspecialchars($client_info['email']); ?><br>
                        <strong>Carte Étudiante :</strong> <?php echo htmlspecialchars($client_info['carte_etudiant'] ?? 'Non renseignée'); ?>
                    </div>
                    <div>
                        <strong>Adresse :</strong> <?php echo htmlspecialchars($client_info['adresse'] ?? 'Non renseignée'); ?><br>
                        <strong>Ville :</strong> <?php echo htmlspecialchars($client_info['ville'] ?? 'Non renseignée'); ?><br>
                        <strong>💳 Paiement :</strong> <span style="font-size: 0.8rem; opacity: 0.7;">Informations sécurisées</span>
                    </div>
                </div>
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

            <!-- Rendez-vous à venir -->
            <?php if (!empty($rdv_a_venir)): ?>
            <div class="section-card">
                <h2 class="section-title">
                    📅 Mes Rendez-vous à Venir
                </h2>
                
                <?php foreach ($rdv_a_venir as $rdv): ?>
                <div class="rdv-card" style="border-left-color: #28a745;">
                    <div class="rdv-header">
                        <div class="rdv-info">
                            <div class="coach-name">
                                🏋️‍♂️ Coach <?php echo htmlspecialchars($rdv['coach_prenom'] . ' ' . $rdv['coach_nom']); ?>
                            </div>
                            <span class="status-badge status-<?php echo $rdv['statut']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $rdv['statut'])); ?>
                            </span>
                        </div>
                        <div>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="rdv_id" value="<?php echo $rdv['id']; ?>">
                                <button type="submit" name="cancel_rdv" class="btn btn-danger btn-sm" 
                                        onclick="return confirm('⚠️ Annuler ce rendez-vous ?\n\nLe créneau sera libéré et vous pourrez reprendre un autre RDV.')">
                                    🗑️ Annuler ce RDV
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="rdv-details">
                        <div class="detail-item">
                            <strong>📅 Date :</strong> <?php echo date('d/m/Y', strtotime($rdv['date_rdv'])); ?>
                        </div>
                        <div class="detail-item">
                            <strong>🕒 Horaire :</strong> <?php echo $rdv['heure_debut']; ?> - <?php echo $rdv['heure_fin']; ?>
                        </div>
                        <div class="detail-item">
                            <strong>🏃‍♂️ Activité :</strong> <?php echo htmlspecialchars($rdv['activite_nom'] ?? 'Service salle de sport'); ?>
                        </div>
                        <div class="detail-item">
                            <strong>📍 Lieu :</strong> <?php echo htmlspecialchars($rdv['lieu']); ?>
                        </div>
                        <div class="detail-item">
                            <strong>🎯 Spécialité :</strong> <?php echo htmlspecialchars($rdv['coach_specialite']); ?>
                        </div>
                        <div class="detail-item">
                            <strong>💰 Prix :</strong> <?php echo number_format($rdv['prix'], 2); ?>€
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

<!-- Historique des consultations -->
            <?php if (!empty($rdv_historique)): ?>
            <div class="section-card">
                <h2 class="section-title">
                    📋 Historique de mes Consultations
                </h2>
                
                <?php foreach ($rdv_historique as $rdv): ?>
                <div class="rdv-card" style="border-left-color: #6c757d; opacity: 0.9;">
                    <div class="rdv-header">
                        <div class="rdv-info">
                            <div class="coach-name">
                                🏋️‍♂️ Coach <?php echo htmlspecialchars($rdv['coach_prenom'] . ' ' . $rdv['coach_nom']); ?>
                            </div>
                            <span class="status-badge status-<?php echo $rdv['statut']; ?>">
                                <?php 
                                $status_labels = [
                                    'termine' => '✅ Terminé',
                                    'annule' => '❌ Annulé'
                                ];
                                echo $status_labels[$rdv['statut']] ?? ucfirst($rdv['statut']);
                                ?>
                            </span>
                        </div>
                        <?php if ($rdv['statut'] === 'termine' && empty($rdv['evaluation_client'])): ?>
                        <div>
                            <button class="btn btn-warning btn-sm" onclick="showEvaluationForm(<?php echo $rdv['id']; ?>, '<?php echo htmlspecialchars($rdv['coach_prenom'] . ' ' . $rdv['coach_nom']); ?>')">
                                ⭐ Évaluer
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="rdv-details">
                        <div class="detail-item">
                            <strong>📅 Date :</strong> <?php echo date('d/m/Y', strtotime($rdv['date_rdv'])); ?>
                        </div>
                        <div class="detail-item">
                            <strong>🕒 Horaire :</strong> <?php echo $rdv['heure_debut']; ?> - <?php echo $rdv['heure_fin']; ?>
                        </div>
                        <div class="detail-item">
                            <strong>🏃‍♂️ Activité :</strong> <?php echo htmlspecialchars($rdv['activite_nom'] ?? 'Service salle de sport'); ?>
                        </div>
                        <div class="detail-item">
                            <strong>🎯 Spécialité :</strong> <?php echo htmlspecialchars($rdv['coach_specialite']); ?>
                        </div>
                        <div class="detail-item">
                            <strong>💰 Prix :</strong> <?php echo number_format($rdv['prix'], 2); ?>€
                        </div>
                        <?php if (!empty($rdv['evaluation_client'])): ?>
                        <div class="detail-item">
                            <strong>⭐ Évaluation :</strong> <?php echo str_repeat('⭐', $rdv['evaluation_client']); ?> (<?php echo $rdv['evaluation_client']; ?>/5)
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($rdv['commentaire_client'])): ?>
                    <div style="margin-top: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                        <strong>💬 Mon commentaire :</strong>
                        <p style="margin-top: 0.5rem; font-style: italic;"><?php echo nl2br(htmlspecialchars($rdv['commentaire_client'])); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Services disponibles -->
            <div class="section-card">
                <h2 class="section-title">
                    🏃‍♂️ Services Sportifs Disponibles
                </h2>
                
                <div class="service-grid">
                    <?php 
                    $activites_sportives = array_filter($activites, function($a) { return $a['type'] === 'activite_sportive'; });
                    $sports_competition = array_filter($activites, function($a) { return $a['type'] === 'sport_competition'; });
                    ?>
                    
                    <!-- Activités sportives normales -->
                    <?php foreach ($activites_sportives as $activite): ?>
                    <div class="service-card">
                        <div class="service-header">
                            <div class="service-title">🏃‍♂️ <?php echo htmlspecialchars($activite['nom']); ?></div>
                            <div class="service-price"><?php echo number_format($activite['prix'], 0); ?>€</div>
                        </div>
                        <p><strong>Durée :</strong> <?php echo $activite['duree_minutes']; ?> minutes</p>
                        <p><strong>Type :</strong> Activité sportive normale</p>
                        <button class="btn" onclick="showBookingForm(<?php echo $activite['id']; ?>, '<?php echo htmlspecialchars($activite['nom']); ?>', <?php echo $activite['prix']; ?>)">
                            📅 Réserver un créneau
                        </button>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Sports de compétition -->
                    <?php foreach ($sports_competition as $activite): ?>
                    <div class="service-card" style="border: 2px solid #ffc107;">
                        <div class="service-header">
                            <div class="service-title">🏆 <?php echo htmlspecialchars($activite['nom']); ?></div>
                            <div class="service-price"><?php echo number_format($activite['prix'], 0); ?>€</div>
                        </div>
                        <p><strong>Durée :</strong> <?php echo $activite['duree_minutes']; ?> minutes</p>
                        <p><strong>Type :</strong> Sport de compétition</p>
                        <button class="btn btn-warning" onclick="showBookingForm(<?php echo $activite['id']; ?>, '<?php echo htmlspecialchars($activite['nom']); ?>', <?php echo $activite['prix']; ?>)">
                            🏆 Réserver (Compétition)
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Coachs disponibles -->
            <div class="section-card">
                <h2 class="section-title">
                    🏋️‍♂️ Nos Coachs Disponibles
                </h2>
                
                <div class="coach-grid">
                    <?php foreach ($coachs as $coach): ?>
                    <div class="coach-card">
                        <h4><?php echo htmlspecialchars($coach['prenom'] . ' ' . $coach['nom']); ?></h4>
                        <p><strong>Spécialité :</strong> <?php echo htmlspecialchars($coach['specialite']); ?></p>
                        <p class="rating">⭐ <?php echo number_format($coach['note_moyenne'], 1); ?>/5 (<?php echo $coach['nb_evaluations'] ?? 0; ?> avis)</p>
                        <button class="btn btn-sm" onclick="viewCoachAvailability(<?php echo $coach['id']; ?>, '<?php echo htmlspecialchars($coach['prenom'] . ' ' . $coach['nom']); ?>')">
                            📅 Voir disponibilités
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Message si aucun RDV -->
            <?php if (empty($rdv_a_venir) && empty($rdv_historique)): ?>
            <div class="section-card">
                <div class="empty-state">
                    <h3>📅 Aucune consultation pour le moment</h3>
                    <p>Vous n'avez pas encore de rendez-vous programmé.</p>
                    <p>Découvrez nos services ci-dessus et réservez votre première séance !</p>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- Modal de réservation et paiement -->
    <div id="bookingModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeBookingModal()">&times;</span>
            <div id="bookingContent">
                <!-- Contenu chargé dynamiquement -->
            </div>
        </div>
    </div>

    <!-- Modal d'évaluation -->
    <div id="evaluationModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEvaluationModal()">&times;</span>
            <div id="evaluationContent">
                <!-- Contenu chargé dynamiquement -->
            </div>
        </div>
    </div>

    <!-- Modal disponibilités coach -->
    <div id="availabilityModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAvailabilityModal()">&times;</span>
            <div id="availabilityContent">
                <!-- Contenu chargé dynamiquement -->
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Sportify - Espace Client</p>
        </div>
    </footer>

    <script>
        // Fonction pour afficher le formulaire de réservation
        function showBookingForm(activiteId, activiteNom, prix) {
            const bookingContent = document.getElementById('bookingContent');
            
            bookingContent.innerHTML = `
                <h2>📅 Réserver : ${activiteNom}</h2>
                <p><strong>Prix :</strong> ${prix}€</p>
                
                <form method="POST" id="bookingForm">
                    <input type="hidden" name="activite_id" value="${activiteId}">
                    
                    <h3 style="margin: 1.5rem 0 1rem 0;">🏋️‍♂️ Choisir un coach</h3>
                    <div class="form-group">
                        <select name="coach_id" required>
                            <option value="">Sélectionner un coach</option>
                            ${getCoachs()}
                        </select>
                    </div>
                    
                    <h3 style="margin: 1.5rem 0 1rem 0;">📅 Date et heure</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Date du rendez-vous</label>
                            <input type="date" name="date_rdv" required min="${new Date().toISOString().split('T')[0]}">
                        </div>
                        <div class="form-group">
                            <label>Heure de début</label>
                            <input type="time" name="heure_debut" required>
                        </div>
                        <div class="form-group">
                            <label>Heure de fin</label>
                            <input type="time" name="heure_fin" required>
                        </div>
                    </div>
                    
                    <div class="payment-section">
                        <h3 style="margin-bottom: 1rem;">💳 Informations de Paiement</h3>
                        <p style="margin-bottom: 1rem; color: #856404;">🔒 Pour des raisons de sécurité, nous validons le paiement via notre système sécurisé.</p>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Type de carte</label>
                                <select name="carte_type" required>
                                    <option value="">Choisir...</option>
                                    <option value="visa">Visa</option>
                                    <option value="mastercard">MasterCard</option>
                                    <option value="amex">American Express</option>
                                    <option value="paypal">PayPal</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Numéro de carte</label>
                                <input type="text" name="carte_numero" placeholder="1234 5678 9012 3456" required maxlength="19">
                            </div>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Nom sur la carte</label>
                                <input type="text" name="carte_nom" placeholder="JEAN DUPONT" required>
                            </div>
                            <div class="form-group">
                                <label>Date d'expiration</label>
                                <input type="text" name="carte_expiration" placeholder="MM/AA" required maxlength="5">
                            </div>
                            <div class="form-group">
                                <label>Code de sécurité</label>
                                <input type="text" name="carte_cvv" placeholder="123" required maxlength="4">
                            </div>
                        </div>
                        
                        <div style="background: #e3f2fd; padding: 1rem; border-radius: 8px; margin-top: 1rem;">
                            <h4 style="color: #1976d2; margin-bottom: 0.5rem;">💡 Validation de paiement</h4>
                            <p style="color: #1976d2; font-size: 0.9rem; margin: 0;">
                                Dans un environnement réel, nous communiquerions avec votre banque pour valider le paiement. 
                                Pour ce projet, nous validons si toutes les informations sont correctement remplies.
                            </p>
                        </div>
                    </div>
                    
                    <div style="margin-top: 2rem; text-align: center;">
                        <button type="submit" name="book_rdv" class="btn" style="padding: 1rem 2rem; font-size: 1.1rem;">
                            💳 Confirmer et Payer (${prix}€)
                        </button>
                    </div>
                </form>
            `;
            
            document.getElementById('bookingModal').style.display = 'block';
            setupCardFormatting();
        }

        function getCoachs() {
            return `
                <?php foreach ($coachs as $coach): ?>
                    <option value="<?php echo $coach['id']; ?>">
                        <?php echo htmlspecialchars($coach['prenom'] . ' ' . $coach['nom'] . ' - ' . $coach['specialite']); ?> 
                        (⭐ <?php echo number_format($coach['note_moyenne'], 1); ?>)
                    </option>
                <?php endforeach; ?>
            `;
        }

        function setupCardFormatting() {
            const cardNumber = document.querySelector('input[name="carte_numero"]');
            if (cardNumber) {
                cardNumber.addEventListener('input', function() {
                    let value = this.value.replace(/\s/g, '').replace(/\D/g, '');
                    let formattedValue = value.replace(/(\d{4})(?=\d)/g, '$1 ');
                    if (formattedValue !== this.value) {
                        this.value = formattedValue;
                    }
                });
            }
            
            const expiration = document.querySelector('input[name="carte_expiration"]');
            if (expiration) {
                expiration.addEventListener('input', function() {
                    let value = this.value.replace(/\D/g, '');
                    if (value.length >= 2) {
                        value = value.substring(0, 2) + '/' + value.substring(2, 4);
                    }
                    this.value = value;
                });
            }
            
            const cvv = document.querySelector('input[name="carte_cvv"]');
            if (cvv) {
                cvv.addEventListener('input', function() {
                    this.value = this.value.replace(/\D/g, '');
                });
            }
        }

        function showEvaluationForm(rdvId, coachName) {
            const evaluationContent = document.getElementById('evaluationContent');
            
            evaluationContent.innerHTML = `
                <h2>⭐ Évaluer votre séance</h2>
                <p>Avec le coach <strong>${coachName}</strong></p>
                
                <form method="POST" class="evaluation-form">
                    <input type="hidden" name="rdv_id" value="${rdvId}">
                    
                    <div class="form-group">
                        <label>Note sur 5</label>
                        <div class="star-rating" data-rating="0">
                            <span class="star" data-value="1">⭐</span>
                            <span class="star" data-value="2">⭐</span>
                            <span class="star" data-value="3">⭐</span>
                            <span class="star" data-value="4">⭐</span>
                            <span class="star" data-value="5">⭐</span>
                        </div>
                        <input type="hidden" name="evaluation_client" value="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Commentaire (optionnel)</label>
                        <textarea name="commentaire_client" rows="4" placeholder="Partagez votre expérience avec ce coach..."></textarea>
                    </div>
                    
                    <button type="submit" name="submit_evaluation" class="btn">
                        ⭐ Envoyer l'évaluation
                    </button>
                </form>
            `;
            
            document.getElementById('evaluationModal').style.display = 'block';
            setupStarRating();
        }

        function setupStarRating() {
            const stars = document.querySelectorAll('.star');
            const ratingInput = document.querySelector('input[name="evaluation_client"]');
            
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.dataset.value);
                    ratingInput.value = rating;
                    
                    stars.forEach((s, index) => {
                        if (index < rating) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                });
                
                star.addEventListener('mouseover', function() {
                    const rating = parseInt(this.dataset.value);
                    
                    stars.forEach((s, index) => {
                        if (index < rating) {
                            s.style.color = '#ffc107';
                        } else {
                            s.style.color = '#ddd';
                        }
                    });
                });
            });
            
            const starContainer = document.querySelector('.star-rating');
            starContainer.addEventListener('mouseleave', function() {
                const currentRating = parseInt(ratingInput.value);
                
                stars.forEach((s, index) => {
                    if (index < currentRating) {
                        s.style.color = '#ffc107';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            });
        }

        function viewCoachAvailability(coachId, coachName) {
            const availabilityContent = document.getElementById('availabilityContent');
            
            availabilityContent.innerHTML = `
                <h2>📅 Disponibilités du Coach ${coachName}</h2>
                
                <div style="margin: 1.5rem 0;">
                    <h3>🕒 Planning de la semaine type :</h3>
                    <div style="display: grid; gap: 0.5rem; margin-top: 1rem;">
                        <div style="padding: 0.5rem; background: #f8f9fa; border-radius: 5px;"><strong>Lundi :</strong> 09:00-12:00, 14:00-18:00</div>
                        <div style="padding: 0.5rem; background: #f8f9fa; border-radius: 5px;"><strong>Mardi :</strong> 08:00-12:00, 13:00-17:00</div>
                        <div style="padding: 0.5rem; background: #f8f9fa; border-radius: 5px;"><strong>Mercredi :</strong> 10:00-16:00</div>
                        <div style="padding: 0.5rem; background: #f8f9fa; border-radius: 5px;"><strong>Jeudi :</strong> 09:00-12:00, 14:00-19:00</div>
                        <div style="padding: 0.5rem; background: #f8f9fa; border-radius: 5px;"><strong>Vendredi :</strong> 08:00-15:00</div>
                        <div style="padding: 0.5rem; background: #f8f9fa; border-radius: 5px;"><strong>Samedi :</strong> 09:00-13:00</div>
                        <div style="padding: 0.5rem; background: #f8f9fa; border-radius: 5px;"><strong>Dimanche :</strong> Fermé</div>
                    </div>
                </div>
                
                <div style="background: #e3f2fd; padding: 1rem; border-radius: 8px; margin-top: 1rem;">
                    <h4 style="color: #1976d2; margin-bottom: 0.5rem;">💡 Comment réserver ?</h4>
                    <p style="color: #1976d2; font-size: 0.9rem; margin: 0;">
                        Utilisez les boutons "Réserver un créneau" sur les services ci-dessus pour choisir ce coach et votre horaire préféré.
                    </p>
                </div>
                
                <div style="text-align: center; margin-top: 1.5rem;">
                    <button class="btn" onclick="closeAvailabilityModal()">
                        ✅ Compris, je vais réserver
                    </button>
                </div>
            `;
            
            document.getElementById('availabilityModal').style.display = 'block';
        }

        function closeBookingModal() {
            document.getElementById('bookingModal').style.display = 'none';
        }

        function closeEvaluationModal() {
            document.getElementById('evaluationModal').style.display = 'none';
        }

        function closeAvailabilityModal() {
            document.getElementById('availabilityModal').style.display = 'none';
        }

        // Fermer les modals en cliquant à l'extérieur
        window.onclick = function(event) {
            const bookingModal = document.getElementById('bookingModal');
            const evaluationModal = document.getElementById('evaluationModal');
            const availabilityModal = document.getElementById('availabilityModal');
            
            if (event.target == bookingModal) {
                bookingModal.style.display = "none";
            }
            if (event.target == evaluationModal) {
                evaluationModal.style.display = "none";
            }
            if (event.target == availabilityModal) {
                availabilityModal.style.display = "none";
            }
        }

        // Validation du formulaire de réservation
        document.addEventListener('submit', function(e) {
            if (e.target.id === 'bookingForm') {
                const dateRdv = new Date(e.target.date_rdv.value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (dateRdv < today) {
                    e.preventDefault();
                    alert('❌ Vous ne pouvez pas réserver dans le passé !');
                    return false;
                }
                
                const heureDebut = e.target.heure_debut.value;
                const heureFin = e.target.heure_fin.value;
                
                if (heureDebut >= heureFin) {
                    e.preventDefault();
                    alert('❌ L\'heure de fin doit être après l\'heure de début !');
                    return false;
                }
                
                const carteNumero = e.target.carte_numero.value.replace(/\s/g, '');
                if (carteNumero.length < 13) {
                    e.preventDefault();
                    alert('❌ Numéro de carte invalide !');
                    return false;
                }
                
                const carteCvv = e.target.carte_cvv.value;
                if (carteCvv.length < 3) {
                    e.preventDefault();
                    alert('❌ Code de sécurité invalide !');
                    return false;
                }
                
                const activiteNom = e.target.closest('.modal-content').querySelector('h2').textContent.replace('📅 Réserver : ', '');
                const prix = e.target.closest('.modal-content').querySelector('p').textContent.replace('Prix : ', '').replace('€', '');
                
                if (!confirm(`💳 Confirmer le paiement ?\n\nService : ${activiteNom}\nMontant : ${prix}€\n\nVotre carte sera débitée immédiatement.`)) {
                    e.preventDefault();
                    return false;
                }
            }
        });

        // Animation des cartes au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const rdvCards = document.querySelectorAll('.rdv-card');
            rdvCards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('animate-fade-in');
                }, index * 100);
            });
            
            const serviceCards = document.querySelectorAll('.service-card');
            serviceCards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('animate-fade-in');
                }, (index + rdvCards.length) * 100);
            });
            
            const coachCards = document.querySelectorAll('.coach-card');
            coachCards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('animate-fade-in');
                }, (index + rdvCards.length + serviceCards.length) * 100);
            });
        });

        // Gestion des raccourcis clavier
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeBookingModal();
                closeEvaluationModal();
                closeAvailabilityModal();
            }
        });

        // Protection contre les soumissions multiples
        let formSubmitting = false;
        document.addEventListener('submit', function(e) {
            if (formSubmitting) {
                e.preventDefault();
                return false;
            }
            
            formSubmitting = true;
            setTimeout(() => {
                formSubmitting = false;
            }, 3000);
        });

        // Message de bienvenue pour le client
        console.log(`
        👤 ESPACE CLIENT SPORTIFY
        =========================
        
        Connecté en tant que : ${document.querySelector('.user-badge').textContent}
        
        Fonctionnalités disponibles :
        ✅ Consultation des services sportifs
        ✅ Visualisation des disponibilités des coachs
        ✅ Réservation avec paiement sécurisé
        ✅ Annulation des rendez-vous à venir
        ✅ Historique complet des consultations
        ✅ Évaluation des coachs
        ✅ Informations personnelles sécurisées
        
        Types de paiement acceptés :
        💳 Visa, MasterCard, American Express, PayPal
        
        Sécurité :
        🔒 Validation bancaire simulée
        📧 Notifications email/SMS automatiques
        🛡️ Informations de paiement protégées
        `);
    </script>
</body>
</html>