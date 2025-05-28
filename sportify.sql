-- Suppression préalable si elle existe
DROP DATABASE IF EXISTS sportify;

-- Création de la base de données
CREATE DATABASE sportify;

-- Utilisation de la base
USE sportify;

-- Table Client
CREATE TABLE Client (
  id_client INT PRIMARY KEY,
  nom VARCHAR(50),
  prenom VARCHAR(50),
  adresse VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  mot_de_passe VARCHAR(255),
  carte_etudiant VARCHAR(30)
);

-- Table Coach
CREATE TABLE Coach (
  id_coach INT PRIMARY KEY,
  nom VARCHAR(50),
  prenom VARCHAR(50),
  email VARCHAR(100) UNIQUE,
  bureau VARCHAR(100),
  cv TEXT,
  photo VARCHAR(255),
  video VARCHAR(255),
  specialite VARCHAR(50)
);

-- Table Administrateur
CREATE TABLE Administrateur (
  id_admin INT PRIMARY KEY,
  nom VARCHAR(50),
  prenom VARCHAR(50),
  email VARCHAR(100) UNIQUE
);

-- Table Salle
CREATE TABLE Salle (
  id_salle INT PRIMARY KEY,
  nom VARCHAR(50),
  numero VARCHAR(10),
  telephone VARCHAR(20),
  email VARCHAR(100),
  horaires TEXT
);

-- Table RendezVous
CREATE TABLE RendezVous (
  id_rdv INT PRIMARY KEY,
  date DATE,
  heure TIME,
  moyen_comm VARCHAR(20),
  statut VARCHAR(20),
  id_client INT,
  id_coach INT,
  id_salle INT,
  FOREIGN KEY (id_client) REFERENCES Client(id_client),
  FOREIGN KEY (id_coach) REFERENCES Coach(id_coach),
  FOREIGN KEY (id_salle) REFERENCES Salle(id_salle)
);

-- Table Disponibilite
CREATE TABLE Disponibilite (
  id_dispo INT PRIMARY KEY,
  jour VARCHAR(10),
  heure_debut TIME,
  heure_fin TIME,
  id_coach INT,
  FOREIGN KEY (id_coach) REFERENCES Coach(id_coach)
);

-- Table Paiement
CREATE TABLE Paiement (
  id_paiement INT PRIMARY KEY,
  type_carte VARCHAR(20),
  num_carte VARCHAR(20),
  nom_carte VARCHAR(100),
  date_expiration DATE,
  code_securite VARCHAR(5),
  id_client INT,
  FOREIGN KEY (id_client) REFERENCES Client(id_client)
);

-- Table Superviser (admin supervise coachs)
CREATE TABLE Superviser (
  id_admin INT,
  id_coach INT,
  PRIMARY KEY (id_admin, id_coach),
  FOREIGN KEY (id_admin) REFERENCES Administrateur(id_admin),
  FOREIGN KEY (id_coach) REFERENCES Coach(id_coach)
);

-- Table Gerer (admin gère salles)
CREATE TABLE Gerer (
  id_admin INT,
  id_salle INT,
  PRIMARY KEY (id_admin, id_salle),
  FOREIGN KEY (id_admin) REFERENCES Administrateur(id_admin),
  FOREIGN KEY (id_salle) REFERENCES Salle(id_salle)
);
