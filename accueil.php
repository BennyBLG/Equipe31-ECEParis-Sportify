<?php include('config.php'); ?>
<?php include('includes/header.php'); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sportify - Votre coach, votre rythme, votre succès</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header avec navigation -->
    <header>
        <div class="header-container">
            <h1>Sportify</h1>
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
    </header>
     <!-- Carrousel des coachs du mois -->
    <section class="carrousel">
        <h2>Nos Coachs du Mois</h2>
        <div class="carousel-images">
            <img src="images/coach1.jpg" alt="Coach 1">
            <img src="images/coach2.jpg" alt="Coach 2">
            <!-- Ajouter d'autres coachs -->
        </div>
    </section>

    <!-- Bulletin sportif de la semaine -->
    <section class="bulletin">
        <h2>Événement de la Semaine</h2>
        <div class="bulletin-content">
            <p><strong>Match de Rugby</strong> - Omnes Education vs. Visiteurs</p>
            <p><strong>Date :</strong> 25 Juin 2025</p>
            <p><strong>Lieu :</strong> Stade Omnes, Paris</p>
        </div>
    </section>

    <!-- Google Map et coordonnées du centre Sportify -->
    <section class="localisation">
        <h2>Nous Trouver</h2>
        <div class="map-container">
            <!-- Intégration de Google Map -->
            <iframe src="https://www.google.com/maps/embed?pb=..." width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
        <div class="contact-info">
            <p><strong>Sportify Centre</strong></p>
            <p>Adresse : 123 Rue du Sport, Paris</p>
            <p>Téléphone : +33 1 23 45 67 89</p>
            <p>Email : contact@sportify.com</p>
        </div>
    </section>

    <?php include('includes/footer.php'); ?>
    <script src="js/script.js"></script>
</body>
</html>


            <!-- Dans une Cocotte-Minute chaude avec de l'huile d'olive, faire revenir les morceaux de viande à feu vif et les saler. Les débarrasser une fois qu'ils sont bien colorés.
Dans la même cocotte, faire revenir les oignons et les lardons dans le beurre et ajouter du sel. Une fois les oignons brillants, ajouter les rondelles de carottes, l'ail haché, les champignons de Paris. Remuer pendant 1 min.
Remettre ensuite la viande dans la cocotte, puis ajouter la farine (singer) et cuire pendant 2 min tout en remuant (torréfier). Mouiller avec le vin rouge tout en grattant le fond de la cocotte pour récupérer tous les sucs, ajouter le jus de veau, la branche de thym et de laurier, puis fermer la Cocotte-Minute. Garder un feu vif jusqu'aux premiers sifflements de la cocotte, puis baisser le feu et laisser mijoter pendant 45 min. -->