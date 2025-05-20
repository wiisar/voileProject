php
<nav>
    <ul>
    <li><a href="/home.php">Accueil</a></li>
    <li class="dropdown">
        <a href="#" class="dropbtn">Informations</a>
        <div class="dropdown-content">
            <a href="/pricing.php">Prix</a>
            <a href="/club.php">Club</a>
            <a href="/horaires.php">Horaires</a>
        </div>
    </li>
    <li><a href="/reservation.php">Réserver en ligne</a></li>
    <li><a href="/trip.php">Séjours organisés</a></li>
    <li><a href="/contact.php">Contact</a></li>
    <?php
        if (isset($_SESSION["group"]) && $_SESSION["group"] == "Administrateur") {
            echo '<li class="dropdown dropdown-admin">';
            echo ' <a class="dropbtn-login" href="admin.php">Admin</a>';
            echo '<div class="dropdown-content">';
            echo '<a href="adminpage/users.php">Gestion utilisateurs</a>';
            echo '<a href="profile.php">Gestion Séjours</a>';
            echo '<a href="profile.php">Paramétres</a>';
            echo '</div>';
            echo '</li>';
        }
        if (isset($_SESSION["username"])) {
            echo '<li class="dropdown dropdown-login">';
            echo '<a href="#" class="dropbtn-login">' . htmlspecialchars($_SESSION["username"]) . '</a>';
            echo '<div class="dropdown-content">';
            echo '<span class="log-panel-info" >'. htmlspecialchars($_SESSION["nom"]) .' ' .htmlspecialchars($_SESSION["prenom"]) .'</span> <br>';
            echo '<span class="log-panel-info">'. htmlspecialchars($_SESSION["group"]) .'</span>';
            echo '<a href="profile.php">Profil</a>';
            echo '<a href="/phpfunc/logout.php">Déconnexion</a>';
            echo '</div>';
            echo '</li>';
        } else {
            echo '<li class="login-signup"><a href="#">Connexion / Inscription</a></li>';
            echo '</ul>';
            echo '<div id="loginModal" style="display:none;">';
            include 'phpfunc/login.php';
            echo '</div>';
        }
        ?>
        </ul>
</nav>

    