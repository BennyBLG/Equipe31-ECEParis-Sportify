<?php
session_start();

// Vérification de la connexion client
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_info']['role'] !== 'client') {
    header('Location: votre_compte.php');
    exit;
}

$user = $_SESSION['user_info'];

// Données simulées du client (à remplacer par BDD)
$client_data = [
    'nom' => $user['nom'],
    'prenom' => $user['prenom'],
    'email' => $user['email'],
    'adresse' => $user['adresse'] ?? '123 Rue Test, Paris',
    'carte_etudiant' => $user['carte_etudiant'] ?? 'ETU2025001',
    'telephone' => '+33 6 12 34 56 78',
    'date_inscription' => '2025-01-15',
    'statut_compte' => 'actif'
];

// Rendez-vous à venir
$rdv_a_venir = [
    [
        'id' => 1,
        'coach' => 'Guy DUMAIS',
        'specialite' => 'Musculation',
        'date' => '2025-06-02',
        'heure' => '14:00',
        'duree' => '1h',
        'lieu' => 'Salle Omnes - Studio 1',
        'prix' => 35.00,
        'statut' => 'confirmé'
    ],
    [
        'id' => 2,
        'coach' => 'Marie MARTIN',
        'specialite' => 'Fitness',
        'date' => '2025-06-05',
        'heure' => '16:30',
        'duree' => '45min',
        'lieu' => 'Salle Omnes - Studio 2',
        'prix' => 30.00,
        'statut' => 'confirmé'
    ]
];

// Historique des consultations
$historique = [
    [
        'coach' => 'Paul BERNARD',
        'specialite' => 'Tennis',
        'date' => '2025-05-25',
        'heure' => '15:00',
        'duree' => '1h',
        'prix' => 40.00,
        'statut' => 'terminé',
        'note' => 'Excellent cours ! Très pédagogue.'
    ],
    [
        'coach' => 'Sophie DUBOIS',
        'specialite' => 'Cardio-Training',
        'date' => '2025-05-20',
        'heure' => '10:30',
        'duree' => '30min',
        'prix' => 25.00,
        'statut' => 'terminé',
        'note' => 'Séance intensive, parfait pour débuter.'
    ],
    [
        'coach' => 'Guy DUMAIS',
        'specialite' => 'Musculation',
        'date' => '2025-05-18',
        'heure' => '14:00',
        'duree' => '1h',
        'prix' => 35.00,
        'statut' => 'terminé',
        'note' => 'Programme personnalisé très efficace.'
    ]
];

// Messages non lus
$messages_non_lus = 3;

