<?php
session_start();

// Données simulées - même structure que l'original mais avec plus de détails
$coachs = [
    ['id' => 1, 'nom' => 'DUMAIS', 'prenom' => 'Guy', 'specialite' => 'Musculation', 'type' => 'activite', 'photo' => 'coach-guy.jpg', 'experience' => '5 ans', 'note' => 4.8, 'prix' => 35, 'disponible' => true],
    ['id' => 2, 'nom' => 'MARTIN', 'prenom' => 'Marie', 'specialite' => 'Fitness', 'type' => 'activite', 'photo' => 'coach-marie.jpg', 'experience' => '3 ans', 'note' => 4.6, 'prix' => 30, 'disponible' => true],
    ['id' => 3, 'nom' => 'BERNARD', 'prenom' => 'Paul', 'specialite' => 'Tennis', 'type' => 'competition', 'photo' => 'coach-paul.jpg', 'experience' => '7 ans', 'note' => 4.9, 'prix' => 40, 'disponible' => false],
    ['id' => 4, 'nom' => 'DUBOIS', 'prenom' => 'Sophie', 'specialite' => 'Cardio-Training', 'type' => 'activite', 'photo' => 'coach-sophie.jpg', 'experience' => '4 ans', 'note' => 4.5, 'prix' => 25, 'disponible' => true],
    ['id' => 5, 'nom' => 'MOREAU', 'prenom' => 'Jean', 'specialite' => 'Cours Collectifs', 'type' => 'activite', 'photo' => 'coach-jean.jpg', 'experience' => '6 ans', 'note' => 4.7, 'prix' => 20, 'disponible' => true],
];

$services = [
    ['id' => 1, 'nom' => 'Salle de sport Omnes', 'description' => 'Équipements modernes et services personnalisés', 'type' => 'etablissement', 'horaires' => '7h-22h', 'prix' => 0],
    ['id' => 2, 'nom' => 'Personnels de la salle', 'description' => 'Équipe qualifiée à votre service', 'type' => 'service', 'horaires' => '7h-22h', 'prix' => 0],
    ['id' => 3, 'nom' => 'Règles d\'utilisation', 'description' => 'Guide d\'utilisation des équipements', 'type' => 'service', 'horaires' => '24h/24', 'prix' => 0],
    ['id' => 4, 'nom' => 'Nouveaux clients', 'description' => 'Informations pour les nouveaux membres', 'type' => 'service', 'horaires' => '9h-18h', 'prix' => 0],
    ['id' => 5, 'nom' => 'Alimentation et nutrition', 'description' => 'Conseils nutritionnels personnalisés', 'type' => 'service', 'horaires' => '10h-17h', 'prix' => 25]
];

// Traitement de la recherche avec logique améliorée
$resultats = [];
$terme_recherche = '';
$type_recherche = '';
$filtre_prix = '';
$filtre_note = '';
$filtre_disponibilite = '';

