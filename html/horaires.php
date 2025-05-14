<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="fr"></html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horaires</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

    <?php include 'phpfunc/navbar.php'; ?>

    <h1 id="titre-centre-nautique">Les Flots Bleus</h1>
    <h2 id="titre-onglet">Horaires</h2>

    <strong>Lundi : </strong>8h-12h et 14h-18h<br>
    <strong>Mardi : </strong>8h-12h et 14h-18h<br>
    <strong>Mercredi : </strong>8h-12h et 14h-18h<br>
    <strong>Jeudi : </strong>8h-12h et 14h-18h<br>
    <strong>Vendredi : </strong>8h-12h et 14h-18h<br>
    <strong>Samedi : </strong>7h-13h et 14h-19h<br>
    <strong>Dimanche : </strong>7h-13h et 14h-19h<br>

    <script src="script/display.js"></script>
    <script src="script.js"></script>
</body>
</html>