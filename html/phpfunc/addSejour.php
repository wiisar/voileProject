<?php
$db_host = getenv('MYSQL_HOST') ?: 'localhost';
$db_port = getenv('MYSQL_PORT') ?: '3306';
$servername = $db_host . ":" . $db_port;
$db_username = getenv('MYSQL_USER') ?: 'bmanem';
$db_password = getenv('MYSQL_PASSWORD') ?: 'jeXja8-septuf-fitsuj';
$dbname = getenv('MYSQL_DATABASE') ?: 'bmanem';

// Connexion BDD
$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter_sejour') {
    $capacite = $_POST['capacite'];
    $prix = $_POST['prix'];
    $idPortsDepart = $_POST['idPortsDepart'];
    $idPortsArriver = $_POST['idPortsArriver'];
    $dateDebut = $_POST['dateDebut'];
    $idStage = $_POST['idStage'];
    $idBateau = $_POST['idBateau'];

    // Validation des références
    $checkPortsQuery = "SELECT COUNT(*) as count FROM Ports WHERE idPort = ? OR idPort = ?";
    $checkPorts = $conn->prepare($checkPortsQuery);
    $checkPorts->bind_param("ii", $idPortsDepart, $idPortsArriver);
    $checkPorts->execute();
    $portResult = $checkPorts->get_result();
    $portCount = $portResult->fetch_assoc()['count'];

    $checkStageQuery = "SELECT COUNT(*) as count FROM Stages WHERE idStage = ?";
    $checkStage = $conn->prepare($checkStageQuery);
    $checkStage->bind_param("i", $idStage);
    $checkStage->execute();
    $stageResult = $checkStage->get_result();
    $stageCount = $stageResult->fetch_assoc()['count'];

    $checkBateauQuery = "SELECT COUNT(*) as count FROM Bateaux WHERE idBateau = ?";
    $checkBateau = $conn->prepare($checkBateauQuery);
    $checkBateau->bind_param("i", $idBateau);
    $checkBateau->execute();
    $bateauResult = $checkBateau->get_result();
    $bateauCount = $bateauResult->fetch_assoc()['count'];

    if ($portCount < 2 || $stageCount < 1 || $bateauCount < 1) {
        echo "<p style='color:red;'>Erreur: Ports, stage ou bateau invalides.</p>";
    } else {
        // Récupérer la durée du stage
        $getDureeQuery = "SELECT duree FROM Stages WHERE idStage = ?";
        $getDuree = $conn->prepare($getDureeQuery);
        $getDuree->bind_param("i", $idStage);
        $getDuree->execute();
        $dureeResult = $getDuree->get_result();
        $dureeRow = $dureeResult->fetch_assoc();
        $duree = $dureeRow['duree']; // Supposons que la colonne s'appelle 'duree' (en jours)
        $getDuree->close();

        // Calculer la date de fin
        $dateDebutDateTime = new DateTime($dateDebut);
        $dateDebutDateTime->modify("+$duree days");
        $dateFin = $dateDebutDateTime->format('Y-m-d');

        // Insertion dans Séjours
        $insertSejour = $conn->prepare("INSERT INTO Sejours (capacite, prix, idPortsDepart, idPortsArriver, dateDebut, dateFin, idStage, idBateau) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insertSejour->bind_param("idiissii", $capacite, $prix, $idPortsDepart, $idPortsArriver, $dateDebut, $dateFin, $idStage, $idBateau);
        if ($insertSejour->execute()) {
            echo "<p style='color:green;'>Séjour ajouté avec succès !</p>";
        } else {
            echo "<p style='color:red;'>Erreur lors de l'ajout du séjour.</p>";
        }
        $insertSejour->close();
    }

    $checkPorts->close();
    $checkStage->close();
    $checkBateau->close();
}

// Récupération des données pour les listes déroulantes
$ports = $conn->query("SELECT idPort, emplacements FROM Ports");
$stages = $conn->query("SELECT idStage, intitule FROM Stages");
$bateaux = $conn->query("SELECT idBateau, nom FROM Bateaux");
?>

<div class="logform">
    <form method="POST" action="">
        <input type="hidden" name="action" value="ajouter_sejour">

        <label for="idPortsDepart">Port de départ :</label>
        <select name="idPortsDepart" required>
            <?php while ($row = $ports->fetch_assoc()) echo "<option value='".$row['idPort']."'>".$row['emplacements']."</option>"; ?>
        </select>
        <br>
        <label for="idPortsArriver">Port d'arrivée :</label>
        <select name="idPortsArriver" required>
            <?php
            $ports->data_seek(0); // Rewind result set
            while ($row = $ports->fetch_assoc()) echo "<option value='".$row['idPort']."'>".$row['emplacements']."</option>";
            ?>
        </select>
        <br>

        <label for="idStage">Type de stage :</label>
        <select name="idStage" required>
            <?php while ($row = $stages->fetch_assoc()) echo "<option value='".$row['idStage']."'>".$row['intitule']."</option>"; ?>
        </select>
        <br>

        <label for="idBateau">Bateau :</label>
        <select name="idBateau" required>
            <?php while ($row = $bateaux->fetch_assoc()) echo "<option value='".$row['idBateau']."'>".$row['nom']."</option>"; ?>
        </select>
        <br>

        <label for="dateDebut">Date de début :</label>
        <input type="date" name="dateDebut" required>

        <input type="hidden" name="dateFin"> <!-- Plus besoin de demander -->

        <input type="number" name="capacite" placeholder="Capacité" required>
        <input type="number" name="prix" placeholder="Prix" step="0.01" required>

        <button type="submit">Ajouter</button>
        <button type="button" onclick="document.getElementById('addSejour').style.display='none';">Annuler</button>
    </form>
</div>

<?php
$conn->close();
?>
