-- ========================================
-- BASE DE DONNÉES SPORTIFY - VERSION CORRIGÉE
-- ========================================

-- Supprimer et recréer la base
DROP DATABASE IF EXISTS sportify;
CREATE DATABASE sportify CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sportify;

-- Table principale des utilisateurs (EMAIL RACCOURCI)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(191) UNIQUE NOT NULL,  -- 191 au lieu de 255 pour éviter l'erreur
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'coach', 'client') NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20),
    statut ENUM('actif', 'inactif') DEFAULT 'actif',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des clients
CREATE TABLE clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    adresse TEXT,
    ville VARCHAR(100),
    code_postal VARCHAR(10),
    pays VARCHAR(100) DEFAULT 'France',
    carte_etudiant VARCHAR(50),
    date_naissance DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des coachs
CREATE TABLE coachs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    specialite VARCHAR(100) NOT NULL,
    bureau VARCHAR(50),
    experience_annees INT DEFAULT 0,
    description TEXT,
    photo VARCHAR(255),
    video VARCHAR(255),
    cv_xml LONGTEXT,
    note_moyenne DECIMAL(3,2) DEFAULT 0.00,
    nb_evaluations INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des activités sportives
CREATE TABLE activites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    type ENUM('activite_sportive', 'sport_competition') NOT NULL,
    prix DECIMAL(6,2) NOT NULL,
    duree_minutes INT NOT NULL,
    niveau ENUM('debutant', 'intermediaire', 'avance', 'tous') DEFAULT 'tous',
    statut ENUM('actif', 'inactif') DEFAULT 'actif',
    ordre INT DEFAULT 0
);

-- Table de liaison coach-activités
CREATE TABLE coach_activites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    coach_id INT NOT NULL,
    activite_id INT NOT NULL,
    FOREIGN KEY (coach_id) REFERENCES coachs(id) ON DELETE CASCADE,
    FOREIGN KEY (activite_id) REFERENCES activites(id) ON DELETE CASCADE,
    UNIQUE KEY unique_coach_activite (coach_id, activite_id)
);

-- Table des disponibilités
CREATE TABLE disponibilites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    coach_id INT NOT NULL,
    jour_semaine ENUM('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche') NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    statut ENUM('disponible', 'occupe', 'pause') DEFAULT 'disponible',
    FOREIGN KEY (coach_id) REFERENCES coachs(id) ON DELETE CASCADE
);

-- Table des rendez-vous
CREATE TABLE rendezvous (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_id INT NOT NULL,
    coach_id INT NOT NULL,
    activite_id INT,
    date_rdv DATE NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    lieu VARCHAR(255) NOT NULL,
    statut ENUM('planifie', 'confirme', 'en_cours', 'termine', 'annule') DEFAULT 'planifie',
    prix DECIMAL(6,2) NOT NULL,
    notes_coach TEXT,
    notes_client TEXT,
    evaluation_client INT,
    commentaire_client TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (coach_id) REFERENCES coachs(id) ON DELETE CASCADE,
    FOREIGN KEY (activite_id) REFERENCES activites(id) ON DELETE SET NULL
);

-- Table de la salle de sport
CREATE TABLE salle_sport (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL DEFAULT 'Salle de sport Omnes',
    adresse TEXT NOT NULL,
    telephone VARCHAR(20),
    email VARCHAR(191),  -- 191 au lieu de 255
    horaires_json TEXT,  -- TEXT au lieu de JSON pour compatibilité
    services_json TEXT,  -- TEXT au lieu de JSON pour compatibilité
    regles TEXT,
    tarifs_json TEXT,    -- TEXT au lieu de JSON pour compatibilité
    capacite_max INT DEFAULT 50,
    statut ENUM('ouvert', 'ferme', 'maintenance') DEFAULT 'ouvert'
);

-- Table des événements
CREATE TABLE evenements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    date_evenement DATE NOT NULL,
    heure_debut TIME,
    heure_fin TIME,
    lieu VARCHAR(255),
    image VARCHAR(255),
    statut ENUM('actif', 'inactif', 'termine') DEFAULT 'actif',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des bulletins
