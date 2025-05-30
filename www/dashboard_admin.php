<?php
session_start();
require_once 'config.php';

// Vérification de la connexion admin
requireRole('admin');

$user = $_SESSION['user_info'];

try {
    $pdo = getDBConnection();
    
    // Statistiques générales
    $stats = [];
    
    // Nombre total de coachs
    $stmt = $pdo->query("SELECT COUNT(*) as total, 
                        SUM(CASE WHEN u.statut = 'actif' THEN 1 ELSE 0 END) as actifs 
                        FROM coachs c 
                        JOIN users u ON c.user_id = u.id");
    $coach_stats = $stmt->fetch();
    $stats['total_coachs'] = $coach_stats['total'];
    $stats['coachs_actifs'] = $coach_stats['actifs'];
    
    // Nombre total de clients
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM clients c JOIN users u ON c.user_id = u.id WHERE u.statut = 'actif'");
    $stats['total_clients'] = $stmt->fetch()['total'];
    
    // Rendez-vous cette semaine
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM rendezvous WHERE date_rdv >= CURDATE() AND date_rdv < DATE_ADD(CURDATE(), INTERVAL 7 DAY)");
    $stats['rdv_semaine'] = $stmt->fetch()['total'];
    
    // Rendez-vous ce mois
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM rendezvous WHERE YEAR(date_rdv) = YEAR(CURDATE()) AND MONTH(date_rdv) = MONTH(CURDATE())");
    $stats['rdv_mois'] = $stmt->fetch()['total'];
    
    // Revenus ce mois
    $stmt = $pdo->query("SELECT SUM(prix) as total FROM rendezvous WHERE YEAR(date_rdv) = YEAR(CURDATE()) AND MONTH(date_rdv) = MONTH(CURDATE()) AND statut IN ('termine', 'confirme')");
    $stats['revenus_mois'] = $stmt->fetch()['total'] ?? 0;
    
    // Nouveaux clients ce mois
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'client' AND YEAR(date_creation) = YEAR(CURDATE()) AND MONTH(date_creation) = MONTH(CURDATE())");
    $stats['nouveaux_clients'] = $stmt->fetch()['total'];
    
    // Liste des coachs pour aperçu
    $stmt = $pdo->query("SELECT c.*, u.nom, u.prenom, u.email, u.statut,
                        (SELECT COUNT(*) FROM rendezvous r WHERE r.coach_id = c.id) as nb_clients
                        FROM coachs c 
                        JOIN users u ON c.user_id = u.id 
                        ORDER BY u.nom, u.prenom 
                        LIMIT 5");
    $coachs_apercu = $stmt->fetchAll();
    
    // Liste des clients pour aperçu
    $stmt = $pdo->query("SELECT cl.*, u.nom, u.prenom, u.email, u.date_creation,
                        (SELECT COUNT(*) FROM rendezvous r JOIN coachs c ON r.coach_id = c.id WHERE c.user_id = cl.user_id) as nb_rdv
                        FROM clients cl 
                        JOIN users u ON cl.user_id = u.id 
                        WHERE u.statut = 'actif'
                        ORDER BY u.date_creation DESC 
                        LIMIT 3");
    $clients_apercu = $stmt->fetchAll();
    
    // Activités récentes (simulation basée sur les dernières modifications)
    $stmt = $pdo->query("SELECT 'Nouveau client inscrit' as action, CONCAT(u.prenom, ' ', u.nom) as details, 
                        TIMESTAMPDIFF(HOUR, u.date_creation, NOW()) as heures_ago, 'add' as type
                        FROM users u WHERE u.role = 'client' ORDER BY u.date_creation DESC LIMIT 3");
    $recent_activities = $stmt->fetchAll();
    
    // Alertes système
    $system_alerts = [];
    
    // Vérifier les coachs inactifs
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'coach' AND statut = 'inactif'");
    $coachs_inactifs = $stmt->fetch()['count'];
    if ($coachs_inactifs > 0) {
        $system_alerts[] = ['type' => 'warning', 'message' => "$coachs_inactifs coach(s) inactif(s)"];
    }
    
    // Vérifier les rendez-vous en attente
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM rendezvous WHERE statut = 'planifie'");
    $rdv_attente = $stmt->fetch()['count'];
    if ($rdv_attente > 0) {
        $system_alerts[] = ['type' => 'info', 'message' => "$rdv_attente rendez-vous en attente de confirmation"];
    }
    
    // Sauvegarde OK
    $system_alerts[] = ['type' => 'success', 'message' => 'Connexion base de données opérationnelle'];
    
} catch (PDOException $e) {
    error_log("Erreur dashboard admin: " . $e->getMessage());
    $stats = ['total_coachs' => 0, 'coachs_actifs' => 0, 'total_clients' => 0, 'rdv_semaine' => 0, 'rdv_mois' => 0, 'revenus_mois' => 0, 'nouveaux_clients' => 0];
    $coachs_apercu = [];
    $clients_apercu = [];
    $recent_activities = [];
    $system_alerts = [['type' => 'error', 'message' => 'Erreur de connexion à la base de données']];
}

// Traitement des actions admin
$success_message = '';
$error_message = '';

if ($_POST) {
    if (isset($_POST['toggle_coach_status'])) {
        $coach_id = (int)$_POST['coach_id'];
        try {
            $stmt = $pdo->prepare("UPDATE users u 
                                  JOIN coachs c ON u.id = c.user_id 
                                  SET u.statut = CASE WHEN u.statut = 'actif' THEN 'inactif' ELSE 'actif' END 
                                  WHERE c.id = ?");
            $stmt->execute([$coach_id]);
            $success_message = "Statut du coach modifié avec succès.";
            // Recharger la page pour voir les changements
            header('Location: dashboard_admin.php');
            exit;
        } catch (PDOException $e) {
            $error_message = "Erreur lors de la modification du statut.";
        }
    }
    
    if (isset($_POST['delete_coach'])) {
        $coach_id = (int)$_POST['coach_id'];
        try {
            $pdo->beginTransaction();
            
            // Récupérer l'user_id
            $stmt = $pdo->prepare("SELECT user_id FROM coachs WHERE id = ?");
            $stmt->execute([$coach_id]);
            $user_id = $stmt->fetch()['user_id'];
            
            // Supprimer les rendez-vous liés
            $stmt = $pdo->prepare("DELETE FROM rendezvous WHERE coach_id = ?");
            $stmt->execute([$coach_id]);
            
            // Supprimer les disponibilités
            $stmt = $pdo->prepare("DELETE FROM disponibilites WHERE coach_id = ?");
            $stmt->execute([$coach_id]);
            
            // Supprimer les activités du coach
            $stmt = $pdo->prepare("DELETE FROM coach_activites WHERE coach_id = ?");
            $stmt->execute([$coach_id]);
            
            // Supprimer le coach
            $stmt = $pdo->prepare("DELETE FROM coachs WHERE id = ?");
            $stmt->execute([$coach_id]);
            
            // Supprimer l'utilisateur
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            
            $pdo->commit();
            $success_message = "Coach supprimé avec succès.";
            // Recharger la page
            header('Location: dashboard_admin.php');
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error_message = "Erreur lors de la suppression du coach.";
        }
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
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .admin-nav {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-logo h2 {
            font-size: 1.5rem;
        }

        .nav-logo a {
            color: white;
            text-decoration: none;
        }

        .nav-menu ul {
            list-style: none;
            display: flex;
            gap: 1.5rem;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-menu a:hover,
        .nav-menu a.active {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-avatar {
            font-size: 1.5rem;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
        }

        .user-role {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .logout-btn {
            background: rgba(231, 76, 60, 0.2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #e74c3c;
            transform: translateY(-2px);
        }

        .admin-main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 20px;
        }

        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            border-radius: 15px;
            margin-bottom: 2rem;
            text-align: center;
        }

        .dashboard-header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .dashboard-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .system-alerts {
            margin-bottom: 2rem;
        }

        .system-alerts h2 {
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        .alerts-list {
            display: grid;
            gap: 0.5rem;
        }

        .admin-stats {
            margin-bottom: 3rem;
        }

        .admin-stats h2 {
            margin-bottom: 1.5rem;
            color: #2c3e50;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            font-size: 3rem;
            opacity: 0.8;
        }

        .stat-content {
            flex: 1;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
            display: block;
        }

        .stat-label {
            color: #7f8c8d;
            font-size: 0.9rem;
            display: block;
        }

        .stat-sublabel {
            color: #95a5a6;
            font-size: 0.8rem;
            display: block;
        }

        .stat-trend {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            background: #d4edda;
            color: #155724;
        }

        .admin-quick-actions {
            margin-bottom: 3rem;
        }

        .admin-quick-actions h2 {
            margin-bottom: 1.5rem;
            color: #2c3e50;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .action-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            color: inherit;
            text-decoration: none;
        }

        .action-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }

        .action-card h3 {
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }

        .action-card p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .coaches-management,
        .clients-management,
        .gym-configuration,
        .recent-activity,
        .admin-tools {
            margin-bottom: 3rem;
        }

        .section-header-admin {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-header-admin h2 {
            color: #2c3e50;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            color: #3498db;
            border: 2px solid #3498db;
        }

        .btn-outline:hover {
            background: #3498db;
            color: white;
        }

        .admin-table {
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .admin-table th,
        .admin-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .admin-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        .admin-table tr:hover {
            background: #f8f9fa;
        }

        .coach-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .coach-avatar-small {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
        }

        .coach-avatar-small img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .coach-email {
            font-size: 0.8rem;
            color: #7f8c8d;
        }

        .client-count {
            font-weight: bold;
            color: #3498db;
        }

        .client-label {
            font-size: 0.8rem;
            color: #7f8c8d;
        }

        .status-toggle {
            padding: 0.25rem 0.75rem;
            border: none;
            border-radius: 20px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .status-toggle.actif {
            background: #d4edda;
            color: #155724;
        }

        .status-toggle.inactif {
            background: #f8d7da;
            color: #721c24;
        }

        .actions-cell {
            display: flex;
            gap: 0.5rem;
        }

        .btn-action {
            padding: 0.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .btn-action.edit {
            background: #fff3cd;
            color: #856404;
        }

        .btn-action.xml {
            background: #d1ecf1;
            color: #0c5460;
        }

        .btn-action.delete {
            background: #f8d7da;
            color: #721c24;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .clients-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .client-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .client-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .client-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .client-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
        }

        .client-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .client-info h4 {
            margin-bottom: 0.25rem;
            color: #2c3e50;
        }

        .client-info p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .client-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-weight: bold;
            color: #3498db;
            display: block;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #7f8c8d;
        }

        .client-actions {
            text-align: center;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }

        .gym-config-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .config-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .config-card:hover {
            transform: translateY(-5px);
        }

        .config-card h3 {
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        .config-content {
            margin-bottom: 1rem;
        }

        .config-content p {
            margin-bottom: 0.5rem;
            color: #555;
        }

        .config-content ul {
            list-style: none;
            padding: 0;
        }

        .config-content li {
            padding: 0.25rem 0;
            color: #555;
        }

        .activity-timeline {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .activity-icon.add {
            background: #d4edda;
            color: #155724;
        }

        .activity-icon.edit {
            background: #fff3cd;
            color: #856404;
        }

        .activity-icon.delete {
            background: #f8d7da;
            color: #721c24;
        }

        .activity-icon.xml {
            background: #d1ecf1;
            color: #0c5460;
        }

        .activity-content {
            flex: 1;
        }

        .activity-content h4 {
            margin-bottom: 0.25rem;
            color: #2c3e50;
        }

        .activity-content p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .activity-time {
            color: #95a5a6;
            font-size: 0.8rem;
        }

        .activity-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .tools-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .tool-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s ease;
        }

        .tool-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .tool-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }

        .tool-card h3 {
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        .tool-card p {
            color: #7f8c8d;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .tool-features {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .tool-features span {
            background: #f8f9fa;
            color: #555;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav-menu ul {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .dashboard-header h1 {
                font-size: 2rem;
            }
            
            .section-header-admin {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }
            
            .header-actions {
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
    <!-- Navigation Admin -->
    <div class="admin-nav">
        <div class="nav-container">
            <div class="nav-logo">
                <h2><a href="accueil.php">🛡️ Admin Sportify</a></h2>
            </div>
            <nav class="nav-menu">
                <ul>
                    <li><a href="dashboard_admin.php" class="active">📊 Tableau de Bord</a></li>
                    <li><a href="admin_add_coach.php">👨‍🏫 Ajouter Coach</a></li>
                    <li><a href="admin_coachs.php">👨‍🏫 Gestion Coachs</a></li>
                    <li><a href="admin_clients.php">👥 Gestion Clients</a></li>
                    <li><a href="admin_xml.php">📄 CV XML</a></li>
                    <li><a href="admin_salle.php">🏢 Salle de Sport</a></li>
                </ul>
            </nav>
            <div class="nav-user">
                <div class="user-info">
                    <span class="user-avatar">🛡️</span>
                    <div class="user-details">
                        <span class="user-name"><?php echo htmlspecialchars($user['prenom']); ?></span>
                        <span class="user-role">Administrateur</span>
                    </div>
                </div>
                <a href="votre_compte.php?logout=1" class="logout-btn">🚪 Déconnexion</a>
            </div>
        </div>
    </div>

    <!-- Contenu Principal -->
    <div class="admin-main">
        <!-- Header Dashboard -->
        <div class="dashboard-header">
            <div class="container">
                <h1>🛡️ Tableau de bord administrateur</h1>
                <p>Bienvenue <?php echo htmlspecialchars($user['prenom']); ?>, gérez votre plateforme Sportify connectée à la base de données</p>
            </div>
        </div>

        <?php if (!empty($success_message)): ?>
        <div class="container">
            <div class="alert alert-success">
                <span class="alert-icon">✅</span>
                <?php echo $success_message; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
        <div class="container">
            <div class="alert alert-error">
                <span class="alert-icon">❌</span>
                <?php echo $error_message; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Alertes système -->
        <section class="system-alerts">
            <div class="container">
                <h2>🚨 Alertes système</h2>
                <div class="alerts-list">
                    <?php foreach ($system_alerts as $alert): ?>
                    <div class="alert alert-<?php echo $alert['type']; ?>">
                        <span class="alert-icon">
                            <?php 
                            echo $alert['type'] === 'warning' ? '⚠️' : 
                                ($alert['type'] === 'info' ? 'ℹ️' : 
                                ($alert['type'] === 'error' ? '❌' : '✅'));
                            ?>
                        </span>
                        <?php echo htmlspecialchars($alert['message']); ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Statistiques principales -->
        <section class="admin-stats">
            <div class="container">
                <h2>📈 Statistiques générales (BDD en temps réel)</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">👨‍🏫</div>
                        <div class="stat-content">
                            <span class="stat-number"><?php echo $stats['total_coachs']; ?></span>
                            <span class="stat-label">Coachs total</span>
                            <span class="stat-sublabel"><?php echo $stats['coachs_actifs']; ?> actifs</span>
                        </div>
                        <div class="stat-trend">🗃️ Base de données</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">👥</div>
                        <div class="stat-content">
                            <span class="stat-number"><?php echo $stats['total_clients']; ?></span>
                            <span class="stat-label">Clients inscrits</span>
                            <span class="stat-sublabel"><?php echo $stats['nouveaux_clients']; ?> nouveaux</span>
                        </div>
                        <div class="stat-trend">📈 En croissance</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">📅</div>
                        <div class="stat-content">
                            <span class="stat-number"><?php echo $stats['rdv_semaine']; ?></span>
                            <span class="stat-label">RDV cette semaine</span>
                            <span class="stat-sublabel"><?php echo $stats['rdv_mois']; ?> ce mois</span>
                        </div>
                        <div class="stat-trend">📈 Actif</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">💰</div>
                        <div class="stat-content">
                            <span class="stat-number">€<?php echo number_format($stats['revenus_mois'], 0); ?></span>
                            <span class="stat-label">Revenus ce mois</span>
                            <span class="stat-sublabel">Moyenne €<?php echo $stats['rdv_mois'] > 0 ? number_format($stats['revenus_mois']/$stats['rdv_mois'], 0) : 0; ?>/RDV</span>
                        </div>
                        <div class="stat-trend">💰 Rentable</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Actions rapides d'administration -->
        <section class="admin-quick-actions">
            <div class="container">
                <h2>⚡ Actions rapides</h2>
                <div class="actions-grid">
                    <a href="admin_add_coach.php" class="action-card">
                        <div class="action-icon">➕</div>
                        <h3>Ajouter un coach</h3>
                        <p>Créer un nouveau profil de coach avec toutes ses informations</p>
                    </a>

                    <a href="admin_xml_generator.php" class="action-card">
                        <div class="action-icon">📄</div>
                        <h3>Générer CV XML</h3>
                        <p>Créer des CV XML pour les coachs avec formations et expériences</p>
                    </a>

                    <a href="admin_salle.php" class="action-card">
                        <div class="action-icon">🏢</div>
                        <h3>Gérer la salle Omnes</h3>
                        <p>Configuration de la salle de sport et de ses services</p>
                    </a>

                    <a href="admin_backup.php" class="action-card">
                        <div class="action-icon">💾</div>
                        <h3>Sauvegarde</h3>
                        <p>Exporter et sauvegarder toutes les données</p>
                    </a>
                </div>
            </div>
        </section>

        <!-- Gestion des coachs - Aperçu -->
        <section class="coaches-management">
            <div class="container">
                <div class="section-header-admin">
                    <h2>👨‍🏫 Gestion des coachs (BDD)</h2>
                    <div class="header-actions">
                        <a href="admin_add_coach.php" class="btn btn-primary">➕ Ajouter un coach</a>
                        <a href="admin_coachs.php" class="btn btn-outline">Voir tous</a>
                    </div>
                </div>

                <div class="coaches-table">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Coach</th>
                                <th>Spécialité</th>
                                <th>Rendez-vous</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($coachs_apercu as $coach): ?>
                            <tr>
                                <td class="coach-info">
                                    <div class="coach-avatar-small">
                                        <img src="https://via.placeholder.com/40x40/007BFF/ffffff?text=<?php echo substr($coach['prenom'], 0, 1); ?>" 
                                             alt="<?php echo $coach['prenom']; ?>">
                                    </div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($coach['prenom'] . ' ' . $coach['nom']); ?></strong>
                                        <div class="coach-email"><?php echo htmlspecialchars($coach['email']); ?></div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($coach['specialite']); ?></td>
                                <td>
                                    <span class="client-count"><?php echo $coach['nb_clients']; ?></span>
                                    <span class="client-label">rendez-vous</span>
                                </td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="coach_id" value="<?php echo $coach['id']; ?>">
                                        <button type="submit" name="toggle_coach_status" 
                                                class="status-toggle <?php echo $coach['statut']; ?>">
                                            <?php echo $coach['statut'] === 'actif' ? '✅ Actif' : '❌ Inactif'; ?>
                                        </button>
                                    </form>
                                </td>
                                <td class="actions-cell">
                                    <a href="admin_edit_coach.php?id=<?php echo $coach['id']; ?>" 
                                       class="btn-action edit" title="Modifier">✏️</a>
                                    <a href="admin_xml_generator.php?coach=<?php echo $coach['id']; ?>" 
                                       class="btn-action xml" title="CV XML">📄</a>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="coach_id" value="<?php echo $coach['id']; ?>">
                                        <button type="submit" name="delete_coach" 
                                                class="btn-action delete" title="Supprimer" 
                                                onclick="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer ce coach ?')">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Gestion des clients - Aperçu -->
        <section class="clients-management">
            <div class="container">
                <div class="section-header-admin">
                    <h2>👥 Aperçu des clients (BDD)</h2>
                    <a href="admin_clients.php" class="btn btn-outline">Gérer tous les clients</a>
                </div>

                <div class="clients-grid">
                    <?php foreach ($clients_apercu as $client): ?>
                    <div class="client-card">
                        <div class="client-header">
                            <div class="client-avatar">
                                <img src="https://via.placeholder.com/60x60/007BFF/ffffff?text=<?php echo substr($client['prenom'], 0, 1); ?>" 
                                     alt="<?php echo $client['prenom']; ?>">
                            </div>
                            <div class="client-info">
                                <h4><?php echo htmlspecialchars($client['prenom'] . ' ' . $client['nom']); ?></h4>
                                <p><?php echo htmlspecialchars($client['email']); ?></p>
                            </div>
                        </div>
                        <div class="client-stats">
                            <div class="stat-item">
                                <span class="stat-value"><?php echo $client['nb_rdv']; ?></span>
                                <span class="stat-label">RDV</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value"><?php echo date('d/m/Y', strtotime($client['date_creation'])); ?></span>
                                <span class="stat-label">Inscrit le</span>
                            </div>
                        </div>
                        <div class="client-actions">
                            <a href="admin_client_detail.php?id=<?php echo $client['id']; ?>" 
                               class="btn btn-outline btn-sm">Voir détails</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Configuration de la salle de sport Omnes -->
        <section class="gym-configuration">
            <div class="container">
                <div class="section-header-admin">
                    <h2>🏢 Configuration Salle de Sport Omnes</h2>
                    <a href="admin_salle.php" class="btn btn-primary">Modifier configuration</a>
                </div>

                <div class="gym-config-grid">
                    <div class="config-card">
                        <h3>🕐 Horaires d'ouverture</h3>
                        <div class="config-content">
                            <p><strong>Lun-Ven :</strong> 7h00 - 22h00</p>
                            <p><strong>Sam-Dim :</strong> 8h00 - 20h00</p>
                        </div>
                        <a href="admin_horaires.php" class="btn btn-outline btn-sm">Modifier</a>
                    </div>

                    <div class="config-card">
                        <h3>📞 Coordonnées</h3>
                        <div class="config-content">
                            <p><strong>Téléphone :</strong> +33 1 23 45 67 89</p>
                            <p><strong>Email :</strong> salle@sportify.com</p>
                            <p><strong>Adresse :</strong> 123 Rue du Sport, Paris</p>
                        </div>
                        <a href="admin_contact.php" class="btn btn-outline btn-sm">Modifier</a>
                    </div>

                    <div class="config-card">
                        <h3>⚙️ Services disponibles</h3>
                        <div class="config-content">
                            <ul>
                                <li>✅ Musculation</li>
                                <li>✅ Cardio-training</li>
                                <li>✅ Cours collectifs</li>
                                <li>✅ Vestiaires</li>
                                <li>✅ Parking</li>
                            </ul>
                        </div>
                        <a href="admin_services.php" class="btn btn-outline btn-sm">Modifier</a>
                    </div>

                    <div class="config-card">
                        <h3>💰 Tarification</h3>
                        <div class="config-content">
                            <p><strong>Séance individuelle :</strong> €35</p>
                            <p><strong>Cours collectif :</strong> €25</p>
                            <p><strong>Consultation :</strong> €30</p>
                        </div>
                        <a href="admin_tarifs.php" class="btn btn-outline btn-sm">Modifier</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Activité récente -->
        <section class="recent-activity">
            <div class="container">
                <h2>📋 Activité récente (BDD)</h2>
                <div class="activity-timeline">
                    <?php if (empty($recent_activities)): ?>
                    <div class="activity-item">
                        <div class="activity-icon add">✅</div>
                        <div class="activity-content">
                            <h4>Système opérationnel</h4>
                            <p>Connexion à la base de données réussie</p>
                        </div>
                        <div class="activity-time">
                            Maintenant
                        </div>
                    </div>
                    <?php else: ?>
                    <?php foreach ($recent_activities as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon <?php echo $activity['type']; ?>">
                            <?php 
                            $icons = [
                                'add' => '➕',
                                'edit' => '✏️',
                                'delete' => '🗑️',
                                'xml' => '📄'
                            ];
                            echo $icons[$activity['type']] ?? '📝';
                            ?>
                        </div>
                        <div class="activity-content">
                            <h4><?php echo htmlspecialchars($activity['action']); ?></h4>
                            <p><?php echo htmlspecialchars($activity['details']); ?></p>
                        </div>
                        <div class="activity-time">
                            Il y a <?php echo $activity['heures_ago']; ?>h
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Outils d'administration avancés -->
        <section class="admin-tools">
            <div class="container">
                <h2>🛠️ Outils d'administration</h2>
                <div class="tools-grid">
                    <div class="tool-card">
                        <div class="tool-icon">📄</div>
                        <h3>Générateur CV XML</h3>
                        <p>Créer et gérer les CV XML des coachs avec toutes leurs informations professionnelles</p>
                        <div class="tool-features">
                            <span>✓ Formations</span>
                            <span>✓ Expériences</span>
                            <span>✓ Certifications</span>
                            <span>✓ Export/Import</span>
                        </div>
                        <a href="admin_xml_generator.php" class="btn btn-primary">Accéder</a>
                    </div>

                    <div class="tool-card">
                        <div class="tool-icon">👨‍🏫</div>
                        <h3>Gestion Complète Coachs</h3>
                        <p>Ajouter, modifier, supprimer les coachs et gérer leurs disponibilités</p>
                        <div class="tool-features">
                            <span>✓ Profils complets</span>
                            <span>✓ Photos & vidéos</span>
                            <span>✓ Spécialités</span>
                            <span>✓ Planning</span>
                        </div>
                        <a href="admin_add_coach.php" class="btn btn-primary">Accéder</a>
                    </div>

                    <div class="tool-card">
                        <div class="tool-icon">🏢</div>
                        <h3>Salle de Sport Omnes</h3>
                        <p>Configuration complète des informations de la salle de sport</p>
                        <div class="tool-features">
                            <span>✓ Horaires</span>
                            <span>✓ Services</span>
                            <span>✓ Tarifs</span>
                            <span>✓ Coordonnées</span>
                        </div>
                        <a href="admin_salle.php" class="btn btn-primary">Accéder</a>
                    </div>

                    <div class="tool-card">
                        <div class="tool-icon">📊</div>
                        <h3>Rapports & Statistiques</h3>
                        <p>Analyser les performances de la plateforme et générer des rapports détaillés</p>
                        <div class="tool-features">
                            <span>✓ Revenus</span>
                            <span>✓ Fréquentation</span>
                            <span>✓ Satisfaction</span>
                            <span>✓ Export PDF</span>
                        </div>
                        <a href="admin_reports.php" class="btn btn-primary">Accéder</a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        // Animation des cartes au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card, .action-card, .config-card, .tool-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('animate-fade-in');
                }, index * 50);
            });
        });

        // Confirmation des actions critiques
        document.querySelectorAll('.btn-action.delete, .status-toggle').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (this.classList.contains('delete')) {
                    return; // La confirmation est déjà dans onclick
                } else if (this.classList.contains('inactif')) {
                    if (!confirm('Activer ce coach ?\n\nLe coach pourra à nouveau :\n• Recevoir de nouveaux clients\n• Être visible dans les recherches\n• Gérer son planning')) {
                        e.preventDefault();
                    }
                }
            });
        });
    </script>
</body>
</html>