if (isset($_GET['recherche']) && !empty($_GET['recherche'])) {
    $terme_recherche = htmlspecialchars($_GET['recherche']);
    $type_recherche = isset($_GET['type']) ? $_GET['type'] : 'tous';
    $filtre_prix = isset($_GET['prix_max']) ? (int)$_GET['prix_max'] : '';
    $filtre_note = isset($_GET['note_min']) ? (float)$_GET['note_min'] : '';
    $filtre_disponibilite = isset($_GET['disponible']) ? $_GET['disponible'] : '';
    
    // Recherche dans les coachs
    if ($type_recherche == 'tous' || $type_recherche == 'coach') {
        foreach ($coachs as $coach) {
            $nom_complet = strtolower($coach['prenom'] . ' ' . $coach['nom']);
            $specialite = strtolower($coach['specialite']);
            $terme = strtolower($terme_recherche);
            
            $match = strpos($nom_complet, $terme) !== false || strpos($specialite, $terme) !== false;
            
            // Appliquer les filtres
            if ($match) {
                if ($filtre_prix && $coach['prix'] > $filtre_prix) continue;
                if ($filtre_note && $coach['note'] < $filtre_note) continue;
                if ($filtre_disponibilite === 'oui' && !$coach['disponible']) continue;
                if ($filtre_disponibilite === 'non' && $coach['disponible']) continue;
                
                $resultats[] = ['type' => 'coach', 'data' => $coach];
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
                if ($filtre_prix && $service['prix'] > $filtre_prix) continue;
                
                $resultats[] = ['type' => 'service', 'data' => $service];
            }
        }
    }
    
    // Tri des résultats par pertinence (score basé sur note et disponibilité)
    usort($resultats, function($a, $b) {
        if ($a['type'] == 'coach' && $b['type'] == 'coach') {
            $scoreA = $a['data']['note'] + ($a['data']['disponible'] ? 1 : 0);
            $scoreB = $b['data']['note'] + ($b['data']['disponible'] ? 1 : 0);
            return $scoreB <=> $scoreA;
        }
        return 0;
    });
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche Avancée - Sportify</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .search-filters-advanced {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .filter-row {
            display: flex;
            gap: 20px;
            margin: 15px 0;
        }
        .filter-group {
            flex: 1;
        }
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        .filter-group input, .filter-group select {
            width: 100%;
            padding: 8px 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            transition: border-color 0.3s;
        }
        .filter-group input:focus, .filter-group select:focus {
            border-color: #007BFF;
            outline: none;
        }
        .search-stats {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .coach-badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 12px;
            border-radius: 20px;
            margin: 2px;
        }
        .badge-disponible { background: #d4edda; color: #155724; }
        .badge-occupe { background: #f8d7da; color: #721c24; }
        .badge-premium { background: #fff3cd; color: #856404; }
        .result-highlight {
            background: linear-gradient(45deg, #fff, #f0f8ff);
            border-left: 4px solid #007BFF;
        }
        .price-tag {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 14px;
        }
        .rating-stars {
            color: #ffc107;
        }
        .quick-filters {
            display: flex;
            gap: 10px;
            margin: 15px 0;
            flex-wrap: wrap;
        }
        .quick-filter-btn {
            background: #fff;
            border: 2px solid #007BFF;
            color: #007BFF;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .quick-filter-btn:hover, .quick-filter-btn.active {
            background: #007BFF;
            color: white;
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

    <!-- Section Hero -->
    <section class="hero hero-secondary">
        <div class="hero-content">
            <h1>🔍 Recherche Avancée</h1>
            <p class="hero-subtitle">Trouvez exactement ce que vous cherchez avec nos filtres intelligents</p>
        </div>
    </section>

    <!-- Section de recherche améliorée -->
    <section class="search-section">
        <div class="container">
            <div class="search-container">
                <h2>🎯 Recherche Intelligente</h2>
                
                <form method="GET" action="recherche.php" class="search-form" id="searchForm">
                    <div class="search-input-container">
                        <input type="text" 
                               name="recherche" 
                               id="search-input"
                               placeholder="🔍 Coach, spécialité, service..." 
                               value="<?php echo $terme_recherche; ?>"
                               required>
                        <button type="submit" class="search-btn">
                            <span>🚀</span> Rechercher
                        </button>
                    </div>

                    <!-- Filtres rapides -->
                    <div class="quick-filters">
                        <button type="button" class="quick-filter-btn" onclick="setQuickSearch('musculation')">💪 Musculation</button>
                        <button type="button" class="quick-filter-btn" onclick="setQuickSearch('tennis')">🎾 Tennis</button>
                        <button type="button" class="quick-filter-btn" onclick="setQuickSearch('natation')">🏊 Natation</button>
                        <button type="button" class="quick-filter-btn" onclick="setQuickSearch('fitness')">🏃 Fitness</button>
                        <button type="button" class="quick-filter-btn" onclick="setQuickSearch('salle')">🏢 Salle</button>
                    </div>
                    
                    <!-- Filtres avancés -->
                    <div class="search-filters-advanced">
                        <h3>🎛️ Filtres Avancés</h3>
                        
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="type">Type de service</label>
                                <select name="type" id="type">
                                    <option value="tous" <?php echo ($type_recherche == '' || $type_recherche == 'tous') ? 'selected' : ''; ?>>🌟 Tout</option>
                                    <option value="coach" <?php echo ($type_recherche == 'coach') ? 'selected' : ''; ?>>👨‍🏫 Coachs</option>
                                    <option value="service" <?php echo ($type_recherche == 'service') ? 'selected' : ''; ?>>⚙️ Services</option>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="prix_max">Prix maximum (€)</label>
                                <input type="number" name="prix_max" id="prix_max" min="0" max="100" 
                                       value="<?php echo $filtre_prix; ?>" placeholder="Ex: 35">
                            </div>
                            
                            <div class="filter-group">
                                <label for="note_min">Note minimum</label>
                                <select name="note_min" id="note_min">
                                    <option value="">Toutes les notes</option>
                                    <option value="4.5" <?php echo ($filtre_note == '4.5') ? 'selected' : ''; ?>>⭐ 4.5+ Excellent</option>
                                    <option value="4.0" <?php echo ($filtre_note == '4.0') ? 'selected' : ''; ?>>⭐ 4.0+ Très bon</option>
                                    <option value="3.5" <?php echo ($filtre_note == '3.5') ? 'selected' : ''; ?>>⭐ 3.5+ Bon</option>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="disponible">Disponibilité</label>
                                <select name="disponible" id="disponible">
                                    <option value="">Tous</option>
                                    <option value="oui" <?php echo ($filtre_disponibilite == 'oui') ? 'selected' : ''; ?>>✅ Disponible maintenant</option>
                                    <option value="non" <?php echo ($filtre_disponibilite == 'non') ? 'selected' : ''; ?>>⏰ Occupé</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Suggestions améliorées -->
                <div class="search-suggestions">
                    <h3>💡 Suggestions Populaires</h3>
                    <div class="suggestions-tags">
                        <a href="?recherche=musculation&type=coach&disponible=oui" class="suggestion-tag">💪 Musculation Dispo</a>
                        <a href="?recherche=tennis&note_min=4.5" class="suggestion-tag">🎾 Tennis Top Rated</a>
                        <a href="?recherche=fitness&prix_max=25" class="suggestion-tag">🏃 Fitness Économique</a>
                        <a href="?recherche=natation&type=coach" class="suggestion-tag">🏊 Natation Pro</a>
                        <a href="?recherche=salle&type=service" class="suggestion-tag">🏢 Services Salle</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section des résultats améliorée -->
    <?php if (!empty($terme_recherche)): ?>
    <section class="results-section">
        <div class="container">
            <div class="search-stats">
                <h2>📊 Résultats de Recherche</h2>
                <p><strong><?php echo count($resultats); ?> résultat(s)</strong> trouvé(s) pour "<strong><?php echo $terme_recherche; ?></strong>"</p>
                <?php if ($filtre_prix): ?>
                    <span class="coach-badge badge-premium">Prix max: <?php echo $filtre_prix; ?>€</span>
                <?php endif; ?>
                <?php if ($filtre_note): ?>
                    <span class="coach-badge badge-premium">Note min: <?php echo $filtre_note; ?>⭐</span>
                <?php endif; ?>
                <?php if ($filtre_disponibilite): ?>
                    <span class="coach-badge <?php echo $filtre_disponibilite == 'oui' ? 'badge-disponible' : 'badge-occupe'; ?>">
                        <?php echo $filtre_disponibilite == 'oui' ? '✅ Disponibles uniquement' : '⏰ Occupés uniquement'; ?>
                    </span>
                <?php endif; ?>
            </div>

            <?php if (!empty($resultats)): ?>
                <div class="results-grid">
                    <?php foreach ($resultats as $index => $resultat): ?>
                        <?php if ($resultat['type'] == 'coach'): ?>
                            <div class="result-card coach-result <?php echo $index < 3 ? 'result-highlight' : ''; ?>">
                                <div class="result-image">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($resultat['data']['prenom'] . '+' . $resultat['data']['nom']); ?>&size=150&background=<?php echo $resultat['data']['specialite'] == 'Musculation' ? 'FF6B6B' : ($resultat['data']['specialite'] == 'Fitness' ? '4ECDC4' : ($resultat['data']['specialite'] == 'Tennis' ? '45B7D1' : ($resultat['data']['specialite'] == 'Cardio-Training' ? 'F093FB' : '4568DC'))); ?>&color=ffffff&font-size=0.4&bold=true" 
                                         alt="<?php echo $resultat['data']['prenom'] . ' ' . $resultat['data']['nom']; ?>"
                                         onerror="this.src='https://via.placeholder.com/150x150/007BFF/ffffff?text=Coach'"
                                    <?php if ($resultat['data']['disponible']): ?>
                                        <div class="availability-indicator available">🟢</div>
                                    <?php else: ?>
                                        <div class="availability-indicator busy">🔴</div>
                                    <?php endif; ?>
                                </div>
                                <div class="result-content">
                                    <div class="result-header">
                                        <div class="result-type">
                                            <span class="type-badge coach-badge">
                                                <?php echo $resultat['data']['type'] == 'competition' ? '🏆 Compétition' : '🏋️‍♂️ Activité'; ?>
                                            </span>
                                            <?php if ($resultat['data']['disponible']): ?>
                                                <span class="coach-badge badge-disponible">✅ Disponible</span>
                                            <?php else: ?>
                                                <span class="coach-badge badge-occupe">⏰ Occupé</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="price-tag">€<?php echo $resultat['data']['prix']; ?></div>
                                    </div>
                                    
                                    <h3><?php echo $resultat['data']['prenom'] . ' ' . $resultat['data']['nom']; ?></h3>
                                    <p class="result-specialty">🎯 <?php echo $resultat['data']['specialite']; ?></p>
                                    
                                    <div class="coach-stats">
                                        <div class="rating">
                                            <span class="rating-stars">
                                                <?php 
                                                $note = $resultat['data']['note'];
                                                $stars = str_repeat('⭐', floor($note));
                                                echo $stars . ' ' . $note . '/5';
                                                ?>
                                            </span>
                                        </div>
                                        <p class="experience">📅 <?php echo $resultat['data']['experience']; ?> d'expérience</p>
                                    </div>
                                    
                                    <div class="result-actions">
                                        <a href="coach_detail.php?id=<?php echo $resultat['data']['id']; ?>" 
                                           class="btn btn-primary">👁️ Voir Profil</a>
                                        <?php if ($resultat['data']['disponible']): ?>
                                            <a href="rendez_vous.php?coach=<?php echo $resultat['data']['id']; ?>" 
                                               class="btn btn-outline">📅 Réserver</a>
                                        <?php else: ?>
                                            <span class="btn btn-disabled">⏰ Indisponible</span>
                                        <?php endif; ?>
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
                                        <?php if ($resultat['data']['prix'] > 0): ?>
                                            <div class="price-tag">€<?php echo $resultat['data']['prix']; ?></div>
                                        <?php else: ?>
                                            <span class="coach-badge badge-disponible">🆓 Gratuit</span>
                                        <?php endif; ?>
                                    </div>
                                    <h3><?php echo $resultat['data']['nom']; ?></h3>
                                    <p class="result-description">📝 <?php echo $resultat['data']['description']; ?></p>
                                    <p class="service-hours">🕐 Horaires: <?php echo $resultat['data']['horaires']; ?></p>
                                    <div class="result-actions">
                                        <a href="service_detail.php?id=<?php echo $resultat['data']['id']; ?>" 
                                           class="btn btn-primary">ℹ️ En savoir plus</a>
                                        <?php if ($resultat['data']['type'] == 'etablissement'): ?>
                                            <a href="rendez_vous.php?service=<?php echo $resultat['data']['id']; ?>" 
                                               class="btn btn-outline">📅 Visiter</a>
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
                    <p>Aucun résultat ne correspond à vos critères de recherche</p>
                    <div class="no-results-suggestions">
                        <p><strong>💡 Suggestions :</strong></p>
                        <ul>
                            <li>• Vérifiez l'orthographe des mots-clés</li>
                            <li>• Essayez des termes plus généraux</li>
                            <li>• Réduisez les filtres appliqués</li>
                            <li>• Parcourez nos <a href="tout_parcourir.php">services disponibles</a></li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Sportify - Votre plateforme sportive intelligente</p>
        </div>
    </footer>

    <script>
        // Fonctions JavaScript améliorées
        function setQuickSearch(term) {
            document.getElementById('search-input').value = term;
            document.getElementById('searchForm').submit();
        }

        // Auto-focus sur le champ de recherche
        document.getElementById('search-input').focus();

        // Recherche en temps réel (simulée)
        let searchTimeout;
        document.getElementById('search-input').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                // Ici vous pourriez implémenter une recherche AJAX en temps réel
                console.log('Recherche en temps réel:', this.value);
            }, 500);
        });

        // Animation des résultats
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.result-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });

        // Highlight des termes recherchés
        function highlightSearchTerms() {
            const searchTerm = "<?php echo $terme_recherche; ?>";
            if (searchTerm) {
                const results = document.querySelectorAll('.result-content h3, .result-specialty, .result-description');
                results.forEach(element => {
                    const regex = new RegExp(`(${searchTerm})`, 'gi');
                    element.innerHTML = element.innerHTML.replace(regex, '<mark style="background: #ffeb3b; padding: 2px 4px; border-radius: 3px;">$1</mark>');
                });
            }
        }

        // Appliquer le highlighting si on a des résultats
        <?php if (!empty($terme_recherche)): ?>
        document.addEventListener('DOMContentLoaded', highlightSearchTerms);
        <?php endif; ?>

        // Gestion des filtres rapides
        document.querySelectorAll('.quick-filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.quick-filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>