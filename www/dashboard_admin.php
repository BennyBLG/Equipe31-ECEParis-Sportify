<?php
session_start();

// Vérification de la connexion admin
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_info']['role'] !== 'admin') {
    header('Location: votre_compte.php');
    exit;
}

$user = $_SESSION['user_info'];

// Données simulées de l'administration (à remplacer par BDD)
$coachs_data = [
    ['id' => 1, 'nom' => 'DUMAIS', 'prenom' => 'Guy', 'specialite' => 'Musculation', 'statut' => 'actif', 'email' => 'guy.dumais@sportify.com', 'nb_clients' => 15],
    ['id' => 2, 'nom' => 'MARTIN', 'prenom' => 'Marie', 'specialite' => 'Fitness', 'statut' => 'actif', 'email' => 'marie.martin@sportify.com', 'nb_clients' => 22],
    ['id' => 3, 'nom' => 'BERNARD', 'prenom' => 'Paul', 'specialite' => 'Tennis', 'statut' => 'actif', 'email' => 'paul.bernard@sportify.com', 'nb_clients' => 18],
    ['id' => 4, 'nom' => 'DUBOIS', 'prenom' => 'Sophie', 'specialite' => 'Cardio-Training', 'statut' => 'inactif', 'email' => 'sophie.dubois@sportify.com', 'nb_clients' => 8],
    ['id' => 5, 'nom' => 'MOREAU', 'prenom' => 'Jean', 'specialite' => 'Cours Collectifs', 'statut' => 'actif', 'email' => 'jean.moreau@sportify.com', 'nb_clients' => 35],
];

$clients_data = [
    ['id' => 1, 'nom' => 'DUPONT', 'prenom' => 'Jean', 'email' => 'jean.dupont@email.com', 'date_inscription' => '2025-01-15', 'nb_rdv' => 8],
    ['id' => 2, 'nom' => 'MARTIN', 'prenom' => 'Marie', 'email' => 'marie.martin@email.com', 'date_inscription' => '2025-02-03', 'nb_rdv' => 12],
    ['id' => 3, 'nom' => 'BERNARD', 'prenom' => 'Paul', 'email' => 'paul.bernard@email.com', 'date_inscription' => '2025-01-28', 'nb_rdv' => 5],
];

// Statistiques générales
$stats = [
    'total_coachs' => count($coachs_data),
    'coachs_actifs' => count(array_filter($coachs_data, fn($c) => $c['statut'] === 'actif')),
    'total_clients' => count($clients_data),
    'rdv_semaine' => 47,
    'rdv_mois' => 186,
    'revenus_mois' => 5240.00,
    'nouveaux_clients' => 12
];

// Activités récentes
$recent_activities = [
    ['action' => 'Nouveau coach ajouté', 'details' => 'Thomas COLIN - Plongeon', 'time' => '2 heures', 'type' => 'add'],
    ['action' => 'CV XML généré', 'details' => 'Guy DUMAIS - Musculation', 'time' => '3 heures', 'type' => 'xml'],
    ['action' => 'Horaires modifiés', 'details' => 'Salle de sport Omnes', 'time' => '5 heures', 'type' => 'edit'],
    ['action' => 'Coach supprimé', 'details' => 'Anna DURAND - Tennis', 'time' => '1 jour', 'type' => 'delete'],
    ['action' => 'Nouveau client inscrit', 'details' => 'Marc BERNARD', 'time' => '1 jour', 'type' => 'add'],
];

// Alertes système
$system_alerts = [
    ['type' => 'warning', 'message' => 'Coach Sophie DUBOIS inactive depuis 15 jours'],
    ['type' => 'info', 'message' => '3 nouveaux avis clients à modérer'],
    ['type' => 'success', 'message' => 'Sauvegarde automatique effectuée avec succès']
];

