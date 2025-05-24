<?php
include $_SERVER['DOCUMENT_ROOT'] . '/phpfunc/bddconnexion.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter_sejour') {
    $capacite = $_POST['capacite'];
    $idPortsDepart = $_POST['idPortsDepart'];
    $idPortsArriver = $_POST['idPortsArriver'];
    $dateDebut = $_POST['dateDebut'];
    $idStage = $_POST['idStage'];
    // $idBateau = $_POST['idBateau']; // Ne sert à rien ici

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

    if ($portCount < 2 || $stageCount < 1) {
        echo "<p style='color:red;'>Erreur: Ports ou stage invalides.</p>";
    } else {
        // Récupérer la durée et le prix max du stage
        $getStageQuery = "SELECT duree, prixMax FROM Stages WHERE idStage = ?";
        $getStage = $conn->prepare($getStageQuery);
        $getStage->bind_param("i", $idStage);
        $getStage->execute();
        $stageResult = $getStage->get_result();
        $stageRow = $stageResult->fetch_assoc();
        $duree = $stageRow['duree'];
        $prix = $stageRow['prixMax'];
        $getStage->close();


        // Insertion dans Séjours
        $insertSejour = $conn->prepare("INSERT INTO Sejours (capacite, prix, idPortsDepart, idPortsArriver, dates, idStage) VALUES (?, ?, ?, ?, ?, ?)");
        $insertSejour->bind_param("idiisi", $capacite, $prix, $idPortsDepart, $idPortsArriver, $dateDebut, $idStage);
        if ($insertSejour->execute()) {
            echo "<p style='color:green;'>Séjour ajouté avec succès !</p>";
        } else {
            echo "<p style='color:red;'>Erreur lors de l'ajout du séjour.</p>";
            echo "<p style='color:red;'>Erreur SQL : " . $insertSejour->error . "</p>";
        }
        $insertSejour->close();
    }

    $checkPorts->close();
    $checkStage->close();
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
        <!-- <input type="number" name="prix" placeholder="Prix" step="0.01" required> -->

        <button type="submit">Ajouter</button>
        <button type="button" onclick="document.getElementById('addSejour').style.display='none';">Annuler</button>
    </form>
</div>

<?php
$conn->close();
?>
