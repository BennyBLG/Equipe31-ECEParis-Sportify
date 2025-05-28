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
$success_message = '';
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

        <?php if (!empty($success_message)): ?>
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
                    <h2>👥 Aperçu des clients</h2>
                    <a href="admin_clients.php" class="btn btn-outline">Gérer tous les clients</a>
                </div>

                <div class="clients-grid">
                    <?php foreach ($clients_data as $client): ?>
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
                                <span class="stat-value"><?php echo date('d/m/Y', strtotime($client['date_inscription'])); ?></span>
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

        <!-- Configuration de la salle de sport -->
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
                <h2>📋 Activité récente</h2>
                <div class="activity-timeline">
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
                            Il y a <?php echo htmlspecialchars($activity['time']); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="activity-actions">
                    <a href="admin_activity_log.php" class="btn btn-outline">Voir tout l'historique</a>
                    <a href="admin_export_log.php" class="btn btn-outline">Exporter les logs</a>
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

                    <div class="tool-card">
                        <div class="tool-icon">💾</div>
                        <h3>Sauvegarde & Restauration</h3>
                        <p>Gérer les sauvegardes automatiques et manuelles de toutes les données</p>
                        <div class="tool-features">
                            <span>✓ Sauvegarde auto</span>
                            <span>✓ Export BDD</span>
                            <span>✓ Restauration</span>
                            <span>✓ Planification</span>
                        </div>
                        <a href="admin_backup.php" class="btn btn-primary">Accéder</a>
                    </div>

                    <div class="tool-card">
                        <div class="tool-icon">⚙️</div>
                        <h3>Configuration Système</h3>
                        <p>Paramètres avancés de la plateforme et configuration des fonctionnalités</p>
                        <div class="tool-features">
                            <span>✓ Emails auto</span>
                            <span>✓ Notifications</span>
                            <span>✓ Sécurité</span>
                            <span>✓ Performances</span>
                        </div>
                        <a href="admin_settings.php" class="btn btn-primary">Accéder</a>
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

        // Mise à jour automatique des statistiques
        function updateStats() {
            const rdvElement = document.querySelector('.stat-card:nth-child(3) .stat-number');
            if (rdvElement && Math.random() < 0.1) {
                let currentValue = parseInt(rdvElement.textContent);
                rdvElement.textContent = currentValue + 1;
                rdvElement.style.color = '#28a745';
                setTimeout(() => {
                    rdvElement.style.color = '';
                }, 2000);
            }
        }

        setInterval(updateStats, 30000); // Toutes les 30 secondes

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

