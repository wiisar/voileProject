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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    // Hash the password
    $password = hash('sha256', $password);
    // Database connection
    $db_host = getenv('MYSQL_HOST') ?: "localhost";
    $db_port = getenv('MYSQL_PORT') ?: "3306";
    $servername = $db_host . ":" . $db_port;
    $username = getenv('MYSQL_USER') ?: "bmanem";
    $password = getenv('MYSQL_PASSWORD') ?: "jeXja8-septuf-fitsuj";
    $dbname = getenv('MYSQL_DATABASE') ?: "bmanem";


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


        // $rqdt = $conn->prepare("SELECT idDroit FROM PossederDroits WHERE idAdherent = ?");
        // $rqdt->bind_param("ss", $row['idAdherent']);
        // $rqdt->execute();
        // $rqdtres = $rqdt->get_result();

        // if($rqdtres->num_rows >0){
        //     $right = $rqdtres->fetch_assoc();
        //     $_SESSION['droit']+=$right["idDroit"];
        // }
        $_SESSION['username'] = $row["mail"];
        $_SESSION['nom'] = $row["nom"];
        $_SESSION['prenom'] = $row["prenom"];
        $_SESSION['group'] = "administrateur";
        $_SESSION['success_message'] = "Bienvenue " . $row["nom"] . " " . $row["prenom"];
        // echo "<h1>". $_SESSION['droit'] . " Salut";
        header("Location: home.php");
        exit();
    } else {
        echo "<p>Invalid username or password.</p>";
    }
    // $rqdt->close();
    $stmt->close();
    $conn->close();
}
?>
<div class ="logform">


<form method="POST" action="">
    <div class="toggle-container">
        <input type="radio" id="login" name="action" value="login" checked >
        <label for="login" class="log-radio-label">Login</label>
        <input type="radio" id="signup" name="action" value="signup" >
        <label for="signup" class="log-radio-label">Sign Up</label>
    </div>
    <br>
    <input type="text" name="username" placeholder="Username" required>
    <br>
    <input type="password" name="password" placeholder="Password" required>
    <br>
    <div id="signup-fields" style="display: none;">
        <input type="password" name="confirm_password" placeholder="Confirm Password">
        <br>
        <input type="text" name="prenom" placeholder="Prenom">
        <br>
        <input type="text" name="nom" placeholder="Nom">
        <br>
        <input type="text" name="email" placeholder="Email">
        <br>
        
        
    </div>
    <input type="submit" name="send" value="Submit">
</form>
</div>

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
