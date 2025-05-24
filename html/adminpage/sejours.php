<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Séjours</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/style/style.css">
</head>
<body>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/phpfunc/navbar.php';?>

<h1 id="titre-centre-nautique">Les Flots Bleus</h1>
<h2 id="titre-onglet">Gestion des séjours</h2>

<?php
if (isset($_SESSION['group']) && ($_SESSION['group'] == "Administrateur" || $_SESSION['group'] == "Planificateur")) {

    echo "<button id='addSejBtn'>Ajouter un séjour</button>";
    echo '<div id="addSejour" style="display:none;">';
    include $_SERVER['DOCUMENT_ROOT'] . '/phpfunc/addSejour.php';
    echo '</div>';

    // Connexion à la base de données
    include $_SERVER['DOCUMENT_ROOT'] . '/phpfunc/bddconnexion.php';


    $sqlUser = "SELECT * FROM Sejours";
    $stmt = $conn->prepare($sqlUser);
    $stmt->execute();
    $resultTypes = $stmt->get_result();

    if ($resultTypes->num_rows > 0) {
        echo '<table class="user-tab" border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Stage</th>
            <th>Port de départ</th>
            <th>Port d\'arrivée</th>
            <th>Date de début</th>
            <th>Date de fin</th>
            <th>Capacité</th>
            <th>Prix</th>
            <th>Bateau utilisé</th>
            <th>Gérer</th>
            <th>Suppression</th>
        </tr>';

        while ($sejour = $resultTypes->fetch_assoc()) {
            // Récupérer les ports
            $sqlPorts = "SELECT p1.emplacements AS portDepart, p2.emplacements AS portArrivee
                         FROM Ports p1, Ports p2
                         WHERE p1.idPort = ? AND p2.idPort = ?";
            $stmtPorts = $conn->prepare($sqlPorts);
            $stmtPorts->bind_param("ii", $sejour["idPortsDepart"], $sejour["idPortsArriver"]);
            $stmtPorts->execute();
            $resultPorts = $stmtPorts->get_result();
            $portInfo = $resultPorts->fetch_assoc();

            // Récupérer le stage (intitulé et durée)
            $sqlStage = "SELECT intitule, duree FROM Stages WHERE idStage = ?";
            $stmtStage = $conn->prepare($sqlStage);
            $stmtStage->bind_param("i", $sejour["idStage"]);
            $stmtStage->execute();
            $resultStage = $stmtStage->get_result();
            $stageInfo = $resultStage->fetch_assoc();

            $dateDebut = new DateTime($sejour["dates"]);
            $dateFin = (clone $dateDebut)->modify('+' . intval($stageInfo["duree"]) . ' days');

            $sqlBateau = "SELECT B.nom
                           FROM Bateaux B
                           JOIN Modeles M ON B.idModele = M.idModele
                           JOIN Stages S ON M.idModele = S.idModele
                           WHERE S.idStage = ?
                           LIMIT 1";
            $stmtBateau = $conn->prepare($sqlBateau);
            $stmtBateau->bind_param("i", $sejour["idStage"]);
            $stmtBateau->execute();
            $resultBateau = $stmtBateau->get_result();
            $bateauInfo = $resultBateau->fetch_assoc();
            $nomBateau = $bateauInfo ? $bateauInfo["nom"] : "Non trouvé";
            $stmtBateau->close();

            echo '<tr>';
            echo '<td>' . htmlspecialchars($stageInfo["intitule"]) . '</td>';
            echo '<td>' . htmlspecialchars($portInfo["portDepart"]) . '</td>';
            echo '<td>' . htmlspecialchars($portInfo["portArrivee"]) . '</td>';
            echo '<td>' . htmlspecialchars($dateDebut->format('Y-m-d')) . '</td>';
            echo '<td>' . htmlspecialchars($dateFin->format('Y-m-d')) . '</td>';
            echo '<td>' . htmlspecialchars($sejour["capacite"]) . '</td>';
            echo '<td>' . htmlspecialchars($sejour["prix"]) . ' €</td>';
            echo '<td>' . htmlspecialchars($nomBateau) . '</td>';
        
                echo '<td>
                    <form method="get" action="/adminpage/sejour_details.php" style="display:inline;">
                        <input type="hidden" name="idSejour" value="' . htmlspecialchars($sejour['idSejour']) . '">
                        <button type="submit" class="manage-btn">Gérer</button>
                    </form>
                    </td>';
            echo '<td>
                <form method="post" action="/phpfunc/deleteSejour.php" style="display:inline;" onsubmit="return confirm(\'Êtes-vous sûr de vouloir supprimer ce séjour ?\');">
                    <input type="hidden" name="idSejour" value="' . htmlspecialchars($sejour["idSejour"]) . '">
                    <button type="submit" class="delete-btn">×</button>
                </form>
                </td>';
            echo '</tr>';

            $stmtPorts->close();
            $stmtStage->close();
        }
        echo '</table>';
    } else {
        echo "<p>Aucun séjour trouvé.</p>";
    }

    $stmt->close();
    $conn->close();

} else {
    echo "<h2>Vous n'avez pas les droits d'accès à cette page.</h2>";
}
?>
<script src="/script/display.js"></script>
<script src="/script/userUpdate.js"></script>
</body>
</html>
