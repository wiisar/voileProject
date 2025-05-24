<?php
session_start();
if (!isset($_SESSION['group']) || $_SESSION['group'] != "Administrateur" ) {
    echo "<h2>Accès refusé.</h2>";
    exit;
}

     include $_SERVER['DOCUMENT_ROOT'] . '/phpfunc/bddconnexion.php';

$idSejour = isset($_GET['idSejour']) ? intval($_GET['idSejour']) : 0;
if ($idSejour <= 0) {
    echo "<p>Séjour invalide.</p>";
    exit;
}

// Suppression d'un participant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['removeAdherent'])) {
    $idAdherent = intval($_POST['removeAdherent']);
    $stmt = $conn->prepare("DELETE FROM Participer WHERE idAdherent = ? AND idSejour = ?");
    $stmt->bind_param("ii", $idAdherent, $idSejour);
    $stmt->execute();
    $stmt->close();
}

// Ajout d'un participant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addAdherent'])) {
    $idAdherent = intval($_POST['addAdherent']);
    $stmt = $conn->prepare("INSERT INTO Participer (idAdherent, idSejour) VALUES (?, ?)");
    $stmt->bind_param("ii", $idAdherent, $idSejour);
    $stmt->execute();
    $stmt->close();
}

// Infos séjour
$stmt = $conn->prepare("SELECT S.*, St.intitule FROM Sejours S JOIN Stages St ON S.idStage = St.idStage WHERE S.idSejour = ?");
$stmt->bind_param("i", $idSejour);
$stmt->execute();
$sejour = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$sejour) {
    echo "<p>Séjour introuvable.</p>";
    exit;
}

// Liste des participants inscrits
$stmt = $conn->prepare("SELECT A.idAdherent, A.nom, A.prenom FROM Adherents A JOIN Participer P ON A.idAdherent = P.idAdherent WHERE P.idSejour = ?");
$stmt->bind_param("i", $idSejour);
$stmt->execute();
$participants = $stmt->get_result();
$nbParticipants = $participants->num_rows;
$stmt->close();

// Liste des adhérents non inscrits
$stmt = $conn->prepare("SELECT idAdherent, nom, prenom FROM Adherents WHERE idAdherent NOT IN (SELECT idAdherent FROM Participer WHERE idSejour = ?)");
$stmt->bind_param("i", $idSejour);
$stmt->execute();
$nonInscrits = $stmt->get_result();
$stmt->close();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du séjour</title>
    <link rel="stylesheet" href="/style/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/phpfunc/navbar.php';?>
    
    <div class="details-flex-container">
        <div class="sejour-info-box">
            <h1>Détails du séjour</h1>
            <p><strong>Stage :</strong> <?= htmlspecialchars($sejour['intitule']) ?></p>
            <p><strong>Date :</strong> <?= htmlspecialchars($sejour['dates']) ?></p>
            <p><strong>Capacité :</strong> <?= htmlspecialchars($sejour['capacite']) ?></p>
            <p><strong>Nombre de participants :</strong> <?= $nbParticipants ?></p>
            <p><a href="<?= isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : '/adminpage/sejours.php' ?>">&larr; Retour</a></p>
        </div>
        <div class="participants-panel">
            <h2>Ajouter un participant</h2>
            <form method="post" autocomplete="off" id="addAdhForm">
                <input type="text" id="adherentSearch" placeholder="Nom ou prénom..." required>
                <input type="hidden" name="addAdherent" id="addAdherentId">
                <div id="adherentResults" class="autocomplete-list"></div>
                <button type="submit" class="btn-primary">Ajouter</button>
            </form>

            <h2>Participants inscrits</h2>
            <ul class="participants-list">
                <?php while ($row = $participants->fetch_assoc()){
                 ?>
                    <li>
                        <?= htmlspecialchars($row['prenom'] . ' ' . $row['nom']) ?>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="removeAdherent" value="<?= $row['idAdherent'] ?>">
                            <button type="submit" class="btn-secondaire" onclick="return confirm('Retirer cet adhérent du séjour ?')">Supprimer</button>
                        </form>
                    </li>
                <?php }; ?>
            </ul>
        </div>
    </div>
</body>
<?php $conn->close(); ?>
<script>
const adherents = [
    <?php
    // Remplir la liste JS avec tous les non-inscrits
    $nonInscrits->data_seek(0);
    while ($row = $nonInscrits->fetch_assoc()) {
        $label = htmlspecialchars($row['prenom'] . ' ' . $row['nom']);
        echo "{id: " . $row['idAdherent'] . ", label: '" . $label . "'},";
    }
    ?>
];

const input = document.getElementById('adherentSearch');
const results = document.getElementById('adherentResults');
const hiddenId = document.getElementById('addAdherentId');

input.addEventListener('input', function() {
    const val = this.value.toLowerCase();
    results.innerHTML = '';
    if (!val) return;
    adherents.filter(a => a.label.toLowerCase().includes(val)).forEach(a => {
        const div = document.createElement('div');
        div.textContent = a.label;
        div.className = 'autocomplete-item';
        div.onclick = function() {
            input.value = a.label;
            hiddenId.value = a.id;
            results.innerHTML = '';
        };
        results.appendChild(div);
    });
});

// Sécurité : vider l'id caché si on modifie le texte
input.addEventListener('change', function() {
    if (!adherents.some(a => a.label === input.value)) {
        hiddenId.value = '';
    }
});

// Empêcher l'envoi si pas d'id sélectionné
document.getElementById('addAdhForm').addEventListener('submit', function(e) {
    if (!hiddenId.value) {
        e.preventDefault();
        alert("Veuillez sélectionner un adhérent dans la liste.");
    }
});
</script>
</html>
