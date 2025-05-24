<?php 
    // Get the user ID from POST data
    if (isset($_POST['idAdherent'])) {
        $id = $_POST['idAdherent'];
        include $_SERVER['DOCUMENT_ROOT'] . '/phpfunc/bddconnexion.php';

        // Query to insert user
        $stmt = $conn->prepare("DELETE FROM Adherents WHERE idAdherent = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();

    } else {
        die("No user ID provided.");
    }

?>