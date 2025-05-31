<?php
session_start();

// Vérification de l'authentification et du rôle admin
if (!isset($_SESSION["user_logged_in"]) || $_SESSION["role"] !== "admin") {
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

// Fonction pour générer le CV XML d'un coach
function generateCoachXML($coach_data, $formations = [], $experiences = []) {
    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;
    
    // Élément racine
    $cv = $xml->createElement('cv_coach');
    $xml->appendChild($cv);
    
    // Informations personnelles
    $infos_personnelles = $xml->createElement('informations_personnelles');
    $cv->appendChild($infos_personnelles);
    
    $infos_personnelles->appendChild($xml->createElement('nom', htmlspecialchars($coach_data['nom'])));
    $infos_personnelles->appendChild($xml->createElement('prenom', htmlspecialchars($coach_data['prenom'])));
    $infos_personnelles->appendChild($xml->createElement('email', htmlspecialchars($coach_data['email'])));
    $infos_personnelles->appendChild($xml->createElement('telephone', htmlspecialchars($coach_data['telephone'] ?? '')));
    $infos_personnelles->appendChild($xml->createElement('specialite', htmlspecialchars($coach_data['specialite'])));
    $infos_personnelles->appendChild($xml->createElement('experience_annees', $coach_data['experience_annees'] ?? 0));
    $infos_personnelles->appendChild($xml->createElement('bureau', htmlspecialchars($coach_data['bureau'] ?? '')));
    
    // Formations
    $formations_node = $xml->createElement('formations');
    $cv->appendChild($formations_node);
    
    if (empty($formations)) {
        // Formations par défaut selon la spécialité
        $formations_defaut = [
            'Musculation' => [
                ['diplome' => 'BPJEPS Activités de la Forme', 'etablissement' => 'CREPS Île-de-France', 'annee' => '2019'],
                ['diplome' => 'Certificat Musculation Avancée', 'etablissement' => 'FFHM', 'annee' => '2020']
            ],
            'Fitness' => [
                ['diplome' => 'BPJEPS Activités de la Forme', 'etablissement' => 'CREPS Bordeaux', 'annee' => '2021'],
                ['diplome' => 'Certificat Cours Collectifs', 'etablissement' => 'FFA', 'annee' => '2022']
            ],
            'Tennis' => [
                ['diplome' => 'DEJEPS Tennis', 'etablissement' => 'CREPS PACA', 'annee' => '2017'],
                ['diplome' => 'Certificat Enseignement Tennis', 'etablissement' => 'FFT', 'annee' => '2018']
            ],
            'Cardio-Training' => [
                ['diplome' => 'BPJEPS Activités de la Forme', 'etablissement' => 'CREPS Lyon', 'annee' => '2020'],
                ['diplome' => 'Spécialisation Cardio', 'etablissement' => 'FFEPGV', 'annee' => '2021']
            ]
        ];
        
        $formations = $formations_defaut[$coach_data['specialite']] ?? [
            ['diplome' => 'Formation Sportive', 'etablissement' => 'CREPS France', 'annee' => '2020']
        ];
    }
    
    foreach ($formations as $formation) {
        $formation_node = $xml->createElement('formation');
        $formations_node->appendChild($formation_node);
        
        $formation_node->appendChild($xml->createElement('diplome', htmlspecialchars($formation['diplome'])));
        $formation_node->appendChild($xml->createElement('etablissement', htmlspecialchars($formation['etablissement'])));
        $formation_node->appendChild($xml->createElement('annee', $formation['annee']));
    }
    
    // Expériences
    $experiences_node = $xml->createElement('experiences');
    $cv->appendChild($experiences_node);
    
    if (empty($experiences)) {
        // Expériences par défaut
        $experiences = [
            [
                'poste' => 'Coach ' . $coach_data['specialite'],
                'entreprise' => 'Salle de sport Omnes',
                'duree' => ($coach_data['experience_annees'] ?? 2) . ' ans',
                'description' => 'Encadrement et coaching personnalisé en ' . strtolower($coach_data['specialite'])
            ],
            [
                'poste' => 'Assistant Coach',
                'entreprise' => 'Club Sportif Paris',
                'duree' => '2 ans',
                'description' => 'Assistance et formation des nouveaux pratiquants'
            ]
        ];
    }
    
    foreach ($experiences as $experience) {
        $exp_node = $xml->createElement('experience');
        $experiences_node->appendChild($exp_node);
        
        $exp_node->appendChild($xml->createElement('poste', htmlspecialchars($experience['poste'])));
        $exp_node->appendChild($xml->createElement('entreprise', htmlspecialchars($experience['entreprise'])));
        $exp_node->appendChild($xml->createElement('duree', htmlspecialchars($experience['duree'])));
        $exp_node->appendChild($xml->createElement('description', htmlspecialchars($experience['description'])));
    }
    
    // Compétences et spécialisations
    $competences_node = $xml->createElement('competences');
    $cv->appendChild($competences_node);
    
    $specialisations = [
        'Musculation' => ['Force', 'Hypertrophie', 'Powerlifting', 'Préparation physique'],
        'Fitness' => ['Cardio', 'Renforcement musculaire', 'Cours collectifs', 'Bien-être'],
        'Tennis' => ['Technique', 'Tactique', 'Préparation mentale', 'Compétition'],
        'Cardio-Training' => ['Endurance', 'HIIT', 'Rééducation cardiaque', 'Perte de poids']
    ];
    
    $competences = $specialisations[$coach_data['specialite']] ?? ['Sport', 'Coaching', 'Encadrement'];
    
    foreach ($competences as $competence) {
        $competences_node->appendChild($xml->createElement('competence', htmlspecialchars($competence)));
    }
    
    // Date de génération
    $cv->appendChild($xml->createElement('date_generation', date('Y-m-d H:i:s')));
    
    return $xml->saveXML();
}

// Récupération des informations utilisateur
$user_info = $_SESSION["user_info"];
$message = "";
$message_type = "";

// Données simulées si pas de BDD
$stats_fallback = [
    'total_clients' => 156,
    'total_coachs' => 4,
    'reservations_jour' => 23,
    'revenus_mois' => '15,420€'
];

$coachs_fallback = [
    [
        'id' => 1,
        'user_id' => 2,
        'nom' => 'DUMAIS',
        'prenom' => 'Guy',
        'email' => 'guy.dumais@sportify.com',
        'telephone' => '+33987654321',
        'specialite' => 'Musculation',
        'bureau' => 'Bureau 12',
        'experience_annees' => 5,
        'description' => 'Coach expérimenté en musculation',
        'note_moyenne' => 4.8,
        'nb_evaluations' => 127,
        'statut' => 'actif'
    ],
    [
        'id' => 2,
        'user_id' => 3,
        'nom' => 'MARTIN',
        'prenom' => 'Marie',
        'email' => 'marie.martin@sportify.com',
        'telephone' => '+33567891234',
        'specialite' => 'Fitness',
        'bureau' => 'Bureau 15',
        'experience_annees' => 3,
        'description' => 'Spécialiste fitness et cardio',
        'note_moyenne' => 4.6,
        'nb_evaluations' => 89,
        'statut' => 'actif'
    ],
    [
        'id' => 3,
        'user_id' => 4,
        'nom' => 'BERNARD',
        'prenom' => 'Paul',
        'email' => 'paul.bernard@sportify.com',
        'telephone' => '+33456789123',
        'specialite' => 'Tennis',
        'bureau' => 'Court tennis',
        'experience_annees' => 7,
        'description' => 'Professeur de tennis certifié',
        'note_moyenne' => 4.9,
        'nb_evaluations' => 156,
        'statut' => 'actif'
    ],
    [
        'id' => 4,
        'user_id' => 5,
        'nom' => 'DUBOIS',
        'prenom' => 'Sophie',
        'email' => 'sophie.dubois@sportify.com',
        'telephone' => '+33345678912',
        'specialite' => 'Cardio-Training',
        'bureau' => 'Studio Cardio',
        'experience_annees' => 4,
        'description' => 'Experte en cardio-training',
        'note_moyenne' => 4.5,
        'nb_evaluations' => 73,
        'statut' => 'actif'
    ]
];

// Tentative de connexion à la BDD
$pdo = getDBConnection();
$using_database = ($pdo !== null);

// Récupération des statistiques
if ($using_database) {
    try {
        // Statistiques réelles
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM clients");
        $total_clients = $stmt->fetch()['count'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM coachs");
        $total_coachs = $stmt->fetch()['count'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM rendezvous WHERE date_rdv = CURDATE()");
        $reservations_jour = $stmt->fetch()['count'];
        
        $stats = [
            'total_clients' => $total_clients,
            'total_coachs' => $total_coachs,
            'reservations_jour' => $reservations_jour,
            'revenus_mois' => '15,420€' // Calcul complexe à implémenter
        ];
    } catch (PDOException $e) {
        $stats = $stats_fallback;
        $using_database = false;
    }
} else {
    $stats = $stats_fallback;
}

// Récupération des coachs
if ($using_database) {
    try {
        $stmt = $pdo->query("
            SELECT u.id as user_id, c.id, u.nom, u.prenom, u.email, u.telephone, u.statut,
                   c.specialite, c.experience_annees, c.note_moyenne, c.nb_evaluations, c.bureau, c.description, c.cv_xml
            FROM users u 
            INNER JOIN coachs c ON u.id = c.user_id 
            WHERE u.role = 'coach'
            ORDER BY u.nom, u.prenom
        ");
        $coachs = $stmt->fetchAll();
    } catch (PDOException $e) {
        $coachs = $coachs_fallback;
        $using_database = false;
    }
} else {
    $coachs = $coachs_fallback;
}

// Traitement d'ajout de coach
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_coach'])) {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $telephone = trim($_POST['telephone']);
    $specialite = trim($_POST['specialite']);
    $experience = (int)$_POST['experience_annees'];
    $bureau = trim($_POST['bureau']);
    $description = trim($_POST['description']);
    
    if ($using_database) {
        try {
            $pdo->beginTransaction();
            
            // Vérifier si l'email existe déjà
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $message = "Cet email est déjà utilisé.";
                $message_type = "error";
            } else {
                // Insérer l'utilisateur
                $stmt = $pdo->prepare("INSERT INTO users (email, password, role, nom, prenom, telephone) VALUES (?, ?, 'coach', ?, ?, ?)");
                $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT), $nom, $prenom, $telephone]);
                $user_id = $pdo->lastInsertId();
                
                // Insérer les informations coach
                $stmt = $pdo->prepare("INSERT INTO coachs (user_id, specialite, experience_annees, bureau, description) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $specialite, $experience, $bureau, $description]);
                
                $pdo->commit();
                $message = "Coach ajouté avec succès !";
                $message_type = "success";
                
                // Recharger la liste des coachs
                $stmt = $pdo->query("
                    SELECT u.id as user_id, c.id, u.nom, u.prenom, u.email, u.telephone, u.statut,
                           c.specialite, c.experience_annees, c.note_moyenne, c.nb_evaluations, c.bureau, c.description, c.cv_xml
                    FROM users u 
                    INNER JOIN coachs c ON u.id = c.user_id 
                    WHERE u.role = 'coach'
                    ORDER BY u.nom, u.prenom
                ");
                $coachs = $stmt->fetchAll();
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = "Erreur lors de l'ajout du coach : " . $e->getMessage();
            $message_type = "error";
        }
    } else {
        $message = "Ajout simulé réussi ! (Base de données non disponible)";
        $message_type = "success";
    }
}

// Traitement de suppression de coach
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_coach'])) {
    $coach_id = (int)$_POST['coach_id'];
    
    if ($using_database) {
        try {
            $pdo->beginTransaction();
            
            // Récupérer l'user_id du coach
            $stmt = $pdo->prepare("SELECT user_id FROM coachs WHERE id = ?");
            $stmt->execute([$coach_id]);
            $coach = $stmt->fetch();
            
            if ($coach) {
                // Supprimer l'utilisateur (CASCADE supprimera automatiquement le coach)
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'coach'");
                $stmt->execute([$coach['user_id']]);
                
                $pdo->commit();
                $message = "Coach supprimé avec succès !";
                $message_type = "success";
                
                // Recharger la liste
                $stmt = $pdo->query("
                    SELECT u.id as user_id, c.id, u.nom, u.prenom, u.email, u.telephone, u.statut,
                           c.specialite, c.experience_annees, c.note_moyenne, c.nb_evaluations, c.bureau, c.description, c.cv_xml
                    FROM users u 
                    INNER JOIN coachs c ON u.id = c.user_id 
                    WHERE u.role = 'coach'
                    ORDER BY u.nom, u.prenom
                ");
                $coachs = $stmt->fetchAll();
            } else {
                $message = "Coach non trouvé.";
                $message_type = "error";
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = "Erreur lors de la suppression : " . $e->getMessage();
            $message_type = "error";
        }
    } else {
        $message = "Suppression simulée ! (Base de données non disponible)";
        $message_type = "success";
    }
}

// Traitement de génération CV XML
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['generate_xml'])) {
    $coach_id = (int)$_POST['coach_id'];
    
    // Trouver le coach
    $coach_data = null;
    foreach ($coachs as $coach) {
        if ($coach['id'] == $coach_id) {
            $coach_data = $coach;
            break;
        }
    }
    
    if ($coach_data) {
        $xml_content = generateCoachXML($coach_data);
        
        if ($using_database) {
            try {
                // Sauvegarder le XML dans la base de données
                $stmt = $pdo->prepare("UPDATE coachs SET cv_xml = ? WHERE id = ?");
                $stmt->execute([$xml_content, $coach_id]);
                
                $message = "CV XML généré et sauvegardé avec succès pour " . $coach_data['prenom'] . " " . $coach_data['nom'] . " !";
                $message_type = "success";
            } catch (PDOException $e) {
                $message = "Erreur lors de la sauvegarde du XML : " . $e->getMessage();
                $message_type = "error";
            }
        } else {
            $message = "CV XML généré pour " . $coach_data['prenom'] . " " . $coach_data['nom'] . " ! (Non sauvegardé - BDD indisponible)";
            $message_type = "success";
        }
    } else {
        $message = "Coach non trouvé pour la génération XML.";
        $message_type = "error";
    }
}

// Traitement de téléchargement CV XML
if (isset($_GET['download_xml']) && isset($_GET['coach_id'])) {
    $coach_id = (int)$_GET['coach_id'];
    
    $coach_data = null;
    $xml_content = null;
    
    foreach ($coachs as $coach) {
        if ($coach['id'] == $coach_id) {
            $coach_data = $coach;
            break;
        }
    }
    
    if ($coach_data) {
        if ($using_database && !empty($coach_data['cv_xml'])) {
            $xml_content = $coach_data['cv_xml'];
        } else {
            $xml_content = generateCoachXML($coach_data);
        }
        
        $filename = 'CV_' . $coach_data['prenom'] . '_' . $coach_data['nom'] . '_' . date('Y-m-d') . '.xml';
        
        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($xml_content));
        
        echo $xml_content;
        exit;
    }
}

