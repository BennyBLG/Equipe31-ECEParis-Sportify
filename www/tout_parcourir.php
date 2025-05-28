<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tout Parcourir - Sportify</title>
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

    <!-- Section d'introduction -->
    <section class="hero">
        <div class="hero-content">
            <h2>Trouvez l'activité qui vous correspond</h2>
            <p>Explorez nos services et choisissez celui qui vous convient le mieux.</p>
        </div>
    </section>

    <!-- Section Catégories -->
    <section class="categories">
        <div class="section-container">
            <h2>Choisissez une catégorie</h2>
            <div class="categories-list">
                <div class="category-item">
                    <h3><a href="#activites-sportives">Activités sportives</a></h3>
                    <p>Découvrez les activités disponibles pour tous les niveaux.</p>
                </div>
                <div class="category-item">
                    <h3><a href="#sports-competitions">Sports de compétition</a></h3>
                    <p>Rejoignez nos équipes de compétition et améliorez vos performances.</p>
                </div>
                <div class="category-item">
                    <h3><a href="#salle-sport">Salle de sport Omnes</a></h3>
                    <p>Accédez à nos équipements et services dans la salle de sport Omnes.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Activités sportives -->
    <section id="activites-sportives" class="activites-sportives">
        <div class="section-container">
            <h2>Activités sportives</h2>
            <div class="activity-list">
                <div class="activity-item">
                    <h3>Musculation</h3>
                    <p>Suivez un programme de musculation personnalisé avec nos coachs professionnels.</p>
                    <button>Voir le coach</button>
                </div>
                <div class="activity-item">
                    <h3>Fitness</h3>
                    <p>Participez à nos séances de fitness dynamiques et motivantes.</p>
                    <button>Voir le coach</button>
                </div>
                <div class="activity-item">
                    <h3>Biking</h3>
                    <p>Rejoignez nos cours de biking et faites du sport de manière ludique.</p>
                    <button>Voir le coach</button>
                </div>
                <div class="activity-item">
                    <h3>Cardio-Training</h3>
                    <p>Améliorez votre condition physique avec nos coachs spécialisés en cardio-training.</p>
                    <button>Voir le coach</button>
                </div>
                <div class="activity-item">
                    <h3>Cours Collectifs</h3>
                    <p>Participez à des cours collectifs pour une expérience sportive enrichissante.</p>
                    <button>Voir le coach</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Sports de compétition -->
    <section id="sports-competitions" class="sports-competitions">
        <div class="section-container">
            <h2>Sports de compétition</h2>
            <div class="sport-item">
                <h3>Basketball</h3>
                <button>Voir le coach</button>
            </div>
            <div class="sport-item">
                <h3>Football</h3>
                <button>Voir le coach</button>
            </div>
            <div class="sport-item">
                <h3>Rugby</h3>
                <button>Voir le coach</button>
            </div>
            <div class="sport-item">
                <h3>Tennis</h3>
                <button>Voir le coach</button>
            </div>
            <div class="sport-item">
                <h3>Natation</h3>
                <button>Voir le coach</button>
            </div>
            <div class="sport-item">
                <h3>Plongeon</h3>
                <button>Voir le coach</button>
            </div>
        </div>
    </section>

    <!-- Section Salle de sport Omnes -->
    <section id="salle-sport" class="salle-sport">
        <div class="section-container">
            <h2>Salle de sport Omnes</h2>
            <div class="salle-info">
                <p><strong>Horaires d'ouverture :</strong> 7h00 - 22h00</p>
                <p><strong>Services disponibles :</strong> Musculation, Cardio, Yoga, et plus encore.</p>
                <p><strong>Coordonnées :</strong> Contactez le responsable de la salle au +33 1 23 45 67 89</p>
            </div>
            <button>Prendre rendez-vous pour une visite</button>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Sportify - Tous droits réservés</p>
    </footer>

    <script src="js/script.js"></script>

</body>
</html>
