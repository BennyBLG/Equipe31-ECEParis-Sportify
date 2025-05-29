<?php
session_start();

// Vérification si l'utilisateur est connecté (optionnel pour cette démonstration)
$user_connected = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
$user_info = $user_connected ? $_SESSION['user_info'] : null;

// Données simulées des coachs avec leurs disponibilités
$coachs_disponibles = [
    [
        'id' => 1,
        'nom' => 'DUMAIS',
        'prenom' => 'Guy',
        'specialite' => 'Musculation',
        'photo' => 'coach-guy.jpg',
        'prix' => 35,
        'duree' => 60,
        'note' => 4.8,
        'disponibilites' => [
            '2025-06-02' => ['09:00', '10:00', '14:00', '15:00', '16:00'],
            '2025-06-03' => ['09:00', '10:00', '11:00', '15:00', '16:00'],
            '2025-06-04' => ['14:00', '15:00', '16:00', '17:00'],
            '2025-06-05' => ['09:00', '10:00', '14:00', '15:00'],
            '2025-06-06' => ['10:00', '11:00', '15:00', '16:00']
        ]
    ],
    [
        'id' => 2,
        'nom' => 'MARTIN',
        'prenom' => 'Marie',
        'specialite' => 'Fitness',
        'photo' => 'coach-marie.jpg',
        'prix' => 30,
        'duree' => 45,
        'note' => 4.6,
        'disponibilites' => [
            '2025-06-02' => ['08:00', '09:00', '16:30', '17:30'],
            '2025-06-03' => ['08:00', '09:00', '10:00', '16:00', '17:00'],
            '2025-06-04' => ['08:00', '09:00', '16:00', '17:00'],
            '2025-06-05' => ['09:00', '10:00', '16:30', '17:30'],
            '2025-06-06' => ['08:00', '09:00', '10:00']
        ]
    ],
    [
        'id' => 3,
        'nom' => 'BERNARD',
        'prenom' => 'Paul',
        'specialite' => 'Tennis',
        'photo' => 'coach-paul.jpg',
        'prix' => 40,
        'duree' => 60,
        'note' => 4.9,
        'disponibilites' => [
            '2025-06-02' => ['15:00', '16:00', '17:00'],
            '2025-06-03' => ['14:00', '15:00', '16:00', '17:00'],
            '2025-06-04' => ['15:00', '16:00'],
            '2025-06-05' => ['14:00', '15:00', '16:00', '17:00'],
            '2025-06-06' => ['15:00', '16:00']
        ]
    ]
];

// Rendez-vous existants (simulation)
$rdv_existants = [];
if ($user_connected && $user_info['role'] == 'client') {
    $rdv_existants = [
        [
            'id' => 1,
            'coach_id' => 1,
            'coach_nom' => 'Guy DUMAIS',
            'specialite' => 'Musculation',
            'date' => '2025-06-02',
            'heure' => '14:00',
            'duree' => 60,
            'prix' => 35,
            'statut' => 'confirmé',
            'lieu' => 'Salle Omnes - Studio 1'
        ],
        [
            'id' => 2,
            'coach_id' => 2,
            'coach_nom' => 'Marie MARTIN',
            'specialite' => 'Fitness',
            'date' => '2025-06-05',
            'heure' => '16:30',
            'duree' => 45,
            'prix' => 30,
            'statut' => 'confirmé',
            'lieu' => 'Salle Omnes - Studio 2'
        ]
    ];
}

// Traitement des actions (réservation, annulation)
$message_success = '';
$message_error = '';

if ($_POST) {
    if (isset($_POST['reserver'])) {
        // Simulation de réservation
        $coach_id = (int)$_POST['coach_id'];
        $date = $_POST['date'];
        $heure = $_POST['heure'];
        
        if ($user_connected) {
            $message_success = "✅ Rendez-vous réservé avec succès pour le $date à $heure !";
        } else {
            $message_error = "❌ Vous devez être connecté pour réserver un rendez-vous.";
        }
    }
    
    if (isset($_POST['annuler'])) {
        // Simulation d'annulation
        $rdv_id = (int)$_POST['rdv_id'];
        $message_success = "✅ Rendez-vous annulé avec succès. Le créneau a été libéré.";
    }
}

