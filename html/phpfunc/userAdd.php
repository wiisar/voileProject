<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="fr"></html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centre de voile</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter') {
    $nom= $_POST['nom'];
    $prenom= $_POST['prenom'];
    $mail= $_POST['mail'];
    $password = $_POST['password'];
    $date = date('Y-m-d');
    // Hash the password
    $password = hash('sha256', $password);
    // Database connection
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
    $stmt = $conn->prepare("INSERT INTO Adherents (nom, prenom, mail, dateAdhesion, passwd) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nom, $prenom, $mail, $date, $password);
    if ($stmt->execute()) {
        // echo "<p>Ajout réussi </p>";
    } else {
        // echo "<p>L'ajout de l'utilisateur a échoué.</p>";
    }
    $stmt->close();
    $conn->close();
}
?>
<div class ="logform">
<form method="POST" action="">
    <br>
    <input type="text" name="nom" placeholder="nom" required>
    <br>
    <input type="text" name="prenom" placeholder="prenom" required>
    <br>
    <input type="email" name="mail" placeholder="mail" required>
    <span id="email-validation-message" style="display: none;"></span>
    <br>
    <input type="password" name="password" placeholder="Password" required>
    <br>
    <button type="submit" name="action" value="ajouter">Ajouter</button>
    <button type="button" onclick="document.getElementById('userAddModal').style.display='none';">Annuler</button>
</form>
</div>

<script>
        document.querySelector('input[name="mail"]').addEventListener('input', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const validationMessage = document.getElementById('email-validation-message');
            if (email && !emailRegex.test(email)) {
                validationMessage.style.display='block';
                validationMessage.textContent = "Please enter a valid email address";
                validationMessage.style.color = "red";
                this.setCustomValidity("Invalid email format");
            } else {
                validationMessage.textContent = "";
                validationMessage.style.display='none';
                this.setCustomValidity("");
            }
        });
</script>
</body>
</html>
