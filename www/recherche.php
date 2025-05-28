<?php
// Données de test (à remplacer par une vraie base de données plus tard)
$coachs = [
    ['id' => 1, 'nom' => 'DUMAIS', 'prenom' => 'Guy', 'specialite' => 'Musculation', 'type' => 'activite', 'photo' => 'coach-guy.jpg'],
    ['id' => 2, 'nom' => 'MARTIN', 'prenom' => 'Marie', 'specialite' => 'Fitness', 'type' => 'activite', 'photo' => 'coach-marie.jpg'],
    ['id' => 3, 'nom' => 'BERNARD', 'prenom' => 'Paul', 'specialite' => 'Biking', 'type' => 'activite', 'photo' => 'coach-paul.jpg'],
    ['id' => 4, 'nom' => 'DUBOIS', 'prenom' => 'Sophie', 'specialite' => 'Cardio-Training', 'type' => 'activite', 'photo' => 'coach-sophie.jpg'],
    ['id' => 5, 'nom' => 'MOREAU', 'prenom' => 'Jean', 'specialite' => 'Cours Collectifs', 'type' => 'activite', 'photo' => 'coach-jean.jpg'],
    ['id' => 6, 'nom' => 'LEBRUN', 'prenom' => 'Marc', 'specialite' => 'Basketball', 'type' => 'competition', 'photo' => 'coach-marc.jpg'],
    ['id' => 7, 'nom' => 'GARCIA', 'prenom' => 'Luc', 'specialite' => 'Football', 'type' => 'competition', 'photo' => 'coach-luc.jpg'],
    ['id' => 8, 'nom' => 'ROUX', 'prenom' => 'Pierre', 'specialite' => 'Rugby', 'type' => 'competition', 'photo' => 'coach-pierre.jpg'],
    ['id' => 9, 'nom' => 'BLANC', 'prenom' => 'Anna', 'specialite' => 'Tennis', 'type' => 'competition', 'photo' => 'coach-anna.jpg'],
    ['id' => 10, 'nom' => 'PETIT', 'prenom' => 'Julie', 'specialite' => 'Natation', 'type' => 'competition', 'photo' => 'coach-julie.jpg'],
    ['id' => 11, 'nom' => 'COLIN', 'prenom' => 'Thomas', 'specialite' => 'Plongeon', 'type' => 'competition', 'photo' => 'coach-thomas.jpg']
];

$services = [
    ['id' => 1, 'nom' => 'Salle de sport Omnes', 'description' => 'Équipements modernes et services personnalisés', 'type' => 'etablissement'],
    ['id' => 2, 'nom' => 'Personnels de la salle', 'description' => 'Équipe qualifiée à votre service', 'type' => 'service'],
    ['id' => 3, 'nom' => 'Règles d\'utilisation', 'description' => 'Guide d\'utilisation des équipements', 'type' => 'service'],
    ['id' => 4, 'nom' => 'Nouveaux clients', 'description' => 'Informations pour les nouveaux membres', 'type' => 'service'],
    ['id' => 5, 'nom' => 'Alimentation et nutrition', 'description' => 'Conseils nutritionnels personnalisés', 'type' => 'service']
];

// Traitement de la recherche
$resultats = [];
$terme_recherche = '';
$type_recherche = '';

