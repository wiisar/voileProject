<?php
     include $_SERVER['DOCUMENT_ROOT'] . '/phpfunc/bddconnexion.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idSejour'])) {
    $idSejour = intval($_POST['idSejour']);
    $stmt = $conn->prepare("DELETE FROM Sejours WHERE idSejour = ?");
    $stmt->bind_param("i", $idSejour);
    if ($stmt->execute()) {
        // Redirection après suppression
        header("Location: /adminpage/sejours.php");
        exit();
    } else {
        echo "<p style='color:red;'>Erreur lors de la suppression du séjour.</p>";
    }
    $stmt->close();
}

$conn->close();
?>