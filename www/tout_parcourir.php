<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tout Parcourir - Sportify</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styles améliorés pour les images d'activités */
        .activity-image img, .sport-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }
        
        .activity-card:hover .activity-image img,
        .sport-card:hover .sport-image img {
            transform: scale(1.05);
        }
        
        .activity-card, .sport-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .activity-card:hover, .sport-card:hover {
            transform: translateY(-10px);
        }
        
        .coach-avatar img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .hero-secondary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .category-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .category-card:hover {
            transform: translateY(-10px);
        }
        
        .category-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>

    <!-- Navigation -->
    <div class="top-bar">
        <div class="header-container">
            <nav>
                <ul>
                    <li class="<?= ($current_page == 'accueil.php') ? 'active' : '' ?>"><a href="accueil.php">Accueil</a></li>
                    <li class="<?= ($current_page == 'tout_parcourir.php') ? 'active' : '' ?>"><a href="tout_parcourir.php">Tout Parcourir</a></li>
                    <li class="<?= ($current_page == 'recherche.php') ? 'active' : '' ?>"><a href="recherche.php">Recherche</a></li>
                    <li class="<?= ($current_page == 'rendez_vous.php') ? 'active' : '' ?>"><a href="rendez_vous.php">Rendez-vous</a></li>
                    <li class="<?= ($current_page == 'votre_compte.php') ? 'active' : '' ?>"><a href="votre_compte.php">Votre Compte</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Section d'introduction -->
    <section class="hero hero-secondary">
        <div class="hero-content">
            <h1>Trouvez l'activité qui vous correspond</h1>
            <p class="hero-subtitle">Explorez nos services et choisissez celui qui vous convient le mieux</p>
        </div>
    </section>

    <!-- Section Catégories principales -->
    <section class="categories">
        <div class="container">
            <h2>Choisissez une catégorie</h2>
            <div class="categories-grid">
                <div class="category-card" data-category="activites-sportives">
                    <div class="category-icon">🏋️‍♂️</div>
                    <h3><a href="#activites-sportives">Activités sportives</a></h3>
                    <p>Découvrez les activités disponibles pour tous les niveaux</p>
                    <div class="category-stats">
                        <span>5 activités</span>
                    </div>
                </div>
                
                <div class="category-card" data-category="sports-competitions">
                    <div class="category-icon">🏆</div>
                    <h3><a href="#sports-competitions">Sports de compétition</a></h3>
                    <p>Rejoignez nos équipes de compétition et améliorez vos performances</p>
                    <div class="category-stats">
                        <span>6 sports</span>
                    </div>
                </div>
                
                <div class="category-card" data-category="salle-sport">
                    <div class="category-icon">🏢</div>
                    <h3><a href="#salle-sport">Salle de sport Omnes</a></h3>
                    <p>Accédez à nos équipements et services dans la salle de sport Omnes</p>
                    <div class="category-stats">
                        <span>5 services</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Activités sportives -->
    <section id="activites-sportives" class="activites-sportives">
        <div class="container">
            <div class="section-header">
                <h2>Activités sportives</h2>
                <p>Des activités adaptées à tous les niveaux pour améliorer votre condition physique</p>
            </div>
            
            <div class="activities-grid">
                <div class="activity-card">
                    <div class="activity-image">
                        <img src="https://images.unsplash.com/photo-1532029837206-abbe2b7620e3?q=80&w=350&h=200&auto=format&fit=crop&crop=center&ixlib=rb-4.1.0" 
                            alt="Musculation"
                            onerror="this.src='https://via.placeholder.com/350x200/FF6B6B/ffffff?text=💪+Musculation'">
                    </div>

                    <div class="activity-content">
                        <h3>Musculation</h3>
                        <p class="activity-description">
                            Suivez un programme de musculation personnalisé avec nos coachs professionnels.
                        </p>
                        <div class="coach-preview">
                            <div class="coach-avatar">
                                <img src="https://ui-avatars.com/api/?name=Guy+DUMAIS&size=50&background=FF6B6B&color=ffffff&font-size=0.4&bold=true" 
                                     alt="Guy DUMAIS"
                                     onerror="this.src='https://via.placeholder.com/50x50/FF6B6B/ffffff?text=GD'">
                            </div>
                            <div class="coach-info">
                                <p class="coach-name">Guy DUMAIS</p>
                                <p class="coach-title">Coach Musculation</p>
                            </div>
                        </div>
                        <div class="activity-actions">
                            <a href="coach_detail.php?id=1" class="btn btn-primary">Voir le coach</a>
                            <a href="rendez_vous.php?coach=1" class="btn btn-outline">Réserver</a>
                        </div>
                    </div>
                </div>

                <div class="activity-card">
                    <div class="activity-image">
                        <img src="https://images.unsplash.com/photo-1518611012118-696072aa579a?w=350&h=200&fit=crop&crop=center" 
                             alt="Fitness"
                             onerror="this.src='https://via.placeholder.com/350x200/4ECDC4/ffffff?text=🏃‍♀️+Fitness'">
                    </div>
                    <div class="activity-content">
                        <h3>Fitness</h3>
                        <p class="activity-description">
                            Participez à nos séances de fitness dynamiques et motivantes.
                        </p>
                        <div class="coach-preview">
                            <div class="coach-avatar">
                                <img src="https://ui-avatars.com/api/?name=Marie+MARTIN&size=50&background=4ECDC4&color=ffffff&font-size=0.4&bold=true" 
                                     alt="Marie MARTIN"
                                     onerror="this.src='https://via.placeholder.com/50x50/4ECDC4/ffffff?text=MM'">
                            </div>
                            <div class="coach-info">
                                <p class="coach-name">Marie MARTIN</p>
                                <p class="coach-title">Coach Fitness</p>
                            </div>
                        </div>
                        <div class="activity-actions">
                            <a href="coach_detail.php?id=2" class="btn btn-primary">Voir le coach</a>
                            <a href="rendez_vous.php?coach=2" class="btn btn-outline">Réserver</a>
                        </div>
                    </div>
                </div>

                <div class="activity-card">
                    <div class="activity-image">
                        <img src="https://images.unsplash.com/photo-1554470166-20d3f466089b?q=80&w=350&h=200&auto=format&fit=crop&crop=center&ixlib=rb-4.1.0" 
                            alt="Biking"
                            onerror="this.src='https://via.placeholder.com/350x200/45B7D1/ffffff?text=🚴‍♂️+Biking'">
                    </div>

                    <div class="activity-content">
                        <h3>Biking</h3>
                        <p class="activity-description">
                            Rejoignez nos cours de biking et faites du sport de manière ludique.
                        </p>
                        <div class="coach-preview">
                            <div class="coach-avatar">
                                <img src="https://ui-avatars.com/api/?name=Paul+BERNARD&size=50&background=45B7D1&color=ffffff&font-size=0.4&bold=true" 
                                     alt="Paul BERNARD"
                                     onerror="this.src='https://via.placeholder.com/50x50/45B7D1/ffffff?text=PB'">
                            </div>
                            <div class="coach-info">
                                <p class="coach-name">Paul BERNARD</p>
                                <p class="coach-title">Coach Biking</p>
                            </div>
                        </div>
                        <div class="activity-actions">
                            <a href="coach_detail.php?id=3" class="btn btn-primary">Voir le coach</a>
                            <a href="rendez_vous.php?coach=3" class="btn btn-outline">Réserver</a>
                        </div>
                    </div>
                </div>

                <div class="activity-card">
                    <div class="activity-image">
                        <img src="https://images.unsplash.com/photo-1633394782240-f81aba3f850d?q=80&w=350&h=200&auto=format&fit=crop&crop=top&ixlib=rb-4.1.0" 
                            alt="Cardio-Training"
                            onerror="this.src='https://via.placeholder.com/350x200/F093FB/ffffff?text=❤️+Cardio'">
                    </div>


                    <div class="activity-content">
                        <h3>Cardio-Training</h3>
                        <p class="activity-description">
                            Améliorez votre condition physique avec nos coachs spécialisés en cardio-training.
                        </p>
                        <div class="coach-preview">
                            <div class="coach-avatar">
                                <img src="https://ui-avatars.com/api/?name=Sophie+DUBOIS&size=50&background=F093FB&color=ffffff&font-size=0.4&bold=true" 
                                     alt="Sophie DUBOIS"
                                     onerror="this.src='https://via.placeholder.com/50x50/F093FB/ffffff?text=SD'">
                            </div>
                            <div class="coach-info">
                                <p class="coach-name">Sophie DUBOIS</p>
                                <p class="coach-title">Coach Cardio</p>
                            </div>
                        </div>
                        <div class="activity-actions">
                            <a href="coach_detail.php?id=4" class="btn btn-primary">Voir le coach</a>
                            <a href="rendez_vous.php?coach=4" class="btn btn-outline">Réserver</a>
                        </div>
                    </div>
                </div>

                <div class="activity-card">
                    <div class="activity-image">
                        <img src="https://images.unsplash.com/photo-1549576490-b0b4831ef60a?w=350&h=200&fit=crop&crop=center" 
                             alt="Cours Collectifs"
                             onerror="this.src='https://via.placeholder.com/350x200/4568DC/ffffff?text=👥+Cours+Collectifs'">
                    </div>
                    <div class="activity-content">
                        <h3>Cours Collectifs</h3>
                        <p class="activity-description">
                            Participez à des cours collectifs pour une expérience sportive enrichissante.
                        </p>
                        <div class="coach-preview">
                            <div class="coach-avatar">
                                <img src="https://ui-avatars.com/api/?name=Jean+MOREAU&size=50&background=4568DC&color=ffffff&font-size=0.4&bold=true" 
                                     alt="Jean MOREAU"
                                     onerror="this.src='https://via.placeholder.com/50x50/4568DC/ffffff?text=JM'">
                            </div>
                            <div class="coach-info">
                                <p class="coach-name">Jean MOREAU</p>
                                <p class="coach-title">Coach Collectifs</p>
                            </div>
                        </div>
                        <div class="activity-actions">
                            <a href="coach_detail.php?id=5" class="btn btn-primary">Voir le coach</a>
                            <a href="rendez_vous.php?coach=5" class="btn btn-outline">Réserver</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Sports de compétition -->
    <section id="sports-competitions" class="sports-competitions">
        <div class="container">
            <div class="section-header">
                <h2>Sports de compétition</h2>
                <p>Entraînements de haut niveau pour les sportifs ambitieux</p>
            </div>
            
            <div class="sports-grid">
                <div class="sport-card">
                    <div class="sport-image">
                        <img src="https://images.unsplash.com/photo-1546519638-68e109498ffc?w=300&h=180&fit=crop&crop=center" 
                             alt="Basketball"
                             onerror="this.src='https://via.placeholder.com/300x180/FF9A9E/ffffff?text=🏀+Basketball'">
                        <div class="sport-overlay">
                            <h3>Basketball</h3>
                        </div>
                    </div>
                    <div class="sport-content">
                        <div class="sport-details">
                            <h4>Coach : Marc LEBRUN</h4>
                            <p>Entraîneur professionnel de basketball</p>
                        </div>
                        <div class="sport-actions">
                            <a href="coach_detail.php?id=6" class="btn btn-primary">Voir le coach</a>
                            <a href="rendez_vous.php?coach=6" class="btn btn-outline">S'inscrire</a>
                        </div>
                    </div>
                </div>

                <div class="sport-card">
                    <div class="sport-image">
                        <img src="https://images.unsplash.com/photo-1431324155629-1a6deb1dec8d?w=300&h=180&fit=crop&crop=center" 
                             alt="Football"
                             onerror="this.src='https://via.placeholder.com/300x180/A8EDEA/ffffff?text=⚽+Football'">
                        <div class="sport-overlay">
                            <h3>Football</h3>
                        </div>
                    </div>
                    <div class="sport-content">
                        <div class="sport-details">
                            <h4>Coach : Luc GARCIA</h4>
                            <p>Entraîneur de football compétitif</p>
                        </div>
                        <div class="sport-actions">
                            <a href="coach_detail.php?id=7" class="btn btn-primary">Voir le coach</a>
                            <a href="rendez_vous.php?coach=7" class="btn btn-outline">S'inscrire</a>
                        </div>
                    </div>
                </div>

                <div class="sport-card">
                    <div class="sport-image">
                        <img src="https://images.unsplash.com/photo-1512299286776-c18be8ed6a1a?q=80&w=300&h=180&auto=format&fit=crop&ixlib=rb-4.1.0" 
                            alt="Rugby"
                            onerror="this.src='https://via.placeholder.com/300x180/D299C2/ffffff?text=🏉+Rugby'">
                        <div class="sport-overlay">
                            <h3>Rugby</h3>
                        </div>
                    </div>

                    <div class="sport-content">
                        <div class="sport-details">
                            <h4>Coach : Pierre ROUX</h4>
                            <p>Spécialiste rugby de haut niveau</p>
                        </div>
                        <div class="sport-actions">
                            <a href="coach_detail.php?id=8" class="btn btn-primary">Voir le coach</a>
                            <a href="rendez_vous.php?coach=8" class="btn btn-outline">S'inscrire</a>
                        </div>
                    </div>
                </div>

                <div class="sport-card">
                    <div class="sport-image">
                        <img src="https://images.unsplash.com/photo-1622279457486-62dcc4a431d6?w=300&h=180&fit=crop&crop=center" 
                             alt="Tennis"
                             onerror="this.src='https://via.placeholder.com/300x180/45B7D1/ffffff?text=🎾+Tennis'">
                        <div class="sport-overlay">
                            <h3>Tennis</h3>
                        </div>
                    </div>
                    <div class="sport-content">
                        <div class="sport-details">
                            <h4>Coach : Anna BLANC</h4>
                            <p>Professeure de tennis certifiée</p>
                        </div>
                        <div class="sport-actions">
                            <a href="coach_detail.php?id=9" class="btn btn-primary">Voir le coach</a>
                            <a href="rendez_vous.php?coach=9" class="btn btn-outline">S'inscrire</a>
                        </div>
                    </div>
                </div>

                <div class="sport-card">
                    <div class="sport-image">
                        <img src="https://images.unsplash.com/photo-1530549387789-4c1017266635?w=300&h=180&fit=crop&crop=center" 
                             alt="Natation"
                             onerror="this.src='https://via.placeholder.com/300x180/89F7FE/ffffff?text=🏊‍♂️+Natation'">
                        <div class="sport-overlay">
                            <h3>Natation</h3>
                        </div>
                    </div>
                    <div class="sport-content">
                        <div class="sport-details">
                            <h4>Coach : Julie PETIT</h4>
                            <p>Maître-nageur diplômée d'État</p>
                        </div>
                        <div class="sport-actions">
                            <a href="coach_detail.php?id=10" class="btn btn-primary">Voir le coach</a>
                            <a href="rendez_vous.php?coach=10" class="btn btn-outline">S'inscrire</a>
                        </div>
                    </div>
                </div>

                <div class="sport-card">
                    <div class="sport-image">
                        <img src="https://images.unsplash.com/photo-1659303388057-de33f9a6a924?q=80&w=300&h=180&auto=format&fit=crop&ixlib=rb-4.1.0" 
                            alt="Plongeon"
                            onerror="this.src='https://via.placeholder.com/300x180/FDBB2D/ffffff?text=🤸‍♂️+Plongeon'">
                        <div class="sport-overlay">
                            <h3>Plongeon</h3>
                        </div>
                    </div>

                    <div class="sport-content">
                        <div class="sport-details">
                            <h4>Coach : Thomas COLIN</h4>
                            <p>Spécialiste plongeon artistique</p>
                        </div>
                        <div class="sport-actions">
                            <a href="coach_detail.php?id=11" class="btn btn-primary">Voir le coach</a>
                            <a href="rendez_vous.php?coach=11" class="btn btn-outline">S'inscrire</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Salle de sport Omnes -->
    <section id="salle-sport" class="salle-sport">
        <div class="container">
            <div class="section-header">
                <h2>Salle de sport Omnes</h2>
                <p>Équipements modernes et services personnalisés</p>
            </div>
            
            <div class="salle-content">
                <div class="salle-info-card">
                    <div class="info-header">
                        <h3>Informations pratiques</h3>
                    </div>
                    
                    <div class="info-details">
                        <div class="info-item">
                            <strong>⏰</strong>
                            <div>
                                <strong>Horaires d'ouverture :</strong>
                                <p>7h00 - 22h00</p>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <strong>📞</strong>
                            <div>
                                <strong>Téléphone :</strong>
                                <p><a href="tel:+33123456789">+33 1 23 45 67 89</a></p>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <strong>✉️</strong>
                            <div>
                                <strong>Email :</strong>
                                <p><a href="mailto:salle@sportify.com">salle@sportify.com</a></p>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <strong>📍</strong>
                            <div>
                                <strong>Adresse :</strong>
                                <p>123 Rue du Sport, Paris</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="services-grid">
                    <h3>Nos services</h3>
                    <div class="service-cards">
                        <div class="service-card">
                            <h4>Personnels de la salle de sport</h4>
                            <p>Équipe qualifiée à votre service</p>
                            <a href="service_detail.php?id=2" class="btn btn-outline btn-sm">En savoir plus</a>
                        </div>
                        
                        <div class="service-card">
                            <h4>Horaire de la gym</h4>
                            <p>Consultez nos horaires d'ouverture</p>
                            <a href="service_detail.php?id=1" class="btn btn-outline btn-sm">En savoir plus</a>
                        </div>
                        
                        <div class="service-card">
                            <h4>Règles sur l'utilisation des machines</h4>
                            <p>Guide d'utilisation des équipements</p>
                            <a href="service_detail.php?id=3" class="btn btn-outline btn-sm">En savoir plus</a>
                        </div>
                        
                        <div class="service-card">
                            <h4>Nouveaux clients</h4>
                            <p>Informations pour les nouveaux membres</p>
                            <a href="service_detail.php?id=4" class="btn btn-outline btn-sm">En savoir plus</a>
                        </div>
                        
                        <div class="service-card">
                            <h4>Alimentation et nutrition</h4>
                            <p>Conseils nutritionnels personnalisés</p>
                            <a href="service_detail.php?id=5" class="btn btn-outline btn-sm">En savoir plus</a>
                        </div>
                    </div>
                </div>
                
                <div class="salle-booking">
                    <div class="booking-card">
                        <h3>Réserver une visite</h3>
                        <p>Découvrez nos installations lors d'une visite guidée gratuite</p>
                        <div class="booking-features">
                            <span>✓ Visite guidée</span>
                            <span>✓ Test des équipements</span>
                            <span>✓ Conseil personnalisé</span>
                        </div>
                        <a href="rendez_vous.php?service=1" class="btn btn-primary btn-lg">
                            Prendre rendez-vous pour une visite
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Call to Action -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Prêt à commencer votre parcours sportif ?</h2>
                <p>Rejoignez la communauté Sportify et atteignez vos objectifs avec nos coachs professionnels</p>
                <div class="cta-actions">
                    <a href="votre_compte.php" class="btn btn-primary btn-lg">S'inscrire maintenant</a>
                    <a href="mailto:contact@sportify.com" class="btn btn-outline btn-lg">Nous contacter</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Sportify - Tous droits réservés</p>
        </div>
    </footer>

    <script>
        // Smooth scrolling pour les liens d'ancrage
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if(target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Animation des cartes au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, observerOptions);

        // Observer toutes les cartes
        document.querySelectorAll('.activity-card, .sport-card, .category-card').forEach(card => {
            observer.observe(card);
        });

        // Animation des catégories au clic
        document.querySelectorAll('.category-card').forEach(card => {
            card.addEventListener('click', function() {
                const targetId = this.getAttribute('data-category');
                const targetSection = document.getElementById(targetId);
                if(targetSection) {
                    targetSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Effet de hover sur les images
        document.querySelectorAll('.activity-image, .sport-image').forEach(image => {
            image.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.02)';
            });
            
            image.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>