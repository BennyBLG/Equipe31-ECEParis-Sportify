<?php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'sportify');
define('DB_USER', 'root');
define('DB_PASS', ''); // Mot de passe vide pour WAMP par défaut

// Fonction de connexion à la base de données
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données: " . $e->getMessage());
    }
}

// Fonction pour vérifier si un utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

// Fonction pour vérifier le rôle d'un utilisateur
function hasRole($role) {
    return isLoggedIn() && isset($_SESSION['user_info']['role']) && $_SESSION['user_info']['role'] === $role;
}

// Fonction pour rediriger si pas connecté
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: votre_compte.php');
        exit;
    }
}

// Fonction pour rediriger si pas le bon rôle
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        header('Location: votre_compte.php');
        exit;
    }
}

// Fonction pour nettoyer les entrées utilisateur
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Fonction pour hasher les mots de passe
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Fonction pour vérifier les mots de passe
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
?>