if (isset($_GET['recherche']) && !empty($_GET['recherche'])) {
    $terme_recherche = htmlspecialchars($_GET['recherche']);
    $type_recherche = isset($_GET['type']) ? $_GET['type'] : 'tous';
    
    // Recherche dans les coachs
    if ($type_recherche == 'tous' || $type_recherche == 'coach') {
        foreach ($coachs as $coach) {
            $nom_complet = strtolower($coach['prenom'] . ' ' . $coach['nom']);
            $specialite = strtolower($coach['specialite']);
            $terme = strtolower($terme_recherche);
            
            if (strpos($nom_complet, $terme) !== false || strpos($specialite, $terme) !== false) {
                $resultats[] = [
                    'type' => 'coach',
                    'data' => $coach
                ];
            }
        }
    }
    
    // Recherche dans les services
    if ($type_recherche == 'tous' || $type_recherche == 'service') {
        foreach ($services as $service) {
            $nom_service = strtolower($service['nom']);
            $desc_service = strtolower($service['description']);
            $terme = strtolower($terme_recherche);
            
            if (strpos($nom_service, $terme) !== false || strpos($desc_service, $terme) !== false) {
                $resultats[] = [
                    'type' => 'service',
                    'data' => $service
                ];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche - Sportify</title>
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

    <!-- Section Hero -->
    <section class="hero hero-secondary">
        <div class="hero-content">
            <h1>Recherche</h1>
            <p class="hero-subtitle">Trouvez rapidement un coach, une spécialité ou un service</p>
        </div>
    </section>

    <!-- Section de recherche -->
    <section class="search-section">
        <div class="container">
            <div class="search-container">
                <h2>Que recherchez-vous ?</h2>
                
                <form method="GET" action="recherche.php" class="search-form">
                    <div class="search-input-container">
                        <input type="text" 
                               name="recherche" 
                               id="search-input"
                               placeholder="Rechercher un coach, une spécialité, un service..." 
                               value="<?php echo $terme_recherche; ?>"
                               required>
                        <button type="submit" class="search-btn">
                            <span>🔍</span> Rechercher
                        </button>
                    </div>
                    
                    <div class="search-filters">
                        <h3>Filtrer par type :</h3>
                        <div class="filter-options">
                            <label class="filter-option">
                                <input type="radio" name="type" value="tous" 
                                       <?php echo ($type_recherche == '' || $type_recherche == 'tous') ? 'checked' : ''; ?>>
                                <span class="radio-custom"></span>
                                Tout
                            </label>
                            
                            <label class="filter-option">
                                <input type="radio" name="type" value="coach" 
                                       <?php echo ($type_recherche == 'coach') ? 'checked' : ''; ?>>
                                <span class="radio-custom"></span>
                                Coachs
                            </label>
                            
                            <label class="filter-option">
                                <input type="radio" name="type" value="service" 
                                       <?php echo ($type_recherche == 'service') ? 'checked' : ''; ?>>
                                <span class="radio-custom"></span>
                                Services
                            </label>
                        </div>
                    </div>
                </form>

                <!-- Suggestions de recherche -->
                <div class="search-suggestions">
                    <h3>Suggestions populaires :</h3>
                    <div class="suggestions-tags">
                        <a href="?recherche=musculation&type=coach" class="suggestion-tag">Musculation</a>
                        <a href="?recherche=natation&type=coach" class="suggestion-tag">Natation</a>
                        <a href="?recherche=rugby&type=coach" class="suggestion-tag">Rugby</a>
                        <a href="?recherche=tennis&type=coach" class="suggestion-tag">Tennis</a>
                        <a href="?recherche=salle de sport&type=service" class="suggestion-tag">Salle de sport</a>
                        <a href="?recherche=fitness&type=coach" class="suggestion-tag">Fitness</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section des résultats -->
    <?php if (!empty($terme_recherche)): ?>
    <section class="results-section">
        <div class="container">
            <div class="results-header">
                <h2>Résultats de recherche</h2>
                <p class="results-info">
                    <?php echo count($resultats); ?> résultat(s) trouvé(s) pour 
                    "<strong><?php echo $terme_recherche; ?></strong>"
                </p>
            </div>

            <?php if (!empty($resultats)): ?>
                <div class="results-grid">
                    <?php foreach ($resultats as $resultat): ?>
                        <?php if ($resultat['type'] == 'coach'): ?>
                            <div class="result-card coach-result">
                                <div class="result-image">
                                    <img src="media/images/<?php echo $resultat['data']['photo']; ?>" 
                                         alt="<?php echo $resultat['data']['prenom'] . ' ' . $resultat['data']['nom']; ?>"
                                         onerror="this.src='https://via.placeholder.com/150x150/007BFF/ffffff?text=Coach'">
                                </div>
                                <div class="result-content">
                                    <div class="result-type">
                                        <span class="type-badge coach-badge">
                                            <?php echo $resultat['data']['type'] == 'competition' ? '🏆 Compétition' : '🏋️‍♂️ Activité'; ?>
                                        </span>
                                    </div>
                                    <h3><?php echo $resultat['data']['prenom'] . ' ' . $resultat['data']['nom']; ?></h3>
                                    <p class="result-specialty"><?php echo $resultat['data']['specialite']; ?></p>
                                    <p class="result-description">
                                        Coach professionnel en <?php echo strtolower($resultat['data']['specialite']); ?>
                                    </p>
                                    <div class="result-actions">
                                        <a href="coach_detail.php?id=<?php echo $resultat['data']['id']; ?>" 
                                           class="btn btn-primary">Voir le profil</a>
                                        <a href="reservation.php?coach=<?php echo $resultat['data']['id']; ?>" 
                                           class="btn btn-outline">Réserver</a>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="result-card service-result">
                                <div class="result-image service-icon">
                                    <span><?php echo $resultat['data']['type'] == 'etablissement' ? '🏢' : '⚙️'; ?></span>
                                </div>
                                <div class="result-content">
                                    <div class="result-type">
                                        <span class="type-badge service-badge">
                                            <?php echo $resultat['data']['type'] == 'etablissement' ? '🏢 Établissement' : '⚙️ Service'; ?>
                                        </span>
                                    </div>
                                    <h3><?php echo $resultat['data']['nom']; ?></h3>
                                    <p class="result-description"><?php echo $resultat['data']['description']; ?></p>
                                    <div class="result-actions">
                                        <a href="service_detail.php?id=<?php echo $resultat['data']['id']; ?>" 
                                           class="btn btn-primary">En savoir plus</a>
                                        <?php if ($resultat['data']['type'] == 'etablissement'): ?>
                                            <a href="reservation_visite.php" class="btn btn-outline">Visiter</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <div class="no-results-icon">🔍</div>
                    <h3>Aucun résultat trouvé</h3>
                    <p>Aucun résultat ne correspond à votre recherche "<strong><?php echo $terme_recherche; ?></strong>"</p>
                    <div class="no-results-suggestions">
                        <p>Suggestions :</p>
                        <ul>
                            <li>Vérifiez l'orthographe des mots-clés</li>
                            <li>Essayez des termes plus généraux</li>
                            <li>Utilisez des synonymes</li>
                            <li>Parcourez nos <a href="tout_parcourir.php">services disponibles</a></li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Section recherche avancée -->
    <section class="advanced-search">
        <div class="container">
            <h2>Recherche avancée</h2>
            <div class="advanced-grid">
                <div class="advanced-category">
                    <h3>🏋️‍♂️ Rechercher par activité</h3>
                    <div class="category-links">
                        <a href="?recherche=musculation&type=coach">Musculation</a>
                        <a href="?recherche=fitness&type=coach">Fitness</a>
                        <a href="?recherche=biking&type=coach">Biking</a>
                        <a href="?recherche=cardio&type=coach">Cardio-Training</a>
                        <a href="?recherche=cours collectifs&type=coach">Cours Collectifs</a>
                    </div>
                </div>

                <div class="advanced-category">
                    <h3>🏆 Rechercher par sport de compétition</h3>
                    <div class="category-links">
                        <a href="?recherche=basketball&type=coach">Basketball</a>
                        <a href="?recherche=football&type=coach">Football</a>
                        <a href="?recherche=rugby&type=coach">Rugby</a>
                        <a href="?recherche=tennis&type=coach">Tennis</a>
                        <a href="?recherche=natation&type=coach">Natation</a>
                        <a href="?recherche=plongeon&type=coach">Plongeon</a>
                    </div>
                </div>

                <div class="advanced-category">
                    <h3>🏢 Rechercher par service</h3>
                    <div class="category-links">
                        <a href="?recherche=salle de sport&type=service">Salle de sport</a>
                        <a href="?recherche=personnels&type=service">Personnels</a>
                        <a href="?recherche=règles&type=service">Règles d'utilisation</a>
                        <a href="?recherche=nutrition&type=service">Nutrition</a>
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
        // Auto-focus sur le champ de recherche
        document.getElementById('search-input').focus();

        // Soumission du formulaire en appuyant sur Entrée
        document.getElementById('search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.closest('form').submit();
            }
        });

        // Animation des résultats
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.result-card').forEach(card => {
            observer.observe(card);
        });

        // Highlight des termes recherchés
        function highlightSearchTerms() {
            const searchTerm = "<?php echo $terme_recherche; ?>";
            if (searchTerm) {
                const results = document.querySelectorAll('.result-content h3, .result-specialty, .result-description');
                results.forEach(element => {
                    const regex = new RegExp(`(${searchTerm})`, 'gi');
                    element.innerHTML = element.innerHTML.replace(regex, '<mark>$1</mark>');
                });
            }
        }

        // Appliquer le highlighting si on a des résultats
        <?php if (!empty($terme_recherche)): ?>
        document.addEventListener('DOMContentLoaded', highlightSearchTerms);
        <?php endif; ?>
    </script>
</body>
</html>