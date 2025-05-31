<?php
session_start();
if ($_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Admin</title>
</head>
<body>
    <h1>Bienvenue Admin !</h1>
    <p>Vous êtes connecté en tant qu'administrateur.</p>
</body>
</html>
