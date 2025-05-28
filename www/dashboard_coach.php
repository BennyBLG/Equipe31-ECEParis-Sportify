<?php
session_start();

// Vérification de la connexion coach
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_info']['role'] !== 'coach') {
    header('Location: votre_compte.php');
    exit;
}

$user = $_SESSION['user_info'];

// Données du coach
$coach_data = [
    'nom' => $user['nom'],
    'prenom' => $user['prenom'],
    'email' => $user['email'],
    'specialite' => $user['specialite'],
    'telephone' => '+33 6 98 76 54 32',
    'bureau' => 'Bureau 12, Bâtiment A',
    'experience' => '5 ans',
    'certifications' => ['BPJEPS', 'CQP ALS'],
    'note_moyenne' => 4.8,
    'nb_evaluations' => 127
];

// Consultations du jour
$consultations_jour = [
    [
        'id' => 1,
        'heure' => '09:00',
        'duree' => '1h',
        'client' => 'Jean DUPONT',
        'email_client' => 'jean.dupont@email.com',
        'type' => 'Séance individuelle',
        'statut' => 'confirmé',
        'salle' => 'Studio 1'
    ],
    [
        'id' => 2,
        'heure' => '14:00',
        'duree' => '45min',
        'client' => 'Marie MARTIN',
        'email_client' => 'marie.martin@email.com',
        'type' => 'Consultation',
        'statut' => 'confirmé',
        'salle' => 'Bureau 12'
    ],
    [
        'id' => 3,
        'heure' => '16:30',
        'duree' => '1h',
        'client' => 'Paul BERNARD',
        'email_client' => 'paul.bernard@email.com',
        'type' => 'Séance groupe',
        'statut' => 'en_attente',
        'salle' => 'Studio 2'
    ]
];

// Consultations à venir (cette semaine)
$consultations_semaine = [
    ['date' => '2025-06-02', 'nb_rdv' => 3, 'heures' => '9h-17h'],
    ['date' => '2025-06-03', 'nb_rdv' => 5, 'heures' => '8h-18h'],
    ['date' => '2025-06-04', 'nb_rdv' => 2, 'heures' => '14h-16h'],
    ['date' => '2025-06-05', 'nb_rdv' => 4, 'heures' => '10h-17h'],
    ['date' => '2025-06-06', 'nb_rdv' => 1, 'heures' => '15h-16h']
];

// Messages clients
$messages_recents = [
    [
        'client' => 'Jean DUPONT',
        'message' => 'Bonjour, je souhaiterais modifier mon RDV de demain...',
        'heure' => '14:30',
        'non_lu' => true
    ],
    [
        'client' => 'Sophie DURAND',
        'message' => 'Merci pour la séance d\'hier, très enrichissante !',
        'heure' => '12:15',
        'non_lu' => true
    ],
    [
        'client' => 'Marie MARTIN',
        'message' => 'Pouvez-vous me donner des conseils pour...',
        'heure' => '10:45',
        'non_lu' => false
    ]
];

$messages_non_lus = count(array_filter($messages_recents, fn($m) => $m['non_lu']));

