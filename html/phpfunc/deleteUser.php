<?php 
    // Get the user ID from POST data
    if (isset($_POST['idAdherent'])) {
        $id = $_POST['idAdherent'];
        $db_host = getenv('MYSQL_HOST') ?: 'localhost';
        $db_port = getenv('MYSQL_PORT') ?: '3306';
        $servername = $db_host . ":" . $db_port;
        $db_username = getenv('MYSQL_USER') ?: 'bmanem';
        $db_password = getenv('MYSQL_PASSWORD') ?: 'jeXja8-septuf-fitsuj';
        $dbname = getenv('MYSQL_DATABASE') ?: 'bmanem';

        $conn = new mysqli($servername, $db_username, $db_password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
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