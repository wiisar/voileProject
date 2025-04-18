<!DOCTYPE html>
<html lang="fr"></html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centre de voile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

    <!-- Onglets principaux (barre horizontale) -->
    <ul>
        <li><a href="home.php">Accueil</a></li>
        <li><a href="horaires.html">Horaires</a></li>
        <li><a href="pricing.html">Tarifs</a></li>
        <li><a href="reservation.html">Réserver en ligne</a></li>
        <li><a href="trip.php">Séjours organisés</a></li>
        <li><a href="contact.html">Contact</a></li>
        <li class="login-signup"><a href="login.php">Connexion / Inscription</a></li>
    </ul>
    
    <h1 id="titre-centre-nautique">Les Flots Bleus</h1>

    <!-- Fond vidéo -->
    <div class="fond-video">
        <video autoplay muted loop>
            <source src="images/video_vague_grise.mp4" type="video/mp4">
            Votre navigateur ne supporte pas la balise vidéo.
        </video>
    </div>

    <!-- Insertion des images des différentes embarcations dans une grille -->
    <div class="grille-photo">
        <figure>
            <a href="voilier.html">
                <img
                class="fit-picture"
                src="images/voilier.jpeg"
                alt="Image du voilier" width="500">
            </a>
            <figcaption>
                Voilier
            </figcaption>
        </figure>
        <figure>
            <a href="voilier.html">
                <img
                class="fit-picture"
                src="images/catamaran.jpg"
                alt="Image du catamaran" width="500">
            </a>
            <figcaption>
                Catamaran
            </figcaption>
        </figure>
        <figure>
            <a href="voilier.html">
                <img
                class="fit-picture"
                src="images/canoe.jpg"
                alt="Image du canoë" width="500">
            </a>
            <figcaption>
                Canoë
            </figcaption>
        </figure>
        <figure>
            <a href="voilier.html">
                <img
                class="fit-picture"
                src="images/paddle.jpg"
                alt="Image du paddle" width="500">
            </a>
            <figcaption>
                Paddle
            </figcaption>
        </figure>
        <figure>
            <a href="voilier.html">
                <img
                class="fit-picture"
                src="images/planche.jpg"
                alt="Image de la planche à voile" width="500">
            </a>
            <figcaption>
                Planche à voile
            </figcaption>
        </figure>
        <figure>
            <a href="voilier.html">
                <img
                class="fit-picture"
                src="images/surf.jpeg"
                alt="Image du surf" width="500">
            </a>
            <figcaption>
                Surf
            </figcaption>
        </figure>
    </div>

    <script src="script.js"></script>
</body>
</html>