// Disponibilités de la semaine (simulation)
$disponibilites = [
    'lundi' => ['09:00-12:00', '14:00-18:00'],
    'mardi' => ['08:00-12:00', '13:00-17:00'],
    'mercredi' => ['10:00-16:00'],
    'jeudi' => ['09:00-12:00', '14:00-19:00'],
    'vendredi' => ['08:00-15:00'],
    'samedi' => ['09:00-13:00'],
    'dimanche' => []
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Coach - Sportify</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navigation Coach -->
    <div class="coach-nav">
        <div class="nav-container">
            <div class="nav-logo">
                <h2><a href="accueil.php">🏋️‍♂️ Sportify Coach</a></h2>
            </div>
            <nav class="nav-menu">
                <ul>
                    <li><a href="dashboard_coach.php" class="active">📊 Mon Tableau de Bord</a></li>
                    <li><a href="coach_planning.php">📅 Planning</a></li>
                    <li><a href="coach_clients.php">👥 Mes Clients</a></li>
                    <li><a href="coach_messages.php">💬 Messages <span class="badge"><?php echo $messages_non_lus; ?></span></a></li>
                    <li><a href="coach_profil.php">⚙️ Mon Profil</a></li>
                </ul>
            </nav>
            <div class="nav-user">
                <div class="user-info">
                    <span class="user-avatar">🏋️‍♂️</span>
                    <div class="user-details">
                        <span class="user-name"><?php echo htmlspecialchars($user['prenom']); ?></span>
                        <span class="user-role">Coach <?php echo htmlspecialchars($user['specialite']); ?></span>
                    </div>
            </div>
        </section>

        <!-- Actions rapides -->
        <section class="quick-actions">
            <div class="container">
                <h2>⚡ Actions rapides</h2>
                <div class="actions-grid">
                    <a href="coach_planning.php" class="action-card">
                        <div class="action-icon">📅</div>
                        <h3>Gérer mon planning</h3>
                        <p>Modifier mes disponibilités</p>
                    </a>
                    
                    <a href="coach_clients.php" class="action-card">
                        <div class="action-icon">👥</div>
                        <h3>Voir mes clients</h3>
                        <p>Historique et dossiers clients</p>
                    </a>
                    
                    <a href="coach_messages.php" class="action-card">
                        <div class="action-icon">💬</div>
                        <h3>Répondre aux messages</h3>
                        <p>Communication avec les clients</p>
                    </a>
                    
                    <a href="coach_profil.php" class="action-card">
                        <div class="action-icon">⚙️</div>
                        <h3>Mettre à jour mon profil</h3>
                        <p>CV, photos, spécialités</p>
                    </a>
                </div>
            </div>
        </section>

        <!-- Consultations du jour -->
        <section class="today-consultations">
            <div class="container">
                <div class="section-header-coach">
                    <h2>📅 Mes consultations d'aujourd'hui</h2>
                    <span class="date-today"><?php echo date('d/m/Y'); ?></span>
                </div>

                <?php if (!empty($consultations_jour)): ?>
                    <div class="consultations-timeline">
                        <?php foreach ($consultations_jour as $consultation): ?>
                        <div class="consultation-item <?php echo $consultation['statut']; ?>">
                            <div class="consultation-time">
                                <span class="time"><?php echo $consultation['heure']; ?></span>
                                <span class="duration"><?php echo $consultation['duree']; ?></span>
                            </div>
                            
                            <div class="consultation-content">
                                <div class="consultation-header">
                                    <h3><?php echo htmlspecialchars($consultation['client']); ?></h3>
                                    <span class="status-badge <?php echo $consultation['statut']; ?>">
                                        <?php echo $consultation['statut'] === 'confirmé' ? '✅ Confirmé' : '⏳ En attente'; ?>
                                    </span>
                                </div>
                                
                                <div class="consultation-details">
                                    <p><strong>Type :</strong> <?php echo htmlspecialchars($consultation['type']); ?></p>
                                    <p><strong>Lieu :</strong> <?php echo htmlspecialchars($consultation['salle']); ?></p>
                                    <p><strong>Email :</strong> <?php echo htmlspecialchars($consultation['email_client']); ?></p>
                                </div>
                            </div>
                            
                            <div class="consultation-actions">
                                <a href="coach_messages.php?client=<?php echo urlencode($consultation['client']); ?>" 
                                   class="btn btn-outline btn-sm">💬 Contacter</a>
                                <a href="coach_consultation.php?id=<?php echo $consultation['id']; ?>" 
                                   class="btn btn-primary btn-sm">📝 Détails</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-consultations">
                        <div class="no-content-icon">😴</div>
                        <h3>Aucune consultation aujourd'hui</h3>
                        <p>Profitez de cette journée libre pour vous reposer !</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Planning de la semaine -->
        <section class="week-planning">
            <div class="container">
                <h2>📊 Planning de la semaine</h2>
                <div class="week-overview">
                    <?php 
                    $jours = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
                    foreach ($consultations_semaine as $index => $jour): 
                    ?>
                    <div class="day-card <?php echo $jour['nb_rdv'] > 4 ? 'busy' : ($jour['nb_rdv'] > 0 ? 'active' : 'free'); ?>">
                        <div class="day-header">
                            <span class="day-name"><?php echo $jours[$index]; ?></span>
                            <span class="day-date"><?php echo date('d/m', strtotime($jour['date'])); ?></span>
                        </div>
                        <div class="day-content">
                            <span class="rdv-count"><?php echo $jour['nb_rdv']; ?> RDV</span>
                            <span class="time-range"><?php echo $jour['heures']; ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Messages récents -->
        <section class="recent-messages">
            <div class="container">
                <div class="section-header-coach">
                    <h2>💬 Messages récents</h2>
                    <a href="coach_messages.php" class="btn btn-primary">Voir tous les messages</a>
                </div>

                <div class="messages-list">
                    <?php foreach ($messages_recents as $message): ?>
                    <div class="message-item <?php echo $message['non_lu'] ? 'unread' : ''; ?>">
                        <div class="message-avatar">
                            <img src="https://via.placeholder.com/50x50/007BFF/ffffff?text=<?php echo substr($message['client'], 0, 1); ?>" 
                                 alt="<?php echo $message['client']; ?>">
                            <?php if ($message['non_lu']): ?>
                                <span class="unread-indicator">●</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="message-content">
                            <div class="message-header">
                                <h4><?php echo htmlspecialchars($message['client']); ?></h4>
                                <span class="message-time"><?php echo $message['heure']; ?></span>
                            </div>
                            <p class="message-preview"><?php echo htmlspecialchars($message['message']); ?></p>
                        </div>
                        
                        <div class="message-actions">
                            <a href="coach_messages.php?client=<?php echo urlencode($message['client']); ?>" 
                               class="btn btn-outline btn-sm">Répondre</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Disponibilités -->
        <section class="availability-overview">
            <div class="container">
                <div class="section-header-coach">
                    <h2>🕐 Mes disponibilités</h2>
                    <a href="coach_planning.php" class="btn btn-primary">Modifier</a>
                </div>

                <div class="availability-grid">
                    <?php 
                    $jours_complets = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                    $jours_keys = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
                    
                    foreach ($jours_keys as $index => $jour_key): 
                        $creneaux = $disponibilites[$jour_key];
                    ?>
                    <div class="availability-day <?php echo empty($creneaux) ? 'unavailable' : 'available'; ?>">
                        <h4><?php echo $jours_complets[$index]; ?></h4>
                        <?php if (!empty($creneaux)): ?>
                            <?php foreach ($creneaux as $creneau): ?>
                                <span class="time-slot"><?php echo $creneau; ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="unavailable-text">Indisponible</span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Informations du profil coach -->
        <section class="coach-profile-info">
            <div class="container">
                <h2>👤 Mon profil coach</h2>
                <div class="profile-grid">
                    <div class="profile-card">
                        <h3>📝 Informations personnelles</h3>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="label">Nom complet :</span>
                                <span class="value"><?php echo htmlspecialchars($coach_data['prenom'] . ' ' . $coach_data['nom']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Spécialité :</span>
                                <span class="value"><?php echo htmlspecialchars($coach_data['specialite']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Email :</span>
                                <span class="value"><?php echo htmlspecialchars($coach_data['email']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Bureau :</span>
                                <span class="value"><?php echo htmlspecialchars($coach_data['bureau']); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="profile-card">
                        <h3>🏆 Expérience & Certifications</h3>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="label">Expérience :</span>
                                <span class="value"><?php echo htmlspecialchars($coach_data['experience']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Certifications :</span>
                                <span class="value"><?php echo implode(', ', $coach_data['certifications']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Note moyenne :</span>
                                <span class="value"><?php echo $coach_data['note_moyenne']; ?>/5 ⭐</span>
                            </div>
                        </div>
                        <a href="coach_profil.php" class="btn btn-outline">✏️ Modifier mon profil</a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        // Animation au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card, .action-card, .consultation-item');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('animate-fade-in');
                }, index * 100);
            });
        });

        // Mise à jour en temps réel des consultations
        function updateConsultationStatus() {
            const now = new Date();
            const currentTime = now.getHours() * 60 + now.getMinutes();
            
            document.querySelectorAll('.consultation-item').forEach(item => {
                const timeElement = item.querySelector('.time');
                const timeText = timeElement.textContent;
                const [hours, minutes] = timeText.split(':').map(Number);
                const consultationTime = hours * 60 + minutes;
                
                // Marquer comme "en cours" si c'est l'heure
                if (Math.abs(currentTime - consultationTime) < 30) {
                    item.classList.add('current');
                    item.querySelector('.status-badge').innerHTML = '🔴 En cours';
                }
            });
        }

        // Vérifier toutes les minutes
        setInterval(updateConsultationStatus, 60000);
        updateConsultationStatus(); // Première vérification

        // Notification sonore pour nouveaux messages
        let lastMessageCount = <?php echo $messages_non_lus; ?>;
        setInterval(function() {
            // Simulation de nouveaux messages
            if (Math.random() < 0.05) { // 5% de chance
                const badge = document.querySelector('.badge');
                if (badge) {
                    let count = parseInt(badge.textContent) || 0;
                    if (count < lastMessageCount + 3) {
                        badge.textContent = count + 1;
                        badge.style.animation = 'pulse 1s ease-out';
                        
                        // Son de notification (optionnel)
                        // new Audio('notification.mp3').play();
                    }
                }
            }
        }, 10000);
    </script>
