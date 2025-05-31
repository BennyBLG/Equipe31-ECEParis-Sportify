<?php
session_start();
if ($_SESSION["role"] !== "client") {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Client</title>
</head>
<body>
    <h1>Bienvenue Client !</h1>
    <p>Vous êtes connecté en tant que client.</p>
</body>
</html>
