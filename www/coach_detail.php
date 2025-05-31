<?php
// Données des coachs
$coachs = [
    1 => [
        'nom' => 'Guy DUMAIS',
        'specialite' => 'Coach Musculation',
        'image' => 'https://ui-avatars.com/api/?name=Guy+DUMAIS&size=200&background=FF6B6B&color=ffffff&font-size=0.4&bold=true',
        'description' => 'Expert en musculation avec 10 ans d\'expérience. Spécialisé dans la prise de masse et la définition musculaire.',
        'experience' => '10 ans',
        'certifications' => ['Diplôme d\'État BPJEPS', 'Certification CrossFit Level 1', 'Formation Nutrition Sportive'],
        'specialites' => ['Musculation', 'Powerlifting', 'Préparation physique'],
        'horaires' => 'Lundi-Vendredi: 9h-18h, Samedi: 9h-14h',
        'tarif' => '45€/séance'
    ],
    2 => [
        'nom' => 'Marie MARTIN',
        'specialite' => 'Coach Fitness',
        'image' => 'https://ui-avatars.com/api/?name=Marie+MARTIN&size=200&background=4ECDC4&color=ffffff&font-size=0.4&bold=true',
        'description' => 'Passionnée de fitness et de bien-être. Spécialisée dans les cours collectifs et l\'entraînement fonctionnel.',
        'experience' => '8 ans',
        'certifications' => ['Diplôme d\'État BPJEPS', 'Certification Pilates', 'Formation Yoga'],
        'specialites' => ['Fitness', 'Pilates', 'Yoga', 'Cours collectifs'],
        'horaires' => 'Lundi-Samedi: 8h-19h',
        'tarif' => '40€/séance'
    ],
    3 => [
        'nom' => 'Paul BERNARD',
        'specialite' => 'Coach Biking',
        'image' => 'https://ui-avatars.com/api/?name=Paul+BERNARD&size=200&background=45B7D1&color=ffffff&font-size=0.4&bold=true',
        'description' => 'Ancien cycliste professionnel reconverti en coach. Expert en biking et préparation cardiovasculaire.',
        'experience' => '12 ans',
        'certifications' => ['Diplôme d\'État BPJEPS', 'Certification Spinning', 'Formation Entraînement Cycliste'],
        'specialites' => ['Biking', 'Cyclisme', 'Cardio-training'],
        'horaires' => 'Mardi-Dimanche: 7h-20h',
        'tarif' => '35€/séance'
    ],
    4 => [
        'nom' => 'Sophie DUBOIS',
        'specialite' => 'Coach Cardio',
        'image' => 'https://ui-avatars.com/api/?name=Sophie+DUBOIS&size=200&background=F093FB&color=ffffff&font-size=0.4&bold=true',
        'description' => 'Spécialiste en cardio-training et rééducation. Aide ses clients à améliorer leur condition physique.',
        'experience' => '6 ans',
        'certifications' => ['Diplôme d\'État BPJEPS', 'Formation Cardio-training', 'Certification First Aid'],
        'specialites' => ['Cardio-training', 'Rééducation', 'Remise en forme'],
        'horaires' => 'Lundi-Vendredi: 10h-19h',
        'tarif' => '38€/séance'
    ],
    5 => [
        'nom' => 'Jean MOREAU',
        'specialite' => 'Coach Collectifs',
        'image' => 'https://ui-avatars.com/api/?name=Jean+MOREAU&size=200&background=4568DC&color=ffffff&font-size=0.4&bold=true',
        'description' => 'Animateur dynamique spécialisé dans les cours collectifs. Crée une ambiance motivante pour tous.',
        'experience' => '7 ans',
        'certifications' => ['Diplôme d\'État BPJEPS', 'Certification Zumba', 'Formation Animation Sportive'],
        'specialites' => ['Cours collectifs', 'Zumba', 'Step', 'Aqua-fitness'],
        'horaires' => 'Lundi-Samedi: 9h-21h',
        'tarif' => '25€/séance'
    ],
    6 => [
        'nom' => 'Marc LEBRUN',
        'specialite' => 'Entraîneur Basketball',
        'image' => 'https://ui-avatars.com/api/?name=Marc+LEBRUN&size=200&background=FF9A9E&color=ffffff&font-size=0.4&bold=true',
        'description' => 'Ancien joueur professionnel de basketball. Entraîne les équipes de compétition.',
        'experience' => '15 ans',
        'certifications' => ['Diplôme d\'État DEJEPS', 'Licence Entraîneur Basketball', 'Formation Préparation Mentale'],
        'specialites' => ['Basketball', 'Préparation physique', 'Tactique'],
        'horaires' => 'Lundi-Vendredi: 16h-21h, Samedi: 9h-17h',
        'tarif' => '50€/séance'
    ],
    7 => [
        'nom' => 'Luc GARCIA',
        'specialite' => 'Entraîneur Football',
        'image' => 'https://ui-avatars.com/api/?name=Luc+GARCIA&size=200&background=A8EDEA&color=ffffff&font-size=0.4&bold=true',
        'description' => 'Entraîneur de football compétitif avec une approche moderne du jeu.',
        'experience' => '11 ans',
        'certifications' => ['Diplôme d\'État DEJEPS', 'Licence UEFA B', 'Formation Analyse Vidéo'],
        'specialites' => ['Football', 'Tactique', 'Préparation physique'],
        'horaires' => 'Mardi-Dimanche: 15h-20h',
        'tarif' => '45€/séance'
    ],
    8 => [
        'nom' => 'Pierre ROUX',
        'specialite' => 'Spécialiste Rugby',
        'image' => 'https://ui-avatars.com/api/?name=Pierre+ROUX&size=200&background=D299C2&color=ffffff&font-size=0.4&bold=true',
        'description' => 'Ancien international de rugby. Spécialiste du rugby de haut niveau.',
        'experience' => '18 ans',
        'certifications' => ['Diplôme d\'État DEJEPS', 'Licence Entraîneur Rugby', 'Formation Leadership'],
        'specialites' => ['Rugby', 'Mêlée', 'Condition physique'],
        'horaires' => 'Mercredi-Dimanche: 14h-19h',
        'tarif' => '55€/séance'
    ],
    9 => [
        'nom' => 'Anna BLANC',
        'specialite' => 'Professeure Tennis',
        'image' => 'https://ui-avatars.com/api/?name=Anna+BLANC&size=200&background=45B7D1&color=ffffff&font-size=0.4&bold=true',
        'description' => 'Professeure de tennis certifiée FFT. Enseigne à tous les niveaux.',
        'experience' => '9 ans',
        'certifications' => ['Diplôme d\'État DEJEPS', 'Certification FFT', 'Formation Tennis Jeunes'],
        'specialites' => ['Tennis', 'Technique', 'Compétition'],
        'horaires' => 'Lundi-Samedi: 8h-18h',
        'tarif' => '60€/séance'
    ],
    10 => [
        'nom' => 'Julie PETIT',
        'specialite' => 'Maître-nageur',
        'image' => 'https://ui-avatars.com/api/?name=Julie+PETIT&size=200&background=89F7FE&color=ffffff&font-size=0.4&bold=true',
        'description' => 'Maître-nageur diplômée d\'État. Spécialisée dans l\'enseignement de la natation.',
        'experience' => '13 ans',
        'certifications' => ['BEESAN', 'Certificat de Sauvetage', 'Formation Natation Adaptée'],
        'specialites' => ['Natation', 'Aqua-fitness', 'Sauvetage'],
        'horaires' => 'Lundi-Dimanche: 6h-22h',
        'tarif' => '40€/séance'
    ],
    11 => [
        'nom' => 'Thomas COLIN',
        'specialite' => 'Spécialiste Plongeon',
        'image' => 'https://ui-avatars.com/api/?name=Thomas+COLIN&size=200&background=FDBB2D&color=ffffff&font-size=0.4&bold=true',
        'description' => 'Ancien compétiteur en plongeon artistique. Enseigne les techniques avancées.',
        'experience' => '14 ans',
        'certifications' => ['Diplôme d\'État DEJEPS', 'Certification Plongeon FFN', 'Formation Sécurité Aquatique'],
        'specialites' => ['Plongeon artistique', 'Technique', 'Compétition'],
        'horaires' => 'Mardi-Samedi: 10h-19h',
        'tarif' => '65€/séance'
    ]
];