// Traitement de modification de statut
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['toggle_status'])) {
    $coach_user_id = (int)$_POST['coach_user_id'];
    $new_status = $_POST['new_status'];
    
    if ($using_database) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET statut = ? WHERE id = ? AND role = 'coach'");
            $stmt->execute([$new_status, $coach_user_id]);
            
            $message = "Statut mis à jour avec succès !";
            $message_type = "success";
            
            // Recharger la liste
            $stmt = $pdo->query("
                SELECT u.id as user_id, c.id, u.nom, u.prenom, u.email, u.telephone, u.statut,
                       c.specialite, c.experience_annees, c.note_moyenne, c.nb_evaluations, c.bureau, c.description, c.cv_xml
                FROM users u 
                INNER JOIN coachs c ON u.id = c.user_id 
                WHERE u.role = 'coach'
                ORDER BY u.nom, u.prenom
            ");
            $coachs = $stmt->fetchAll();
        } catch (PDOException $e) {
            $message = "Erreur lors de la mise à jour : " . $e->getMessage();
            $message_type = "error";
        }
    } else {
        $message = "Modification simulée ! (Base de données non disponible)";
        $message_type = "success";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Sportify</title>
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

        .admin-info {
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
            color: #007bff;
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

        .btn {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 0.25rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 123, 255, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745, #1e7e34);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #212529;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
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
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .coaches-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .coaches-table th,
        .coaches-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e1e5e9;
        }

        .coaches-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .coaches-table tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-actif {
            background: #d4edda;
            color: #155724;
        }

        .status-inactif {
            background: #f8d7da;
            color: #721c24;
        }

        .rating {
            color: #ffc107;
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

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .salle-info {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 1rem;
            border-radius: 0 10px 10px 0;
            margin: 1rem 0;
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
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .coaches-table {
                font-size: 0.9rem;
            }
            
            .action-buttons {
                flex-direction: column;
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
            <div class="logo">🛡️ Sportify Admin</div>
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
                    👤 <?php echo htmlspecialchars($user_info['prenom'] . ' ' . $user_info['nom']); ?>
                </div>
                <a href="votre_compte.php?logout" class="logout-btn">🚪 Déconnexion</a>
            </div>
        </div>
    </div>

    <!-- Section Hero -->
    <section class="hero">
        <div class="hero-content">
            <h1>🛡️ Panneau d'Administration</h1>
            <p class="hero-subtitle">Gestion complète de la plateforme Sportify</p>
            <div class="admin-info">
                <strong>Administrateur :</strong> <?php echo htmlspecialchars($user_info['prenom'] . ' ' . $user_info['nom']); ?><br>
                <strong>Email :</strong> <?php echo htmlspecialchars($user_info['email']); ?>
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
                    <div class="stat-icon">👥</div>
                    <div class="stat-number"><?php echo $stats['total_clients']; ?></div>
                    <div class="stat-label">Clients inscrits</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🏋️‍♂️</div>
                    <div class="stat-number"><?php echo $stats['total_coachs']; ?></div>
                    <div class="stat-label">Coachs actifs</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📅</div>
                    <div class="stat-number"><?php echo $stats['reservations_jour']; ?></div>
                    <div class="stat-label">RDV aujourd'hui</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-number"><?php echo $stats['revenus_mois']; ?></div>
                    <div class="stat-label">Revenus du mois</div>
                </div>
            </div>

            <!-- Gestion des coachs -->
            <div class="section-card">
                <h2 class="section-title">
                    🏋️‍♂️ Gestion des Coachs et Personnels de Sport
                </h2>
                
                <div style="margin-bottom: 1.5rem;">
                    <button class="btn" onclick="showAddCoachModal()">
                        ➕ Ajouter un nouveau coach
                    </button>
                    <button class="btn btn-success" onclick="generateAllXML()">
                        📄 Générer tous les CV XML
                    </button>
                </div>

                <table class="coaches-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom & Prénom</th>
                            <th>Email & Téléphone</th>
                            <th>Spécialité</th>
                            <th>Bureau</th>
                            <th>Expérience</th>
                            <th>Note</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($coachs as $coach): ?>
                        <tr>
                            <td><strong>#<?php echo $coach['id']; ?></strong></td>
                            <td>
                                <strong><?php echo htmlspecialchars($coach['prenom'] . ' ' . $coach['nom']); ?></strong>
                            </td>
                            <td>
                                📧 <?php echo htmlspecialchars($coach['email']); ?><br>
                                <?php if ($coach['telephone']): ?>
                                    📞 <?php echo htmlspecialchars($coach['telephone']); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="background: #e3f2fd; color: #1976d2; padding: 0.25rem 0.5rem; border-radius: 10px; font-size: 0.9rem;">
                                    <?php echo htmlspecialchars($coach['specialite']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($coach['bureau'] ?? 'Non défini'); ?></td>
                            <td><?php echo ($coach['experience_annees'] ?? 0) . ' ans'; ?></td>
                            <td>
                                <span class="rating">⭐ <?php echo number_format($coach['note_moyenne'] ?? 0, 1); ?></span><br>
                                <small>(<?php echo $coach['nb_evaluations'] ?? 0; ?> avis)</small>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $coach['statut'] ?? 'actif'; ?>">
                                    <?php echo ucfirst($coach['statut'] ?? 'actif'); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <!-- Bouton Activer/Désactiver -->
                                    <form method="POST" style="display: inline-block;">
                                        <input type="hidden" name="coach_user_id" value="<?php echo $coach['user_id']; ?>">
                                        <input type="hidden" name="new_status" value="<?php echo ($coach['statut'] ?? 'actif') === 'actif' ? 'inactif' : 'actif'; ?>">
                                        <button type="submit" name="toggle_status" class="btn btn-sm <?php echo ($coach['statut'] ?? 'actif') === 'actif' ? 'btn-warning' : 'btn-success'; ?>" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir modifier le statut de ce coach ?')">
                                            <?php echo ($coach['statut'] ?? 'actif') === 'actif' ? '⏸️ Désactiver' : '✅ Activer'; ?>
                                        </button>
                                    </form>
                                    
                                    <!-- Bouton Générer XML -->
                                    <form method="POST" style="display: inline-block;">
                                        <input type="hidden" name="coach_id" value="<?php echo $coach['id']; ?>">
                                        <button type="submit" name="generate_xml" class="btn btn-secondary btn-sm">
                                            📄 CV XML
                                        </button>
                                    </form>
                                    
                                    <!-- Bouton Télécharger XML -->
                                    <?php if ($using_database && !empty($coach['cv_xml'])): ?>
                                        <a href="?download_xml=1&coach_id=<?php echo $coach['id']; ?>" class="btn btn-success btn-sm">
                                            ⬇️ Télécharger
                                        </a>
                                    <?php endif; ?>
                                    
                                    <!-- Bouton Voir détails -->
                                    <button class="btn btn-secondary btn-sm" onclick="viewCoachDetails(<?php echo $coach['id']; ?>, '<?php echo htmlspecialchars($coach['prenom'] . ' ' . $coach['nom']); ?>')">
                                        👁️ Détails
                                    </button>
                                    
                                    <!-- Bouton Supprimer -->
                                    <form method="POST" style="display: inline-block;">
                                        <input type="hidden" name="coach_id" value="<?php echo $coach['id']; ?>">
                                        <button type="submit" name="delete_coach" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('⚠️ ATTENTION : Supprimer définitivement le coach <?php echo htmlspecialchars($coach['prenom'] . ' ' . $coach['nom']); ?> ?\n\nCette action supprimera :\n- Le compte utilisateur\n- Toutes les informations du coach\n- Tous les rendez-vous associés\n\nCette action est IRRÉVERSIBLE !')">
                                            🗑️ Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Informations Salle de Sport Omnes -->
            <div class="section-card">
                <h2 class="section-title">
                    🏢 Informations Salle de Sport Omnes
                </h2>
                
                <div class="salle-info">
                    <h3>📍 Salle de sport Omnes</h3>
                    <p><strong>Adresse :</strong> 123 Rue du Sport, 75001 Paris</p>
                    <p><strong>Téléphone :</strong> +33 1 23 45 67 89</p>
                    <p><strong>Email :</strong> salle@sportify.com</p>
                    <p><strong>Capacité maximale :</strong> 50 personnes</p>
                    <p><strong>Statut :</strong> <span style="color: green; font-weight: bold;">🟢 Ouvert</span></p>
                </div>
                
                <h4>🕒 Horaires d'ouverture :</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 1rem 0;">
                    <div><strong>Lundi :</strong> 7:00 - 22:00</div>
                    <div><strong>Mardi :</strong> 7:00 - 22:00</div>
                    <div><strong>Mercredi :</strong> 7:00 - 22:00</div>
                    <div><strong>Jeudi :</strong> 7:00 - 22:00</div>
                    <div><strong>Vendredi :</strong> 7:00 - 22:00</div>
                    <div><strong>Samedi :</strong> 8:00 - 20:00</div>
                    <div><strong>Dimanche :</strong> 8:00 - 20:00</div>
                </div>
                
                <h4>🏃‍♂️ Services disponibles :</h4>
                <ul style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.5rem; margin: 1rem 0; list-style: none;">
                    <li>✅ Musculation</li>
                    <li>✅ Cardio-training</li>
                    <li>✅ Cours collectifs</li>
                    <li>✅ Vestiaires</li>
                    <li>✅ Parking gratuit</li>
                    <li>✅ Wifi gratuit</li>
                </ul>
                
                <h4>💰 Tarifs :</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 1rem 0;">
                    <div><strong>Séance individuelle :</strong> 35€</div>
                    <div><strong>Cours collectif :</strong> 25€</div>
                    <div><strong>Consultation :</strong> 30€</div>
                    <div><strong>Visite découverte :</strong> Gratuit</div>
                </div>
                
                <div style="margin-top: 1.5rem;">
                    <button class="btn btn-secondary">⚙️ Modifier les informations</button>
                    <button class="btn btn-success">📅 Gérer les horaires</button>
                    <button class="btn btn-warning">💰 Modifier les tarifs</button>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="section-card">
                <h2 class="section-title">
                    ⚡ Actions Rapides d'Administration
                </h2>
                <div class="form-grid">
                    <button class="btn" onclick="showAvailabilityManager()">📅 Gérer disponibilités des coachs</button>
                    <button class="btn btn-secondary">📊 Générer rapport mensuel</button>
                    <button class="btn btn-success">📧 Envoyer newsletter</button>
                    <button class="btn btn-warning">💬 Configurer système de chat</button>
                    <button class="btn" onclick="showBulletinManager()">📰 Gérer les bulletins</button>
                    <button class="btn btn-danger">🗂️ Export données complètes</button>
                </div>
            </div>

        </div>
    </section>

    <!-- Modal d'ajout de coach -->
    <div id="addCoachModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddCoachModal()">&times;</span>
            <h2>➕ Ajouter un nouveau coach</h2>
            
            <form method="POST" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="prenom">Prénom *</label>
                        <input type="text" id="prenom" name="prenom" required>
                    </div>
                    <div class="form-group">
                        <label for="nom">Nom *</label>
                        <input type="text" id="nom" name="nom" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe *</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" placeholder="+33123456789">
                    </div>
                    <div class="form-group">
                        <label for="specialite">Spécialité *</label>
                        <select id="specialite" name="specialite" required>
                            <option value="">Choisir une spécialité</option>
                            <option value="Musculation">Musculation</option>
                            <option value="Fitness">Fitness</option>
                            <option value="Cardio-Training">Cardio-Training</option>
                            <option value="Tennis">Tennis</option>
                            <option value="Natation">Natation</option>
                            <option value="Basketball">Basketball</option>
                            <option value="Football">Football</option>
                            <option value="Rugby">Rugby</option>
                            <option value="Yoga">Yoga</option>
                            <option value="Pilates">Pilates</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="experience_annees">Années d'expérience</label>
                        <input type="number" id="experience_annees" name="experience_annees" min="0" max="50" value="0">
                    </div>
                    <div class="form-group">
                        <label for="bureau">Bureau/Local</label>
                        <input type="text" id="bureau" name="bureau" placeholder="Bureau 12 ou Court tennis">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description du coach</label>
                    <textarea id="description" name="description" rows="3" placeholder="Présentation du coach, ses compétences..."></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="add_coach" class="btn btn-lg">
                        ➕ Ajouter le coach
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de détails coach -->
    <div id="coachDetailsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeCoachDetailsModal()">&times;</span>
            <div id="coachDetailsContent">
                <!-- Contenu chargé dynamiquement -->
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Sportify - Administration</p>
        </div>
    </footer>

    <script>
        function showAddCoachModal() {
            document.getElementById('addCoachModal').style.display = 'block';
        }

        function closeAddCoachModal() {
            document.getElementById('addCoachModal').style.display = 'none';
        }

        function closeCoachDetailsModal() {
            document.getElementById('coachDetailsModal').style.display = 'none';
        }

        function viewCoachDetails(coachId, coachName) {
            const coachDetailsContent = document.getElementById('coachDetailsContent');
            
            coachDetailsContent.innerHTML = `
                <h2>🏋️‍♂️ Détails du Coach ${coachName}</h2>
                <div style="margin-top: 1.5rem;">
                    <h3>📋 Informations personnelles</h3>
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 10px; margin: 1rem 0;">
                        <p><strong>ID:</strong> #${coachId}</p>
                        <p><strong>Nom complet:</strong> ${coachName}</p>
                        <p><strong>Email:</strong> Voir tableau principal</p>
                        <p><strong>Téléphone:</strong> Voir tableau principal</p>
                        <p><strong>Spécialité:</strong> Voir tableau principal</p>
                        <p><strong>Bureau:</strong> Voir tableau principal</p>
                    </div>
                    
                    <h3>📸 Photos et médias</h3>
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 10px; margin: 1rem 0;">
                        <p>🖼️ Photos de profil : <em>Fonctionnalité à implémenter</em></p>
                        <p>🎥 Vidéo de présentation : <em>Fonctionnalité à implémenter</em></p>
                        <button class="btn btn-sm" style="margin-top: 0.5rem;">📁 Gérer les médias</button>
                    </div>
                    
                    <h3>📄 CV XML</h3>
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 10px; margin: 1rem 0;">
                        <p>📝 CV au format XML : <em>Utiliser les boutons du tableau principal</em></p>
                        <p>Le CV XML contient :</p>
                        <ul>
                            <li>✅ Informations personnelles complètes</li>
                            <li>✅ Formations et diplômes</li>
                            <li>✅ Expériences professionnelles</li>
                            <li>✅ Compétences et spécialisations</li>
                            <li>✅ Date de génération</li>
                        </ul>
                    </div>
                    
                    <h3>📅 Disponibilités de la semaine</h3>
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 10px; margin: 1rem 0;">
                        <p>⏰ Planning type :</p>
                        <div style="margin-top: 0.5rem;">
                            <p><strong>Lundi:</strong> 09:00-12:00, 14:00-18:00</p>
                            <p><strong>Mardi:</strong> 08:00-12:00, 13:00-17:00</p>
                            <p><strong>Mercredi:</strong> 10:00-16:00</p>
                            <p><strong>Jeudi:</strong> 09:00-12:00, 14:00-19:00</p>
                            <p><strong>Vendredi:</strong> 08:00-15:00</p>
                            <p><strong>Samedi:</strong> 09:00-13:00</p>
                            <p><strong>Dimanche:</strong> Fermé</p>
                        </div>
                        <button class="btn btn-sm" style="margin-top: 0.5rem;" onclick="showAvailabilityManager()">⚙️ Modifier planning</button>
                    </div>
                    
                    <h3>💬 Système de communication</h3>
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 10px; margin: 1rem 0;">
                        <p>📧 Compte email : <em>Configuré et actif</em></p>
                        <p>💬 Chat texto : <em>Système intégré actif</em></p>
                        <p>🎧 Chat audio : <em>Disponible via navigateur</em></p>
                        <p>📹 Chat vidéo : <em>Disponible via navigateur</em></p>
                        <p><em>Les clients peuvent contacter ce coach via tous ces moyens</em></p>
                        <button class="btn btn-sm" style="margin-top: 0.5rem;">⚙️ Tester la communication</button>
                    </div>
                    
                    <div style="margin-top: 2rem; text-align: center;">
                        <button class="btn btn-success">✏️ Modifier le profil complet</button>
                        <button class="btn btn-warning">📅 Gérer les disponibilités</button>
                    </div>
                </div>
            `;
            
            document.getElementById('coachDetailsModal').style.display = 'block';
        }

        function generateAllXML() {
            if (confirm('Générer les CV XML pour tous les coachs actifs ?')) {
                alert('Fonctionnalité en développement !\n\nCela générera automatiquement les CV XML pour tous les coachs avec leurs formations, expériences et compétences.');
            }
        }

        function showAvailabilityManager() {
            alert('Gestionnaire de disponibilités\n\nCette fonctionnalité permettra à l\'administrateur de :\n\n• Définir les créneaux disponibles pour chaque coach\n• Modifier les plannings hebdomadaires\n• Gérer les exceptions et congés\n• Consulter avec les coachs leurs préférences\n\nÀ implémenter dans la prochaine version.');
        }

        function showBulletinManager() {
            alert('Gestionnaire de bulletins\n\nCette fonctionnalité permettra de :\n\n• Créer des bulletins d\'information\n• Publier des actualités\n• Annoncer des événements\n• Gérer les communications avec les utilisateurs\n\nÀ implémenter prochainement.');
        }

        // Fermer les modals en cliquant à l'extérieur
        window.onclick = function(event) {
            const addModal = document.getElementById('addCoachModal');
            const detailsModal = document.getElementById('coachDetailsModal');
            
            if (event.target == addModal) {
                addModal.style.display = "none";
            }
            if (event.target == detailsModal) {
                detailsModal.style.display = "none";
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
            
            const sectionCards = document.querySelectorAll('.section-card');
            sectionCards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('animate-fade-in');
                }, (index + 4) * 100);
            });
        });

        // Validation du formulaire d'ajout de coach
        const addCoachForm = document.querySelector('#addCoachModal form');
        if (addCoachForm) {
            addCoachForm.addEventListener('submit', function(e) {
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                const specialite = document.getElementById('specialite').value;
                const nom = document.getElementById('nom').value;
                const prenom = document.getElementById('prenom').value;
                
                if (!email || !password || !specialite || !nom || !prenom) {
                    e.preventDefault();
                    alert('Veuillez remplir tous les champs obligatoires (marqués par *).');
                    return false;
                }
                
                if (password.length < 6) {
                    e.preventDefault();
                    alert('Le mot de passe doit contenir au moins 6 caractères.');
                    return false;
                }
                
                // Validation email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    e.preventDefault();
                    alert('Veuillez entrer un email valide.');
                    return false;
                }
                
                // Confirmation avant ajout
                if (!confirm(`Ajouter le coach ${prenom} ${nom} ?\n\nSpécialité : ${specialite}\nEmail : ${email}`)) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        // Messages d'information sur les fonctionnalités
        function showFeatureInfo(feature) {
            const messages = {
                'photos': 'Gestion des photos\n\n• Upload de photos de profil\n• Galerie de photos du coach\n• Gestion des formats et tailles\n• Validation automatique\n\nÀ implémenter prochainement.',
                'videos': 'Gestion des vidéos\n\n• Upload de vidéos de présentation\n• Streaming intégré\n• Formats supportés : MP4, WebM\n• Compression automatique\n\nÀ implémenter prochainement.',
                'chat': 'Système de chat intégré\n\n• Chat texto en temps réel\n• Appels audio via WebRTC\n• Appels vidéo HD\n• Historique des conversations\n• Notifications push\n\nÀ implémenter dans la v2.0'
            };
            
            alert(messages[feature] || 'Fonctionnalité en développement');
        }

        // Fonction pour prévisualiser le XML
        function previewXML(coachId) {
            alert('Prévisualisation CV XML\n\nCette fonctionnalité affichera :\n\n• Le contenu XML formaté\n• Les sections du CV\n• La structure des données\n• Un aperçu avant téléchargement\n\nUtilisez le bouton "CV XML" puis "Télécharger" pour obtenir le fichier.');
        }

        // Gestion des erreurs réseau
        window.addEventListener('error', function(e) {
            console.error('Erreur détectée:', e.error);
        });

        // Auto-refresh des statistiques (optionnel)
        function refreshStats() {
            // Cette fonction pourrait recharger les statistiques via AJAX
            console.log('Refresh des statistiques...');
        }

        // Actualiser les stats toutes les 5 minutes
        setInterval(refreshStats, 300000);

        // Message de bienvenue pour l'admin
        console.log(`
        🛡️ PANNEAU D'ADMINISTRATION SPORTIFY
        =====================================
        
        Connecté en tant que : ${document.querySelector('.user-badge').textContent}
        
        Fonctionnalités disponibles :
        ✅ Gestion complète des coachs
        ✅ Ajout/suppression de personnel
        ✅ Génération CV XML automatique
        ✅ Gestion des statuts
        ✅ Informations salle de sport
        ✅ Système de communication intégré
        
        En développement :
        🔄 Upload photos/vidéos
        🔄 Chat temps réel
        🔄 Gestion des disponibilités
        🔄 Rapports avancés
        `);
    </script>
</body>
</html>