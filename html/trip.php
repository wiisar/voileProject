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
    <link rel="stylesheet" href="style/style.css">
    <style>
        .btn-primary {
    background-color: #00bfff;
    color: #fff;
    font-weight: bold;
    border: none;
    padding: 10px;
    border-radius: 4px;
    font-size: 16px;
    transition: all 0.2s;
}
.btn-primary:hover {
    background-color: #0099cc;
    color: #fff;
    opacity: 1;
    transform: scale(1.05);
}
    </style>
</head>
<body>

    <?php include 'phpfunc/navbar.php'; ?>

    
    <h1 id="titre-centre-nautique">Les Flots Bleus</h1>
    <h2 id="titre-onglet">Séjours organisés</h2>

<?php
    include $_SERVER['DOCUMENT_ROOT'] . '/phpfunc/bddconnexion.php';


    $sqlTypes = "SELECT DISTINCT intitule 
        FROM Sejours Se 
        JOIN Stages S 
        ON Se.idStage=S.idStage";
    $stmt = $conn->prepare($sqlTypes);
    $stmt->execute();
    $resultTypes = $stmt->get_result();

    $boatsql="SELECT DISTINCT idModele, type FROM Modeles";
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
            echo '<option value="' . htmlspecialchars($row["intitule"]) . '">' . htmlspecialchars($row["intitule"]) . '</option>';
        }
        echo '</select>';
        echo '<select id ="boat" name="boat">';
        echo '<option value =""> </option>';
        while ($row = $boatresult->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($row["idModele"]) . '">' . htmlspecialchars($row["type"]) . '</option>';
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

    // echo '<p>'.$selectedBoat.'</p>';

    
    $sqlCards = "SELECT S.intitule, M.type, P1.emplacements AS P_Depart, P2.emplacements AS P_Arriver, S.duree, Se.capacite, Se.prix, Se.idSejour
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
            echo '<p>Capacité: ' . htmlspecialchars($row["capacite"]) . '</p>';
            echo '<p>Prix: ' . htmlspecialchars($row["prix"]) . ' €</p>';

            if (isset($_SESSION['idAdherent'])) {
                // Vérifier si déjà inscrit
                $idAdherent = intval($_SESSION['idAdherent']);
                $idSejour = intval($row["idSejour"]);
                $stmtCheck = $conn->prepare("SELECT 1 FROM Participer WHERE idAdherent = ? AND idSejour = ?");
                $stmtCheck->bind_param("ii", $idAdherent, $idSejour);
                $stmtCheck->execute();
                $stmtCheck->store_result();
                $isInscrit = $stmtCheck->num_rows > 0;
                $stmtCheck->close();

                if ($isInscrit) {
                    // Bouton désinscription
                    echo '<form method="post" action="">
                            <input type="hidden" name="desinscrireSejour" value="' . $idSejour . '">
                            <button type="submit" class="btn-secondaire">Se désinscrire</button>
                          </form>';
                } else {
                    // Bouton inscription
                    echo '<form method="post" action="">
                            <input type="hidden" name="inscrireSejour" value="' . $idSejour . '">
                            <button type="submit" class="btn-primary">S\'inscrire</button>
                          </form>';
                }
            } else {
                echo '<p style="color:#7ec9d3;font-size:14px;">Connectez-vous pour vous inscrire</p>';
            }
            if (isset($_SESSION['group']) && ($_SESSION['group'] == "Administrateur" || $_SESSION['group'] == "Planificateur")) {
    echo '<form method="get" action="/adminpage/sejour_details.php" style="margin-top:10px;">
            <input type="hidden" name="idSejour" value="' . intval($row["idSejour"]) . '">
            <button type="submit" class="btn-secondary">Gérer</button>
          </form>';
}
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["inscrireSejour"]) && isset($_SESSION['idAdherent'])) {
    $idSejour = intval($_POST["inscrireSejour"]);
    $idAdherent = intval($_SESSION['idAdherent']);
    // Vérifie si déjà inscrit
    $stmt = $conn->prepare("SELECT 1 FROM Participer WHERE idAdherent = ? AND idSejour = ?");
    $stmt->bind_param("ii", $idAdherent, $idSejour);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 0) {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO Participer (idAdherent, idSejour) VALUES (?, ?)");
        $stmt->bind_param("ii", $idAdherent, $idSejour);
        if ($stmt->execute()) {
            echo "<p style='color: #7ec9d3;'>Inscription réussie !</p>";
        } else {
            echo "<p style='color: red;'>Erreur lors de l'inscription.</p>";
        }
    } else {
        echo "<p style='color: orange;'>Vous êtes déjà inscrit à ce séjour.</p>";
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["desinscrireSejour"]) && isset($_SESSION['idAdherent'])) {
    $idSejour = intval($_POST["desinscrireSejour"]);
    $idAdherent = intval($_SESSION['idAdherent']);
    $stmt = $conn->prepare("DELETE FROM Participer WHERE idAdherent = ? AND idSejour = ?");
    $stmt->bind_param("ii", $idAdherent, $idSejour);
    if ($stmt->execute()) {
        echo "<p style='color: #7ec9d3;'>Désinscription réussie.</p>";
    } else {
        echo "<p style='color: red;'>Erreur lors de la désinscription.</p>";
    }
    $stmt->close();
}

    // if (isset($_SESSION["username"])) {
    //     echo "<h1>Votre adresse mail est : " . htmlspecialchars($_SESSION["username"]) . "</h1>";
    // } else {
    //     echo "<h1>Adresse mail non disponible. Veuillez vous connecter.</h1>";
    // } TEST
    ?>
    <script src="script/display.js"></script>
</body>
</html>