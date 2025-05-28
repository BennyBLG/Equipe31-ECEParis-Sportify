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
            <h1 class="center-title">Sportify</h1>
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

    <!-- Reste de la page -->
    <section class="hero">
        <div class="hero-content">
            <h2>Bienvenue sur Sportify</h2>
            <p>Votre coach, votre rythme, votre succès</p>
            <a href="#parcourir" class="btn">Découvrez nos services</a>
        </div>
    </section>

    <!-- Section Événement de la semaine -->
    <section class="evenement">
        <h2>Événement de la semaine</h2>
        <div class="evenement-content">
            <p><strong>Match de Rugby</strong> - Omnes Education vs. Visiteurs</p>
            <p><strong>Date :</strong> 25 Juin 2025</p>
            <p><strong>Lieu :</strong> Stade Omnes, Paris</p>
        </div>
    </section>

    <!-- Section Bulletin Sportif de la semaine -->
    <section class="bulletin">
        <h2>Bulletin sportif de la semaine</h2>
        <div class="bulletin-content">
            <p><strong>Championnat universitaire</strong> : Résultats des dernières compétitions !</p>
            <p>Les étudiants de Sportify ont brillé dans les dernières épreuves de natation et de tennis. Découvrez toutes les nouvelles !</p>
        </div>
    </section>

    <!-- Section Google Map et coordonnées -->
    <section class="localisation">
        <h2>Nous trouver</h2>
        <div class="map-container">
            <!-- Réduction de la taille de la carte -->
            <iframe src="https://www.google.com/maps/embed?pb=..." width="500" height="350" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
        <div class="contact-info">
            <p><strong>Sportify Centre</strong></p>
            <p>Adresse : 123 Rue du Sport, Paris</p>
            <p>Téléphone : +33 1 23 45 67 89</p>
            <p>Email : contact@sportify.com</p>
        </div>
    </section>
    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Sportify - Tous droits réservés</p>
    </footer>

    <script src="js/script.js"></script>

</body>
</html>
