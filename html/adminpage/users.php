<?php
session_start();
 ?>

<!DOCTYPE html>
<html lang="fr"></html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Séjours</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/style/style.css">
</head>
<body>

    <?php include '../phpfunc/navbar.php'; ?>

    <h1 id="titre-centre-nautique">Les Flots Bleus</h1>
    <h2 id="titre-onglet">Gestion des utilisateurs</h2>

<?php
if(isset($_SESSION['group']) && $_SESSION['group'] == "Administrateur"){
    
    echo "<button id='addUserBtn'>Ajouter un utilisateur</button>";
    echo '<div id="userAddModal" style="display:none;">';
    include $_SERVER['DOCUMENT_ROOT'] . '/phpfunc/userAdd.php';
    echo '</div>';


    // echo '<li class="login-signup"><a href="#">Connexion / Inscription</a></li>';
            
    
    $db_host = getenv('MYSQL_HOST') ?: 'localhost';
    $db_port = getenv('MYSQL_PORT') ?: '3306';
    $servername = $db_host . ":" . $db_port;
    $db_username = getenv('MYSQL_USER') ?: 'bmanem';
    $db_password = getenv('MYSQL_PASSWORD') ?: 'jeXja8-septuf-fitsuj';
    $dbname = getenv('MYSQL_DATABASE') ?: 'bmanem';



    // Create connection
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sqlUser = "SELECT * FROM Adherents A ";
    $stmt = $conn->prepare($sqlUser);
    $stmt->execute();
    $resultTypes = $stmt->get_result();

    if ($resultTypes->num_rows > 0) {
        echo '<table class = "user-tab" border="1" cellpadding="5" cellspacing="0">';
        echo '<tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Date d\'inscription</th>
            <th>Éditer</th>
            <th>Supprimer</th>
              </tr>';
        while ($res = $resultTypes->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($res["nom"]) . '</td>';
            echo '<td>' . htmlspecialchars($res["prenom"]) . '</td>';
            echo '<td>' . htmlspecialchars($res["mail"]) . '</td>';
            echo '<td>' . htmlspecialchars($res["dateAdhesion"]) . '</td>';
            echo '<td><input type="checkbox" name="edit_user[]" value="' . htmlspecialchars($res["idAdherent"]) . '"></td>';
            echo '<td><button class="delete-btn" id="' . htmlspecialchars($res["idAdherent"]) . '">×</button></td>';
            echo '</tr>';
        }
        echo '</table>';
    }
}
else{
    echo "<h2> Vous n'avez pas les droits d'acces à cette page. </h2>";
}
    
    
?>

    <script src="/script/display.js"></script>
</body>
</html>