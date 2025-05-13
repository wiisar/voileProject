<?php

require_once 'phpfunc/session_handler.php';

$handler = new DBSessionHandler();
session_set_save_handler($handler, true);
session_start();

?>

<!DOCTYPE html>
<html lang="fr"></html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Séjours</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

    <ul>
        <li><a href="home.php">Accueil</a></li>
        <li><a href="horaires.html">Horaires</a></li>
        <li><a href="pricing.html">Tarifs</a></li>
        <li><a href="reservation.html">Réserver en ligne</a></li>
        <li><a href="trip.php">Séjours organisés</a></li>
        <li><a href="contact.html">Contact</a></li>
        <li class="login-signup"><a href="#" onclick="document.getElementById('loginModal').style.display='block'">Connexion / Inscription</a></li>
        
        </div>
        
        
            <div id="loginModal" style="display:none;">
                <?php include 'login.php'; ?>
        
        </ul>
    
    <h1 id="titre-centre-nautique">Les Flots Bleus</h1>
    <h2 id="titre-onglet">Séjours organisés</h2>

<?php
    $servername = getenv('MYSQL_HOST') .":". getenv('MYSQL_PORT');
    $username = getenv('MYSQL_USER');
    $password = getenv('MYSQL_PASSWORD');
    $dbname = getenv('MYSQL_DATABASE');

    // $servername = "localhost:3306";
    // $username = "bmanem";
    // $password = "jeXja8-septuf-fitsuj";
    // $dbname = "bmanem";
    
    // echo "<p>Login successful. <br>Welcome, ". $_SESSION["nom"]."!</p>";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // $sql = "SELECT * FROM Sejours";
    // $result = $conn->query($sql);

    // if ($result->num_rows > 0) {
    //     echo "<table><tr><th>ID</th><th>Prenom</th><th>Nom</th></tr>";
    //     while($row = $result->fetch_assoc()) {
    //         echo "<tr><td>" . $row["idSejour"]. "</td><td>" . $row["idPortsDepart"]. "</td><td>" . $row["idPortsArriver"]. "</td></tr>";
    //     }
    //     echo "</table>";
    // } else {
    //     echo "0 results";
    // }
    $sqlTypes = "SELECT DISTINCT intitule 
        FROM Sejours Se 
        JOIN Stages S 
        ON Se.idStage=S.idStage";
    $stmt = $conn->prepare($sqlTypes);
    $stmt->execute();
    $resultTypes = $stmt->get_result();

    $boatsql="SELECT DISTINCT type FROM Modeles";
    $stmt = $conn->prepare($boatsql);
    $stmt->execute();
    $boatresult = $stmt->get_result();
    if ($resultTypes->num_rows > 0) {
        echo '<form class = display-sejours method="POST" action="">';
        echo '<div class="onglets">';
        echo '<label for="typeSejour">Intitule :</label>';
        echo '<select id="intitule" name="intitule">';
        echo '<option value =""> </option>';
        while ($row = $resultTypes->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars(string: $row["intitule"]) . '">' . htmlspecialchars($row["intitule"]) . '</option>';
        }
        echo '</select>';
        echo '<select id ="boat" name="boat">';
        echo '<option value =""> </option>';
        while ($row = $boatresult->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars(string: $row["id"]) . '">' . htmlspecialchars($row["type"]) . '</option>';
        }
        echo '</select>';
        echo '</div>';
        echo '<button type="submit">Afficher les séjours</button>';
        echo '</form>';
    } else {
        echo "Aucun type de séjour disponible.";
    }

    
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["intitule"]) && isset($_POST["boat"])) {
    $selectedIntitule = $_POST["intitule"];
    $selectedBoat = $_POST["boat"];

    // echo '<p>'.$selectedIntitule.'</p>';
    echo '<p>'.$selectedBoat.'</p>';

    
    $sqlCards = "SELECT S.intitule, M.type, P1.emplacements AS P_Depart, P2.emplacements AS P_Arriver, S.duree 
                 FROM Sejours Se 
                 JOIN Stages S ON Se.idStage = S.idStage
                 JOIN Modeles M ON S.idModele = M.idModele
                 JOIN Ports P1 ON Se.idPortsDepart = P1.idPort
                 JOIN Ports P2 ON Se.idPortsArriver = P2.idPort
                 WHERE (? = '' OR S.intitule = ?) 
                 AND (?='' OR M.idModele = ?)";
    
    $stmt = $conn->prepare($sqlCards);
    $stmt->bind_param("ssss", $selectedIntitule, $selectedIntitule, $selectedBoat, $selectedBoat);
    $stmt->execute();
    $resultCards = $stmt->get_result();
                 
    if ($resultCards->num_rows > 0) {
        echo '<div class="cards-container">';
        while ($row = $resultCards->fetch_assoc()) {
            echo '<div class="card">';
            echo '<h3>' . htmlspecialchars($row["intitule"]) . '</h3>';
            echo '<h4>' . htmlspecialchars($row["type"]) . '</h4>';
            echo '<p>Départ: ' . htmlspecialchars($row["P_Depart"]) . '</p>';
            echo '<p>Arrivée: ' . htmlspecialchars($row["P_Arriver"]) . '</p>';
            echo '<p>Durée: ' . htmlspecialchars($row["duree"]) . ' heure(s)</p>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo "Aucun séjour trouvé pour ce type.";
    }
    $stmt->close();
    $boatresult->close();
    $resultTypes->close();
    $resultCards->close();
    $conn->close();
}

    ?>
<!-- // The form has been moved to wrap the dropdown above <form method="POST" action="">
     <button type="submit">Afficher les séjours</button>
 </form> -->

    <script src="script.js"></script>
</body>
</html>