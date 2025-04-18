<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr"></html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centre de voile</title>
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
        <li class="login-signup"><a href="login.php">Connexion / Inscription</a></li>
    </ul>
    <?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    // Hash the password
    $password = hash('sha256', $password);
    // Database connection
    $servername = getenv('MYSQL_HOST') .":". getenv('MYSQL_PORT');
    $db_username = getenv('MYSQL_USER');
    $db_password = getenv('MYSQL_PASSWORD');
    $dbname = getenv('MYSQL_DATABASE');


    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to check user credentials
    $stmt = $conn->prepare("SELECT * FROM Adherents WHERE mail = ? AND passwd = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<p>Login successful. <br>Welcome, ". $row["nom"] ." ". $row["prenom"]."!</p>";
        // Start a session and save user information
        $_SESSION['user_id'] = $row['idAdherent'];
        $_SESSION['user_name'] = $row['nom'] . " " . $row['prenom'];
        $_SESSION['user_email'] = $row['mail'];
        $_SESSION['password'] = $password;
        // header("refresh:3;url=accueil.php");

    } else {
        echo "<p>Invalid username or password.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
<form method="POST" action="">
    <div class="toggle-container">
        <input type="radio" id="login" name="action" value="login" checked>
        <label for="login">Login</label>
        <input type="radio" id="signup" name="action" value="signup">
        <label for="signup">Sign Up</label>
    </div>
    <br>
    <input type="text" name="username" placeholder="Username" required>
    <br>
    <input type="password" name="password" placeholder="Password" required>
    <br>
    <div id="signup-fields" style="display: none;">
        <input type="text" name="email" placeholder="Email">
        <br>
        <input type="password" name="confirm_password" placeholder="Confirm Password">
        <br>
    </div>
    <input type="submit" name="send" value="Submit">
</form>

<script>
    document.querySelectorAll('input[name="action"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'signup') {
                document.getElementById('signup-fields').style.display = 'block';
            } else {
                document.getElementById('signup-fields').style.display = 'none';
            }
        });
    });
</script>
</body>
</html>