// Traitement annulation RDV
if (isset($_POST['cancel_rdv']) && isset($_POST['rdv_id'])) {
    $rdv_id = (int)$_POST['rdv_id'];
    // Simulation de l'annulation (en vraie life : UPDATE BDD)
    $success_message = "Rendez-vous annulé avec succès. Le créneau a été libéré.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Espace Client - Sportify</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navigation Client -->
    <div class="client-nav">
        <div class="nav-container">
            <div class="nav-logo">
                <h2><a href="accueil.php">🏋️‍♂️ Sportify</a></h2>
            </div>
            <nav class="nav-menu">
                <ul>
                    <li><a href="dashboard_client.php" class="active">📊 Mon Tableau de Bord</a></li>
                    <li><a href="recherche.php">🔍 Trouver un Coach</a></li>
                    <li><a href="client_reservations.php">📅 Réserver</a></li>
                    <li><a href="client_messages.php">💬 Messages <span class="badge"><?php echo $messages_non_lus; ?></span></a></li>
                </ul>
            </nav>
            <div class="nav-user">
                <div class="user-info">
                    <span class="user-avatar">👤</span>
                    <div class="user-details">
                        <span class="user-name"><?php echo htmlspecialchars($user['prenom']); ?></span>
                        <span class="user-role">Client</span>
                    </div>
                </div>
                <a href="votre_compte.php?logout=1" class="logout-btn">🚪 Déconnexion</a>
            </div>
        </div>
    </div>

    <!-- Contenu Principal -->
    <div class="client-main">
        <!-- Header Dashboard -->
        <div class="dashboard-header">
            <div class="container">
                <h1>👋 Bonjour <?php echo htmlspecialchars($user['prenom']); ?> !</h1>
                <p>Bienvenue dans votre espace personnel Sportify</p>
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

        <!-- Statistiques rapides -->
        <section class="client-stats">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">📅</div>
                        <div class="stat-content">
                            <span class="stat-number"><?php echo count($rdv_a_venir); ?></span>
                            <span class="stat-label">RDV à venir</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">✅</div>
                        <div class="stat-content">
                            <span class="stat-number"><?php echo count($historique); ?></span>
                            <span class="stat-label">Séances terminées</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">💬</div>
                        <div class="stat-content">
                            <span class="stat-number"><?php echo $messages_non_lus; ?></span>
                            <span class="stat-label">Messages non lus</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">💰</div>
                        <div class="stat-content">
                            <span class="stat-number">€<?php echo array_sum(array_column($historique, 'prix')); ?></span>
                            <span class="stat-label">Dépensé ce mois</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Actions rapides -->
        <section class="quick-actions">
            <div class="container">
                <h2>⚡ Actions rapides</h2>
                <div class="actions-grid">
                    <a href="recherche.php" class="action-card">
                        <div class="action-icon">🔍</div>
                        <h3>Trouver un coach</h3>
                        <p>Rechercher par spécialité ou nom</p>
                    </a>
                    
                    <a href="client_reservations.php" class="action-card">
                        <div class="action-icon">📅</div>
                        <h3>Réserver une séance</h3>
                        <p>Choisir un créneau disponible</p>
                    </a>
                    
                    <a href="client_messages.php" class="action-card">
                        <div class="action-icon">💬</div>
                        <h3>Contacter un coach</h3>
                        <p>Messages et visioconférence</p>
                    </a>
                    
                    <a href="client_profil.php" class="action-card">
                        <div class="action-icon">⚙️</div>
                        <h3>Gérer mon profil</h3>
                        <p>Modifier mes informations</p>
                    </a>
                </div>
            </div>
        </section>

        <!-- Rendez-vous à venir -->
        <section class="upcoming-appointments">
            <div class="container">
                <div class="section-header-client">
                    <h2>📅 Mes prochains rendez-vous</h2>
                    <a href="client_reservations.php" class="btn btn-primary">Réserver une séance</a>
                </div>

                <?php if (!empty($rdv_a_venir)): ?>
                    <div class="appointments-grid">
                        <?php foreach ($rdv_a_venir as $rdv): ?>
                        <div class="appointment-card">
                            <div class="appointment-header">
                                <div class="appointment-date">
                                    <span class="date"><?php echo date('d', strtotime($rdv['date'])); ?></span>
                                    <span class="month"><?php echo date('M', strtotime($rdv['date'])); ?></span>
                                </div>
                                <div class="appointment-status confirmed">✅ Confirmé</div>
                            </div>
                            
                            <div class="appointment-content">
                                <h3><?php echo htmlspecialchars($rdv['coach']); ?></h3>
                                <p class="speciality"><?php echo htmlspecialchars($rdv['specialite']); ?></p>
                                
                                <div class="appointment-details">
                                    <div class="detail-item">
                                        <span class="icon">🕐</span>
                                        <span><?php echo $rdv['heure']; ?> (<?php echo $rdv['duree']; ?>)</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="icon">📍</span>
                                        <span><?php echo htmlspecialchars($rdv['lieu']); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="icon">💰</span>
                                        <span>€<?php echo number_format($rdv['prix'], 2); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="appointment-actions">
                                <a href="client_messages.php?coach=<?php echo urlencode($rdv['coach']); ?>" 
                                   class="btn btn-outline btn-sm">💬 Contacter</a>
                                <form method="POST" style="display: inline;" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?')">
                                    <input type="hidden" name="rdv_id" value="<?php echo $rdv['id']; ?>">
                                    <button type="submit" name="cancel_rdv" class="btn btn-danger btn-sm">❌ Annuler</button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-appointments">
                        <div class="no-content-icon">📅</div>
                        <h3>Aucun rendez-vous programmé</h3>
                        <p>Découvrez nos coachs et réservez votre première séance !</p>
                        <a href="recherche.php" class="btn btn-primary">Trouver un coach</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Historique des consultations -->
        <section class="consultation-history">
            <div class="container">
                <h2>📋 Historique de mes consultations</h2>
                
                <div class="history-table">
                    <table class="client-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Coach</th>
                                <th>Spécialité</th>
                                <th>Durée</th>
                                <th>Prix</th>
                                <th>Note/Avis</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historique as $consultation): ?>
                            <tr>
                                <td>
                                    <div class="date-cell">
                                        <span class="date"><?php echo date('d/m/Y', strtotime($consultation['date'])); ?></span>
                                        <span class="time"><?php echo $consultation['heure']; ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="coach-cell">
                                        <img src="media/images/coach-<?php echo strtolower(explode(' ', $consultation['coach'])[0]); ?>.jpg" 
                                             alt="<?php echo $consultation['coach']; ?>"
                                             onerror="this.src='https://via.placeholder.com/40x40/007BFF/ffffff?text=C'"
                                             class="coach-avatar-mini">
                                        <span><?php echo htmlspecialchars($consultation['coach']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($consultation['specialite']); ?></td>
                                <td><?php echo $consultation['duree']; ?></td>
                                <td class="price">€<?php echo number_format($consultation['prix'], 2); ?></td>
                                <td>
                                    <div class="note-cell">
                                        <div class="rating">⭐⭐⭐⭐⭐</div>
                                        <div class="note"><?php echo htmlspecialchars($consultation['note']); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <a href="client_reserver.php?coach=<?php echo urlencode($consultation['coach']); ?>" 
                                       class="btn-action reserver" title="Réserver à nouveau">🔄</a>
                                    <a href="client_messages.php?coach=<?php echo urlencode($consultation['coach']); ?>" 
                                       class="btn-action message" title="Envoyer un message">💬</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Informations du compte -->
        <section class="account-info">
            <div class="container">
                <h2>👤 Informations de mon compte</h2>
                <div class="account-grid">
                    <div class="info-card">
                        <h3>📝 Informations personnelles</h3>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="label">Nom complet :</span>
                                <span class="value"><?php echo htmlspecialchars($client_data['prenom'] . ' ' . $client_data['nom']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Email :</span>
                                <span class="value"><?php echo htmlspecialchars($client_data['email']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Téléphone :</span>
                                <span class="value"><?php echo htmlspecialchars($client_data['telephone']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Adresse :</span>
                                <span class="value"><?php echo htmlspecialchars($client_data['adresse']); ?></span>
                            </div>
                        </div>
                        <a href="client_profil.php" class="btn btn-outline">✏️ Modifier</a>
                    </div>

                    <div class="info-card">
                        <h3>🎓 Informations étudiantes</h3>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="label">Carte étudiante :</span>
                                <span class="value"><?php echo htmlspecialchars($client_data['carte_etudiant']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Date d'inscription :</span>
                                <span class="value"><?php echo date('d/m/Y', strtotime($client_data['date_inscription'])); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Statut :</span>
                                <span class="value status-active">✅ Compte actif</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-card">
                        <h3>💳 Informations de paiement</h3>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="label">Carte enregistrée :</span>
                                <span class="value">**** **** **** 1234</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Type :</span>
                                <span class="value">Visa</span>
                            </div>
                        </div>
                        <a href="client_paiement.php" class="btn btn-outline">💳 Gérer</a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        // Animation au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card, .action-card, .appointment-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('animate-fade-in');
                }, index * 100);
            });
        });

        // Confirmation d'annulation plus jolie
        document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (confirm('⚠️ Confirmer l\'annulation ?\n\n• Le créneau sera libéré\n• Vous recevrez un email de confirmation\n• Aucun frais d\'annulation (>24h avant)')) {
                    this.submit();
                }
            });
        });

        // Mise à jour automatique des badges de messages
        setInterval(function() {
            const badge = document.querySelector('.badge');
            if (badge && Math.random() < 0.1) {
                let count = parseInt(badge.textContent) || 0;
                if (count < 10) {
                    badge.textContent = count + 1;
                    badge.style.animation = 'pulse 1s ease-out';
                }
            }
        }, 15000);
    </script>
</body>
</html>