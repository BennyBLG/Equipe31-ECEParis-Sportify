<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sportify - Votre coach, votre rythme, votre succès</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Rectangle supérieur avec menu aligné -->
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
            <h2>Bienvenue sur Sportify</h2>
            <p>Votre coach, votre rythme, votre succès</p>
            <a href="#parcourir" class="btn">Découvrez nos services</a>
        </div>
    </section>

    <!-- Section Événement de la semaine -->
    <section class="evenement">
        <div class="section-container">
            <div class="evenement-text">
                <h2>Événement de la semaine</h2>
                <div class="evenement-content">
                    <p><strong>Match de Rugby</strong> - ECE vs. ESILV</p>
                    <p><strong>Date :</strong> 25 Juin 2025</p>
                    <p><strong>Lieu :</strong> Stade Léonien, Paris</p>
                </div>
            </div>
            <div class="evenement-image">
                <!-- Ajout de l'image à droite -->
                <img src="stade_l.jpg" alt="Événement de la semaine" class="evenement-img">
            </div>
        </div>
    </section>

    <!-- Section "Qui sommes-nous" -->
    <section class="qui-sommes-nous">
        <div class="section-container">
            <h2>Qui sommes-nous ?</h2>
            <p>Sportify est une plateforme innovante qui révolutionne l'accès au sport en ligne. Nous offrons une expérience personnalisée, où chaque utilisateur peut trouver un coach spécialisé, suivre des événements sportifs en direct, et bénéficier de conseils adaptés à ses besoins et à son niveau.
Que vous soyez un débutant souhaitant améliorer sa condition physique ou un sportif confirmé cherchant à perfectionner sa technique, Sportify met à votre disposition une équipe de coachs professionnels, experts dans divers domaines du sport, prêts à vous accompagner tout au long de votre parcours.</p>
            <p>Au-delà de la simple consultation sportive, Sportify propose également une variété d'événements sportifs en ligne et en personne, créant ainsi une véritable communauté d'amateurs et de passionnés de sport. Notre objectif est de rendre le sport accessible à tous, sans distinction, tout en cultivant la motivation et la persévérance nécessaires pour atteindre vos objectifs.

Nous croyons que le sport doit être un levier d’épanouissement personnel et un moyen de créer des liens. C’est pourquoi nous nous engageons à offrir une plateforme conviviale, dynamique et inclusive, où chaque utilisateur, qu’il soit chez lui ou sur le terrain, peut se dépasser, se divertir et réussir.</p>
        </div>
    </section>

    <!-- Section Bulletin Sportif de la semaine -->
    <section class="bulletin">
        <div class="section-container">
            <h2>Bulletin sportif de la semaine</h2>
            <div class="bulletin-content">
                <p><strong>Championnat universitaire</strong> : Résultats des dernières compétitions !</p>
                <p>Les étudiants de Sportify ont brillé dans les dernières épreuves de natation et de tennis. Découvrez toutes les nouvelles !</p>
            </div>
        </div>
    </section>

    <!-- Section Carrousel -->
    <section class="carrousel">
        <div class="section-container">
            <h2>Carrousel des coachs</h2>
            <div class="carrousel-content">
                <p>Prochainement, nous ajouterons des images de nos coachs sportifs ici.</p>
            </div>
        </div>
    </section>

    <!-- Section Google Map et coordonnées -->
    <section class="localisation">
        <div class="section-container">
            <!-- Titre "Nous Trouver" centré -->
            <h2 class="section-title">Nous Trouver</h2>
            <div class="map-info-container">
                <!-- Carte centrée -->
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=..." width="500" height="350" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>

                <!-- Informations sous la carte, centrées -->
                <div class="contact-info">
                    <p><strong>Sportify Centre</strong></p>
                    <p>Adresse : 123 Rue du Sport, Paris</p>
                    <p>Téléphone : +33 1 23 45 67 89</p>
                    <p>Email : contact@sportify.com</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Sportify - Tous droits réservés</p>
    </footer>

    <script src="js/script.js"></script>

</body>
</html>