</body>
</html>>
                </div>
                <a href="votre_compte.php?logout=1" class="logout-btn">🚪 Déconnexion</a>
            </div>
        </div>
    </div>

    <!-- Contenu Principal -->
    <div class="coach-main">
        <!-- Header Dashboard -->
        <div class="dashboard-header">
            <div class="container">
                <h1>👋 Bonjour Coach <?php echo htmlspecialchars($user['prenom']); ?> !</h1>
                <p>Gérez vos consultations et restez en contact avec vos clients</p>
                <div class="coach-rating">
                    <span class="rating-stars">⭐⭐⭐⭐⭐</span>
                    <span class="rating-text"><?php echo $coach_data['note_moyenne']; ?>/5 (<?php echo $coach_data['nb_evaluations']; ?> avis)</span>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <section class="coach-stats">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">📅</div>
                        <div class="stat-content">
                            <span class="stat-number"><?php echo count($consultations_jour); ?></span>
                            <span class="stat-label">Consultations aujourd'hui</span>
                            <span class="stat-sublabel">Prochaine à <?php echo $consultations_jour[0]['heure']; ?></span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">📊</div>
                        <div class="stat-content">
                            <span class="stat-number"><?php echo array_sum(array_column($consultations_semaine, 'nb_rdv')); ?></span>
                            <span class="stat-label">RDV cette semaine</span>
                            <span class="stat-sublabel">Répartis sur 5 jours</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">💬</div>
                        <div class="stat-content">
                            <span class="stat-number"><?php echo $messages_non_lus; ?></span>
                            <span class="stat-label">Messages non lus</span>
                            <span class="stat-sublabel">De vos clients</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">⭐</div>
                        <div class="stat-content">
                            <span class="stat-number"><?php echo $coach_data['note_moyenne']; ?></span>
                            <span class="stat-label">Note moyenne</span>
                            <span class="stat-sublabel"><?php echo $coach_data['nb_evaluations']; ?> évaluations</span>
                        </div>
                    </div>
                </div