CREATE TABLE bulletins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    auteur_id INT,
    date_publication DATE,
    statut ENUM('brouillon', 'publie', 'archive') DEFAULT 'brouillon',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auteur_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des messages
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    expediteur_id INT NOT NULL,
    destinataire_id INT NOT NULL,
    sujet VARCHAR(255),
    contenu TEXT NOT NULL,
    type ENUM('texto', 'email', 'notification') DEFAULT 'texto',
    statut ENUM('envoye', 'lu', 'archive') DEFAULT 'envoye',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expediteur_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (destinataire_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ========================================
-- INSERTION DES DONNÉES DE TEST
-- ========================================

-- Utilisateurs (mot de passe: password123 pour tous)
INSERT INTO users (email, password, role, nom, prenom, telephone) VALUES
('admin@sportify.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'ADMIN', 'Sportify', '+33123456789'),
('guy.dumais@sportify.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'coach', 'DUMAIS', 'Guy', '+33987654321'),
('marie.martin@sportify.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'coach', 'MARTIN', 'Marie', '+33567891234'),
('paul.bernard@sportify.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'coach', 'BERNARD', 'Paul', '+33456789123'),
('sophie.dubois@sportify.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'coach', 'DUBOIS', 'Sophie', '+33345678912'),
('client@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', 'DUPONT', 'Jean', '+33234567891'),
('marie.client@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', 'DURAND', 'Marie', '+33123456789');

-- Coachs
INSERT INTO coachs (user_id, specialite, bureau, experience_annees, description, note_moyenne, nb_evaluations) VALUES
(2, 'Musculation', 'Bureau 12', 5, 'Coach expérimenté en musculation', 4.8, 127),
(3, 'Fitness', 'Bureau 15', 3, 'Spécialiste fitness et cardio', 4.6, 89),
(4, 'Tennis', 'Court tennis', 7, 'Professeur de tennis certifié', 4.9, 156),
(5, 'Cardio-Training', 'Studio Cardio', 4, 'Experte en cardio-training', 4.5, 73);

-- Clients
INSERT INTO clients (user_id, adresse, ville, code_postal, carte_etudiant) VALUES
(6, '123 Rue Test', 'Paris', '75001', 'ETU2025001'),
(7, '456 Avenue Test', 'Lyon', '69001', 'ETU2025002');

-- Activités
INSERT INTO activites (nom, description, type, prix, duree_minutes, niveau, ordre) VALUES
('Musculation', 'Séance de musculation personnalisée', 'activite_sportive', 35.00, 60, 'tous', 1),
('Fitness', 'Cours de fitness dynamique', 'activite_sportive', 30.00, 45, 'tous', 2),
('Biking', 'Cours de vélo en salle', 'activite_sportive', 25.00, 45, 'tous', 3),
('Cardio-Training', 'Entraînement cardiovasculaire', 'activite_sportive', 25.00, 30, 'tous', 4),
('Cours Collectifs', 'Séances de groupe', 'activite_sportive', 20.00, 45, 'tous', 5),
('Basketball', 'Basketball compétitif', 'sport_competition', 35.00, 90, 'intermediaire', 1),
('Football', 'Football de compétition', 'sport_competition', 35.00, 90, 'intermediaire', 2),
('Rugby', 'Rugby de haut niveau', 'sport_competition', 40.00, 90, 'avance', 3),
('Tennis', 'Tennis de compétition', 'sport_competition', 40.00, 60, 'tous', 4),
('Natation', 'Natation compétitive', 'sport_competition', 30.00, 60, 'intermediaire', 5),
('Plongeon', 'Plongeon artistique', 'sport_competition', 45.00, 60, 'avance', 6);

-- Liaison coach-activités
INSERT INTO coach_activites (coach_id, activite_id) VALUES
(1, 1), (1, 5), -- Guy: Musculation + Cours collectifs
(2, 2), (2, 4), (2, 5), -- Marie: Fitness + Cardio + Cours collectifs
(3, 9), -- Paul: Tennis
(4, 4), (4, 3); -- Sophie: Cardio + Biking

-- Disponibilités Guy
INSERT INTO disponibilites (coach_id, jour_semaine, heure_debut, heure_fin) VALUES
(1, 'lundi', '09:00:00', '12:00:00'), (1, 'lundi', '14:00:00', '18:00:00'),
(1, 'mardi', '08:00:00', '12:00:00'), (1, 'mardi', '13:00:00', '17:00:00'),
(1, 'mercredi', '10:00:00', '16:00:00'),
(1, 'jeudi', '09:00:00', '12:00:00'), (1, 'jeudi', '14:00:00', '19:00:00'),
(1, 'vendredi', '08:00:00', '15:00:00'), (1, 'samedi', '09:00:00', '13:00:00');

-- Disponibilités Marie
INSERT INTO disponibilites (coach_id, jour_semaine, heure_debut, heure_fin) VALUES
(2, 'lundi', '08:00:00', '12:00:00'), (2, 'lundi', '14:00:00', '18:00:00'),
(2, 'mardi', '09:00:00', '17:00:00'), (2, 'mercredi', '08:00:00', '16:00:00'),
(2, 'jeudi', '10:00:00', '18:00:00'), (2, 'vendredi', '08:00:00', '16:00:00'),
(2, 'samedi', '10:00:00', '14:00:00');

-- Configuration salle
INSERT INTO salle_sport (nom, adresse, telephone, email, horaires_json, services_json, tarifs_json) VALUES
('Salle de sport Omnes', 
 '123 Rue du Sport, 75001 Paris', 
 '+33 1 23 45 67 89', 
 'salle@sportify.com',
 '{"lundi": "7:00-22:00", "mardi": "7:00-22:00", "mercredi": "7:00-22:00", "jeudi": "7:00-22:00", "vendredi": "7:00-22:00", "samedi": "8:00-20:00", "dimanche": "8:00-20:00"}',
 '["Musculation", "Cardio-training", "Cours collectifs", "Vestiaires", "Parking", "Wifi gratuit"]',
 '{"seance_individuelle": 35, "cours_collectif": 25, "consultation": 30, "visite": 0}'
);

-- Événements
INSERT INTO evenements (titre, description, date_evenement, heure_debut, lieu, statut) VALUES
('Match de Rugby', 'Omnes Education vs. Visiteurs', '2025-06-25', '15:00:00', 'Stade Omnes, Paris', 'actif'),
('Portes Ouvertes', 'Découvrez nos installations', '2025-06-15', '10:00:00', 'Salle Omnes', 'actif');

-- Bulletins
INSERT INTO bulletins (titre, contenu, auteur_id, date_publication, statut) VALUES
('Championnat universitaire', 'Les étudiants ont brillé en natation et tennis !', 1, '2025-05-28', 'publie'),
('Nouvelle salle', 'Équipée des dernières technologies !', 1, '2025-05-27', 'publie'),
('Programme été 2025', 'Inscrivez-vous aux programmes d\'été !', 1, '2025-05-26', 'publie');

-- Rendez-vous d'exemple
INSERT INTO rendezvous (client_id, coach_id, activite_id, date_rdv, heure_debut, heure_fin, lieu, statut, prix) VALUES
(1, 1, 1, '2025-06-02', '14:00:00', '15:00:00', 'Studio 1', 'confirme', 35.00),
(1, 2, 2, '2025-06-05', '16:30:00', '17:15:00', 'Studio 2', 'confirme', 30.00),
(2, 3, 9, '2025-05-25', '15:00:00', '16:00:00', 'Court tennis', 'termine', 40.00);

-- ========================================
-- VÉRIFICATION
-- ========================================

-- Compter les enregistrements
SELECT 'VÉRIFICATION DES DONNÉES' as info;
SELECT 'users' as table_name, COUNT(*) as count FROM users
UNION ALL SELECT 'coachs', COUNT(*) FROM coachs
UNION ALL SELECT 'clients', COUNT(*) FROM clients
UNION ALL SELECT 'activites', COUNT(*) FROM activites
UNION ALL SELECT 'bulletins', COUNT(*) FROM bulletins
UNION ALL SELECT 'evenements', COUNT(*) FROM evenements;

-- Afficher quelques données
SELECT 'EXEMPLE UTILISATEURS' as info;
SELECT role, nom, prenom, email FROM users LIMIT 5;