// Récupérer l'ID du coach depuis l'URL
$coach_id = isset($_GET['id']) ? intval($_GET['id']) : 1;
$coach = isset($coachs[$coach_id]) ? $coachs[$coach_id] : $coachs[1];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($coach['nom']); ?> - Sportify</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .coach-detail-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .coach-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 40px;
            border-radius: 20px;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 40px;
            flex-wrap: wrap;
        }
        
        .coach-avatar-large {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid rgba(255,255,255,0.3);
            object-fit: cover;
        }
        
        .coach-info h1 {
            margin: 0 0 10px 0;
            font-size: 2.5em;
        }
        
        .coach-info .specialite {
            font-size: 1.2em;
            opacity: 0.9;
            margin-bottom: 20px;
        }
        
        .coach-stats {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            display: block;
        }
        
        .coach-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .coach-main {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .coach-sidebar {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            height: fit-content;
        }
        
        .section-title {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        
        .certifications-list {
            list-style: none;
            padding: 0;
        }
        
        .certifications-list li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            position: relative;
            padding-left: 30px;
        }
        
        .certifications-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #4ECDC4;
            font-weight: bold;
        }
        
        .specialites-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        
        .tag {
            background: #667eea;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9em;
        }
        
        .booking-section {
            background: linear-gradient(135deg, #4ECDC4 0%, #44A08D 100%);
            color: white;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
        }
        
        .price-highlight {
            font-size: 2em;
            font-weight: bold;
            margin: 20px 0;
        }
        
        .btn-book {
            background: white;
            color: #4ECDC4;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.3s ease;
        }
        
        .btn-book:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        
        .back-btn:hover {
            background: #5a6268;
        }
        
        @media (max-width: 768px) {
            .coach-header {
                flex-direction: column;
                text-align: center;
            }
            
            .coach-content {
                grid-template-columns: 1fr;
            }
            
            .coach-stats {
                justify-content: center;
            }
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

    <div class="coach-detail-container">
        <a href="tout_parcourir.php" class="back-btn">← Retour à la liste</a>
        
        <!-- En-tête du coach -->
        <div class="coach-header">
            <img src="<?php echo htmlspecialchars($coach['image']); ?>" 
                 alt="<?php echo htmlspecialchars($coach['nom']); ?>" 
                 class="coach-avatar-large">
            
            <div class="coach-info">
                <h1><?php echo htmlspecialchars($coach['nom']); ?></h1>
                <p class="specialite"><?php echo htmlspecialchars($coach['specialite']); ?></p>
                
                <div class="coach-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo htmlspecialchars($coach['experience']); ?></span>
                        <span>d'expérience</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo count($coach['certifications']); ?></span>
                        <span>certifications</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo count($coach['specialites']); ?></span>
                        <span>spécialités</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="coach-content">
            <div class="coach-main">
                <h2 class="section-title">À propos</h2>
                <p style="font-size: 1.1em; line-height: 1.6; color: #555;">
                    <?php echo htmlspecialchars($coach['description']); ?>
                </p>
                
                <h3 class="section-title" style="margin-top: 40px;">Spécialités</h3>
                <div class="specialites-tags">
                    <?php foreach($coach['specialites'] as $specialite): ?>
                        <span class="tag"><?php echo htmlspecialchars($specialite); ?></span>
                    <?php endforeach; ?>
                </div>
                
                <h3 class="section-title" style="margin-top: 40px;">Certifications et diplômes</h3>
                <ul class="certifications-list">
                    <?php foreach($coach['certifications'] as $certification): ?>
                        <li><?php echo htmlspecialchars($certification); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="coach-sidebar">
                <h3 class="section-title">Informations pratiques</h3>
                
                <div style="margin-bottom: 20px;">
                    <strong>📅 Horaires :</strong>
                    <p style="margin: 5px 0;"><?php echo htmlspecialchars($coach['horaires']); ?></p>
                </div>
                
                <div style="margin-bottom: 30px;">
                    <strong>💰 Tarif :</strong>
                    <p style="margin: 5px 0; font-size: 1.2em; color: #667eea; font-weight: bold;">
                        <?php echo htmlspecialchars($coach['tarif']); ?>
                    </p>
                </div>
                
                <div class="booking-section">
                    <h3>Prêt à commencer ?</h3>
                    <p>Réservez votre première séance avec <?php echo htmlspecialchars(explode(' ', $coach['nom'])[0]); ?></p>
                    <div class="price-highlight"><?php echo htmlspecialchars($coach['tarif']); ?></div>
                    <a href="rendez_vous.php?coach=<?php echo $coach_id; ?>" class="btn-book">
                        Réserver maintenant
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Sportify - Tous droits réservés</p>
        </div>
    </footer>
</body>
</html>