// Traitement des actions admin
if ($_POST) {
    if (isset($_POST['toggle_coach_status'])) {
        $coach_id = (int)$_POST['coach_id'];
        // Simulation de changement de statut
        $success_message = "Statut du coach modifié avec succès.";
    }
    
    if (isset($_POST['delete_coach'])) {
        $coach_id = (int)$_POST['coach_id'];
        // Simulation de suppression
        $success_message = "Coach supprimé avec succès.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Sportify</title>
    <link rel="stylesheet" href="style.css">
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
                    <li><a href="admin_coachs.php">👨‍🏫 Gestion Coachs</a></li>
                    <li><a href="admin_clients.php">👥 Gestion Clients</a></li>
                    <li><a href="admin_xml.php">📄 CV XML</a></li>
                    <li><a href="admin_salle.php">🏢 Salle de Sport</a></li>
                    <li><a href="admin_stats.php">📈 Statistiques</a></li>
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
                <p>Bienvenue <?php echo htmlspecialchars($user['prenom']); ?>, voici un aperçu de votre plateforme Sportify</p>
            </div>
        </div>

        <?php if (isset($success_message)): ?>
        <div class="container">
            <div class="alert alert-success">
                <span class="alert-icon">✅</span>
                <?php echo $success_message; ?>
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
                                ($alert['type'] === 'info' ? 'ℹ️' : '✅');
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
                <h2>📈 Statistiques générales</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">👨‍🏫</div>
                        <div class="stat-content">
                            <span class="stat-number"><?php echo $stats['total_coachs']; ?></span>
                            <span class="stat-label">Coachs total</span>
                            <span class="stat-sublabel"><?php echo $stats['coachs_actifs']; ?> actifs</span>
                        </div>
                        <div class="stat-trend positive">↗️ +2 ce mois</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">👥</div>
                        <div class="stat-content">
                            <span class="stat-number"><?php echo $stats['total_clients']; ?></span>
                            <span class="stat-label">Clients inscrits</span>
                            <span class="stat-sublabel"><?php echo $stats['nouveaux_clients']; ?> nouveaux</span>
                        </div>
                        <div class="stat-trend positive">↗️ +15%</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">📅</div>
                        <div class="stat-content">
                            <span class="stat-number"><?php echo $stats['rdv_semaine']; ?></span>
                            <span class="stat-label">RDV cette semaine</span>
                            <span class="stat-sublabel"><?php echo $stats['rdv_mois']; ?> ce mois</span>
                        </div>
                        <div class="stat-trend positive">↗️ +8%</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">💰</div>
                        <div class="stat-content">
                            <span class="stat-number">€<?php echo number_format($stats['revenus_mois'], 0); ?></span>
                            <span class="stat-label">Revenus ce mois</span>
                            <span class="stat-sublabel">Moyenne €28/RDV</span>
                        </div>
                        <div class="stat-trend positive">↗️ +12%</div>
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
                        <p>Créer un nouveau profil de coach</p>
                    </a>

                    <a href="admin_xml_generator.php" class="action-card">
                        <div class="action-icon">📄</div>
                        <h3>Générer CV XML</h3>
                        <p>Créer un CV au format XML</p>
                    </a>

                    <a href="admin_salle.php" class="action-card">
                        <div class="action-icon">🏢</div>
                        <h3>Gérer la salle</h3>
                        <p>Modifier infos salle de sport</p>
                    </a>

                    <a href="admin_backup.php" class="action-card">
                        <div class="action-icon">💾</div>
                        <h3>Sauvegarde</h3>
                        <p>Exporter les données</p>
                    </a>
                </div>
            </div>
        </section>

        <!-- Gestion des coachs - Aperçu -->
        <section class="coaches-management">
            <div class="container">
                <div class="section-header-admin">
                    <h2>👨‍🏫 Gestion des coachs</h2>
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
                                <th>Clients</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($coachs_data, 0, 5) as $coach): ?>
                            <tr>
                                <td class="coach-info">
                                    <div class="coach-avatar-small">
                                        <img src="media/images/coach-<?php echo strtolower($coach['prenom']); ?>.jpg" 
                                             alt="<?php echo $coach['prenom']; ?>"
                                             onerror="this.src='https://via.placeholder.com/40x40/007BFF/ffffff?text=<?php echo substr($coach['prenom'], 0, 1); ?>'">
                                    </div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($coach['prenom'] . ' ' . $coach['nom']); ?></strong>
                                        <div class="coach-email"><?php echo htmlspecialchars($coach['email']); ?></div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($coach['specialite']); ?></td>
                                <td>
                                    <span class="client-count"><?php echo $coach['nb_clients']; ?></span>
                                    <span class="client-label">clients</span>
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
                                    <form method="POST" style="display: inline;" 
                                          onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer ce coach ?\n\nCette action supprimera :\n- Le profil du coach\n- Son CV XML\n- Tous ses rendez-vous\n- Son historique')">
                                        <input type="hidden" name="coach_id" value="<?php echo $coach['id']; ?>">