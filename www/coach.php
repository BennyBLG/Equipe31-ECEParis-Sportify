<?php
session_start();
if ($_SESSION["role"] !== "coach") {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Coach</title>
</head>
<body>
    <h1>Bienvenue Coach !</h1>
    <p>Vous êtes connecté en tant que coach.</p>
</body>
</html>