// Paramètres URL pour pré-sélection
$coach_preselectionne = isset($_GET['coach']) ? (int)$_GET['coach'] : null;
$service_preselectionne = isset($_GET['service']) ? (int)$_GET['service'] : null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendez-vous - Sportify</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .rdv-tabs {
            display: flex;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 5px;
            margin: 20px 0;
        }
        .rdv-tab {
            flex: 1;
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            background: transparent;
            font-size: 16px;
            font-weight: 600;
        }
        .rdv-tab.active {
            background: #007BFF;
            color: white;
        }
        .rdv-tab:hover:not(.active) {
            background: #e9ecef;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .coach-selection {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .coach-card-rdv {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 20px;
            transition: all 0.3s;
            cursor: pointer;
        }
        .coach-card-rdv:hover {
            border-color: #007BFF;
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,123,255,0.1);
        }
        .coach-card-rdv.selected {
            border-color: #007BFF;
            background: #f0f8ff;
        }
        .coach-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        .coach-avatar-rdv {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
        }
        .coach-info-rdv h3 {
            margin: 0 0 5px 0;
            color: #333;
        }
        .coach-info-rdv p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        .price-info {
            background: #28a745;
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin: 10px 0;
        }
        .calendar-container {
            margin: 20px 0;
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
            margin: 20px 0;
        }
        .calendar-day {
            text-align: center;
            padding: 15px 10px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .calendar-day:hover {
            border-color: #007BFF;
        }
        .calendar-day.selected {
            background: #007BFF;
            color: white;
            border-color: #007BFF;
        }
        .calendar-day.disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }
        .time-slots {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 10px;
            margin: 20px 0;
        }
        .time-slot {
            padding: 12px;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }
        .time-slot:hover {
            border-color: #007BFF;
            background: #f0f8ff;
        }
        .time-slot.selected {
            background: #007BFF;
            color: white;
            border-color: #007BFF;
        }
        .time-slot.disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }
        .rdv-existing {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .rdv-info h4 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .rdv-details {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .rdv-detail {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            color: #666;
        }
        .rdv-actions {
            display: flex;
            gap: 10px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        .booking-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #007BFF;
        }
        .booking-step {
            margin: 15px 0;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        .step-number {
            background: #007BFF;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
        }
        .step-completed {
            background: #28a745;
        }
        .no-rdv {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .no-rdv-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
    </style>
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
            <h1>📅 Rendez-vous Sportifs</h1>
            <p class="hero-subtitle">Gérez vos consultations et réservez de nouveaux créneaux</p>
        </div>
    </section>

    <!-- Messages -->
    <div class="container">
        <?php if ($message_success): ?>
            <div class="alert alert-success">
                <span class="alert-icon">✅</span>
                <?php echo $message_success; ?>
            </div>
        <?php endif; ?>

        <?php if ($message_error): ?>
            <div class="alert alert-error">
                <span class="alert-icon">❌</span>
                <?php echo $message_error; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Onglets principaux -->
    <section class="rdv-management">
        <div class="container">
            <div class="rdv-tabs">
                <button class="rdv-tab active" onclick="showTab('mes-rdv')">
                    📋 Mes Rendez-vous
                </button>
                <button class="rdv-tab" onclick="showTab('nouveau-rdv')">
                    ➕ Nouveau Rendez-vous
                </button>
                <button class="rdv-tab" onclick="showTab('planning')">
                    📊 Planning Général
                </button>
            </div>

            <!-- Onglet Mes Rendez-vous -->
            <div id="mes-rdv" class="tab-content active">
                <h2>📋 Mes Rendez-vous Confirmés</h2>
                
                <?php if ($user_connected && !empty($rdv_existants)): ?>
                    <?php foreach ($rdv_existants as $rdv): ?>
                    <div class="rdv-existing">
                        <div class="rdv-info">
                            <h4>🏋️‍♂️ <?php echo htmlspecialchars($rdv['coach_nom']); ?></h4>
                            <div class="rdv-details">
                                <div class="rdv-detail">
                                    <span>🎯</span>
                                    <span><?php echo htmlspecialchars($rdv['specialite']); ?></span>
                                </div>
                                <div class="rdv-detail">
                                    <span>📅</span>
                                    <span><?php echo date('d/m/Y', strtotime($rdv['date'])); ?></span>
                                </div>
                                <div class="rdv-detail">
                                    <span>🕐</span>
                                    <span><?php echo $rdv['heure']; ?> (<?php echo $rdv['duree']; ?>min)</span>
                                </div>
                                <div class="rdv-detail">
                                    <span>📍</span>
                                    <span><?php echo htmlspecialchars($rdv['lieu']); ?></span>
                                </div>
                                <div class="rdv-detail">
                                    <span>💰</span>
                                    <span><?php echo $rdv['prix']; ?>€</span>
                                </div>
                            </div>
                            <span class="status-badge status-confirmed">✅ Confirmé</span>
                        </div>
                        <div class="rdv-actions">
                            <a href="coach_messages.php?coach=<?php echo $rdv['coach_id']; ?>" 
                               class="btn btn-outline btn-sm">💬 Contacter</a>
                            <form method="POST" style="display: inline;" 
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?')">
                                <input type="hidden" name="rdv_id" value="<?php echo $rdv['id']; ?>">
                                <button type="submit" name="annuler" class="btn btn-danger btn-sm">❌ Annuler</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php elseif ($user_connected): ?>
                    <div class="no-rdv">
                        <div class="no-rdv-icon">📅</div>
                        <h3>Aucun rendez-vous programmé</h3>
                        <p>Vous n'avez pas encore de rendez-vous confirmés.</p>
                        <button class="btn btn-primary" onclick="showTab('nouveau-rdv')">➕ Réserver maintenant</button>
                    </div>
                <?php else: ?>
                    <div class="no-rdv">
                        <div class="no-rdv-icon">🔒</div>
                        <h3>Connexion requise</h3>
                        <p>Veuillez vous connecter pour voir vos rendez-vous.</p>
                        <a href="votre_compte.php" class="btn btn-primary">🔑 Se connecter</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Onglet Nouveau Rendez-vous -->
            <div id="nouveau-rdv" class="tab-content">
                <h2>➕ Réserver un Nouveau Rendez-vous</h2>
                
                <form method="POST" id="reservationForm">
                    <!-- Étape 1: Sélection du coach -->
                    <div class="booking-step">
                        <h3><span class="step-number" id="step1">1</span>Choisissez votre coach</h3>
                        <div class="coach-selection">
                            <?php foreach ($coachs_disponibles as $coach): ?>
                            <div class="coach-card-rdv" onclick="selectCoach(<?php echo $coach['id']; ?>)" 
                                 <?php if ($coach_preselectionne == $coach['id']): ?>style="border-color: #007BFF; background: #f0f8ff;"<?php endif; ?>>
                                <div class="coach-header">
                                    <div class="coach-avatar-rdv">
                                        <img src="media/images/<?php echo $coach['photo']; ?>" 
                                             alt="<?php echo $coach['prenom']; ?>"
                                             style="width: 100%; height: 100%; object-fit: cover;"
                                             onerror="this.src='https://via.placeholder.com/60x60/007BFF/ffffff?text=<?php echo substr($coach['prenom'], 0, 1); ?>'">
                                    </div>
                                    <div class="coach-info-rdv">
                                        <h3><?php echo htmlspecialchars($coach['prenom'] . ' ' . $coach['nom']); ?></h3>
                                        <p>🎯 <?php echo htmlspecialchars($coach['specialite']); ?></p>
                                        <p>⭐ <?php echo $coach['note']; ?>/5</p>
                                    </div>
                                </div>
                                <div class="price-info">
                                    💰 <?php echo $coach['prix']; ?>€ - ⏱️ <?php echo $coach['duree']; ?>min
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Étape 2: Sélection de la date -->
                    <div class="booking-step">
                        <h3><span class="step-number" id="step2">2</span>Choisissez une date</h3>
                        <div class="calendar-container">
                            <div class="calendar-grid" id="calendar">
                                <!-- Généré par JavaScript -->
                            </div>
                        </div>
                    </div>

                    <!-- Étape 3: Sélection de l'heure -->
                    <div class="booking-step">
                        <h3><span class="step-number" id="step3">3</span>Choisissez un horaire</h3>
                        <div class="time-slots" id="timeSlots">
                            <!-- Généré par JavaScript -->
                        </div>
                    </div>

                    <!-- Résumé et confirmation -->
                    <div class="booking-summary" id="bookingSummary" style="display: none;">
                        <h3>📋 Résumé de votre réservation</h3>
                        <div id="summaryContent"></div>
                        
                        <input type="hidden" name="coach_id" id="selectedCoachId">
                        <input type="hidden" name="date" id="selectedDate">
                        <input type="hidden" name="heure" id="selectedHeure">
                        
                        <?php if ($user_connected): ?>
                            <button type="submit" name="reserver" class="btn btn-primary btn-lg">
                                ✅ Confirmer la réservation
                            </button>
                        <?php else: ?>
                            <p><strong>⚠️ Vous devez être connecté pour réserver.</strong></p>
                            <a href="votre_compte.php" class="btn btn-primary">🔑 Se connecter</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Onglet Planning Général -->
            <div id="planning" class="tab-content">
                <h2>📊 Planning Général des Coachs</h2>
                <p>Visualisez les disponibilités de tous nos coachs pour les prochains jours.</p>
                
                <div class="planning-overview">
                    <?php foreach ($coachs_disponibles as $coach): ?>
                    <div class="coach-planning">
                        <h3>🏋️‍♂️ <?php echo htmlspecialchars($coach['prenom'] . ' ' . $coach['nom']); ?> - <?php echo $coach['specialite']; ?></h3>
                        <div class="planning-days">
                            <?php foreach ($coach['disponibilites'] as $date => $heures): ?>
                            <div class="planning-day">
                                <h4><?php echo date('D d/m', strtotime($date)); ?></h4>
                                <div class="planning-slots">
                                    <?php foreach ($heures as $heure): ?>
                                    <span class="planning-slot available"><?php echo $heure; ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Sportify - Votre plateforme de rendez-vous sportifs</p>
        </div>
    </footer>

    <script>
        // Variables globales
        let selectedCoach = <?php echo $coach_preselectionne ?: 'null'; ?>;
        let selectedDate = null;
        let selectedHeure = null;
        const coachsData = <?php echo json_encode($coachs_disponibles); ?>;

        // Gestion des onglets
        function showTab(tabName) {
            // Cacher tous les contenus
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Désactiver tous les onglets
            document.querySelectorAll('.rdv-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Activer l'onglet et le contenu sélectionnés
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }

        // Sélection du coach
        function selectCoach(coachId) {
            selectedCoach = coachId;
            selectedDate = null;
            selectedHeure = null;
            
            // Mise à jour visuelle
            document.querySelectorAll('.coach-card-rdv').forEach(card => {
                card.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
            
            // Mise à jour des étapes
            document.getElementById('step1').classList.add('step-completed');
            
            // Générer le calendrier
            generateCalendar();
            updateBookingSummary();
        }

        // Génération du calendrier
        function generateCalendar() {
            if (!selectedCoach) return;
            
            const calendar = document.getElementById('calendar');
            const coach = coachsData.find(c => c.id === selectedCoach);
            
            calendar.innerHTML = '';
            
            Object.keys(coach.disponibilites).forEach(date => {
                const dayDiv = document.createElement('div');
                dayDiv.className = 'calendar-day';
                dayDiv.innerHTML = `
                    <div style="font-weight: bold;">${new Date(date).toLocaleDateString('fr-FR', {weekday: 'short'})}</div>
                    <div>${new Date(date).toLocaleDateString('fr-FR', {day: 'numeric', month: 'numeric'})}</div>
                    <div style="font-size: 12px; color: #666;">${coach.disponibilites[date].length} créneaux</div>
                `;
                
                dayDiv.onclick = () => selectDate(date);
                calendar.appendChild(dayDiv);
            });
        }

        // Sélection de la date
        function selectDate(date) {
            selectedDate = date;
            selectedHeure = null;
            
            // Mise à jour visuelle
            document.querySelectorAll('.calendar-day').forEach(day => {
                day.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
            
            // Mise à jour des étapes
            document.getElementById('step2').classList.add('step-completed');
            
            // Générer les créneaux horaires
            generateTimeSlots();
            updateBookingSummary();
        }

        // Génération des créneaux horaires
        function generateTimeSlots() {
            if (!selectedCoach || !selectedDate) return;
            
            const timeSlotsContainer = document.getElementById('timeSlots');
            const coach = coachsData.find(c => c.id === selectedCoach);
            const heures = coach.disponibilites[selectedDate];
            
            timeSlotsContainer.innerHTML = '';
            
            heures.forEach(heure => {
                const slotDiv = document.createElement('div');
                slotDiv.className = 'time-slot';
                slotDiv.textContent = heure;
                slotDiv.onclick = () => selectTime(heure);
                timeSlotsContainer.appendChild(slotDiv);
            });
        }

        // Sélection de l'heure
        function selectTime(heure) {
            selectedHeure = heure;
            
            // Mise à jour visuelle
            document.querySelectorAll('.time-slot').forEach(slot => {
                slot.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
            
            // Mise à jour des étapes
            document.getElementById('step3').classList.add('step-completed');
            
            updateBookingSummary();
        }

        // Mise à jour du résumé
        function updateBookingSummary() {
            const summary = document.getElementById('bookingSummary');
            const summaryContent = document.getElementById('summaryContent');
            
            if (selectedCoach && selectedDate && selectedHeure) {
                const coach = coachsData.find(c => c.id === selectedCoach);
                
                summaryContent.innerHTML = `
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                        <div>
                            <h4>👨‍🏫 Coach</h4>
                            <p>${coach.prenom} ${coach.nom}</p>
                            <p>🎯 ${coach.specialite}</p>
                        </div>
                        <div>
                            <h4>📅 Date & Heure</h4>
                            <p>${new Date(selectedDate).toLocaleDateString('fr-FR', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'})}</p>
                            <p>🕐 ${selectedHeure} (${coach.duree} minutes)</p>
                        </div>
                        <div>
                            <h4>💰 Tarif</h4>
                            <p style="font-size: 24px; font-weight: bold; color: #28a745;">${coach.prix}€</p>
                        </div>
                        <div>
                            <h4>📍 Lieu</h4>
                            <p>Salle Omnes</p>
                            <p>Studio de ${coach.specialite}</p>
                        </div>
                    </div>
                `;
                
                // Remplir les champs cachés
                document.getElementById('selectedCoachId').value = selectedCoach;
                document.getElementById('selectedDate').value = selectedDate;
                document.getElementById('selectedHeure').value = selectedHeure;
                
                summary.style.display = 'block';
            } else {
                summary.style.display = 'none';
            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            // Si un coach est pré-sélectionné, l'activer
            if (selectedCoach) {
                const coachCard = document.querySelector(`[onclick="selectCoach(${selectedCoach})"]`);
                if (coachCard) {
                    coachCard.classList.add('selected');
                    document.getElementById('step1').classList.add('step-completed');
                    generateCalendar();
                }
            }
        });

        // Styles CSS additionnels pour le planning
        const additionalStyles = `
            <style>
                .coach-planning {
                    background: white;
                    border: 1px solid #e9ecef;
                    border-radius: 10px;
                    padding: 20px;
                    margin: 20px 0;
                }
                .planning-days {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                    gap: 15px;
                    margin-top: 15px;
                }
                .planning-day h4 {
                    margin: 0 0 10px 0;
                    color: #333;
                    font-size: 14px;
                    text-transform: uppercase;
                }
                .planning-slots {
                    display: flex;
                    flex-direction: column;
                    gap: 5px;
                }
                .planning-slot {
                    padding: 5px 10px;
                    background: #e3f2fd;
                    border-radius: 5px;
                    text-align: center;
                    font-size: 12px;
                    color: #1976d2;
                }
                .planning-slot.available {
                    background: #d4edda;
                    color: #155724;
                }
            </style>
        `;
        document.head.insertAdjacentHTML('beforeend', additionalStyles);
    </script>
</body>
</html>
