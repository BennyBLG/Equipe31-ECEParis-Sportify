<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Sportify</title>
    <link rel="stylesheet" href="style.css">
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

    <!-- Section d'introduction et slogan -->
    <section class="hero">
        <div class="hero-content">
            <h1>Bienvenue sur Sportify</h1>
            <p class="hero-subtitle">Votre coach, votre rythme, votre succès</p>
            <a href="tout_parcourir.php" class="btn btn-primary">Découvrez nos services</a>
        </div>
    </section>

    <!-- Section Événement de la semaine -->
    <section class="evenement">
        <div class="container">
            <div class="evenement-content">
                <div class="evenement-text">
                    <h2>Événement de la semaine</h2>
                    <div class="evenement-details">
                        <h3>Match de Rugby</h3>
                        <p class="evenement-description">Omnes Education vs. Visiteurs</p>
                        <div class="evenement-info">
                            <p><strong>Date :</strong> 25 Juin 2025</p>
                            <p><strong>Lieu :</strong> Stade Omnes, Paris</p>
                        </div>
                    </div>
                </div>
                <div class="evenement-image">
                    <img src="media/images/stade_l.jpg" alt="Événement de la semaine" 
                         class="responsive-img" onerror="this.style.display='none'">
                </div>
            </div>
        </div>
    </section>

    <!-- Section "Qui sommes-nous" -->
    <section class="qui-sommes-nous">
        <div class="container">
            <h2>Qui sommes-nous ?</h2>
            <div class="about-content">
                <p>Sportify est une plateforme innovante qui révolutionne l'accès au sport en ligne. 
                   Nous offrons une expérience personnalisée, où chaque utilisateur peut trouver un coach 
                   spécialisé, suivre des événements sportifs en direct, et bénéficier de conseils adaptés 
                   à ses besoins et à son niveau.</p>
                
                <p>Que vous soyez un débutant souhaitant améliorer sa condition physique ou un sportif 
                   confirmé cherchant à perfectionner sa technique, Sportify met à votre disposition une 
                   équipe de coachs professionnels, experts dans divers domaines du sport.</p>
            </div>
        </div>
    </section>

    <!-- Section Bulletin Sportif de la semaine -->
    <section class="bulletin">
        <div class="container">
            <h2>Bulletin sportif de la semaine</h2>
            <div class="bulletin-grid">
                <article class="bulletin-item">
                    <h3>Championnat universitaire</h3>
                    <p>Les étudiants de Sportify ont brillé dans les dernières épreuves 
                       de natation et de tennis. Découvrez toutes les nouvelles !</p>
                    <time class="bulletin-date"><?php echo date('d/m/Y'); ?></time>
                </article>
                
                <article class="bulletin-item">
                    <h3>Nouvelle salle de musculation</h3>
                    <p>Découvrez notre nouvelle salle de musculation équipée des dernières 
                       technologies pour un entraînement optimal.</p>
                    <time class="bulletin-date"><?php echo date('d/m/Y'); ?></time>
                </article>
                
                <article class="bulletin-item">
                    <h3>Programme été 2025</h3>
                    <p>Inscrivez-vous dès maintenant à nos programmes d'été avec des 
                       activités en plein air et des camps sportifs.</p>
                    <time class="bulletin-date"><?php echo date('d/m/Y'); ?></time>
                </article>
            </div>
        </div>
    </section>

    <!-- Section Carrousel des coachs -->
    <section class="carrousel">
        <div class="container">
            <h2>Nos coachs professionnels</h2>
            <div class="coaches-carousel">
                <div class="carousel-container">
                    <div class="carousel-track">
                        <div class="coach-card">
                            <div class="coach-image">
                                <img src="media/images/coach1.jpg" alt="Guy DUMAIS" 
                                     onerror="this.src='https://via.placeholder.com/280x200/007BFF/ffffff?text=Coach+1'">
                            </div>
                            <div class="coach-info">
                                <h4>Guy DUMAIS</h4>
                                <p>Musculation</p>
                                <a href="#" class="btn btn-secondary">Voir profil</a>
                            </div>
                        </div>
                        
                        <div class="coach-card">
                            <div class="coach-image">
                                <img src="media/images/coach2.jpg" alt="Marie MARTIN" 
                                     onerror="this.src='https://via.placeholder.com/280x200/007BFF/ffffff?text=Coach+2'">
                            </div>
                            <div class="coach-info">
                                <h4>Marie MARTIN</h4>
                                <p>Fitness</p>
                                <a href="#" class="btn btn-secondary">Voir profil</a>
                            </div>
                        </div>
                        
                        <div class="coach-card">
                            <div class="coach-image">
                                <img src="media/images/coach3.jpg" alt="Paul BERNARD" 
                                     onerror="this.src='https://via.placeholder.com/280x200/007BFF/ffffff?text=Coach+3'">
                            </div>
                            <div class="coach-info">
                                <h4>Paul BERNARD</h4>
                                <p>Tennis</p>
                                <a href="#" class="btn btn-secondary">Voir profil</a>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-btn carousel-prev" onclick="moveCarousel(-1)">‹</button>
                    <button class="carousel-btn carousel-next" onclick="moveCarousel(1)">›</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Google Map et coordonnées -->
    <section class="localisation">
        <div class="container">
            <h2>Nous trouver</h2>
            <div class="location-content">
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.9916256937595!2d2.292292616145037!3d48.85837007928746!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e2964e34e2d%3A0x8ddca9ee380ef7e0!2sEiffel%20Tower!5e0!3m2!1sen!2sfr!4v1653301234567!5m2!1sen!2sfr" 
                            width="100%" 
                            height="350" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                <div class="contact-info">
                    <h3>Sportify Centre</h3>
                    <div class="contact-details">
                        <p><strong>📍</strong> 123 Rue du Sport, 75001 Paris</p>
                        <p><strong>📞</strong> <a href="tel:+33123456789">+33 1 23 45 67 89</a></p>
                        <p><strong>✉️</strong> <a href="mailto:contact@sportify.com">contact@sportify.com</a></p>
                    </div>
                    <div class="horaires">
                        <h4>Horaires d'ouverture</h4>
                        <p>Lundi - Vendredi : 7h00 - 22h00</p>
                        <p>Samedi - Dimanche : 8h00 - 20h00</p>
                    </div>
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
        // Script pour le carrousel
        let currentSlide = 0;
        const slides = document.querySelectorAll('.coach-card');
        const totalSlides = slides.length;

        function moveCarousel(direction) {
            currentSlide += direction;
            
            if (currentSlide >= totalSlides - 2) {
                currentSlide = totalSlides - 3;
            }
            if (currentSlide < 0) {
                currentSlide = 0;
            }
            
            const track = document.querySelector('.carousel-track');
            const slideWidth = slides[0].offsetWidth + 30; // largeur + gap
            track.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
        }

        // Animation au scroll
        window.addEventListener('scroll', () => {
            const elements = document.querySelectorAll('.bulletin-item, .coach-card');
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('animate-fade-in');
                }
            });
        });
    </script>
</body>
</html>