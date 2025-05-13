<?php
    require_once 'phpfunc/session_handler.php'; 
    $handler = new DBSessionHandler();
    session_set_save_handler($handler, true);
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
        $_SESSION['success_message'] = "Bienvenue " . $row["nom"] . " " . $row["prenom"];
        echo "<h1>". $_SESSION['success_message'] ."</h1>";


    } else {
        echo "<p>Invalid username or password.</p>";
    }

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
        <input type="text" name="prenom" placeholder="Prenom">
        <br>
        <input type="text" name="nom" placeholder="Nom">
        <br>
        <input type="text" name="email" placeholder="Email">
        <br>
        
        <input type="password" name="confirm_password" placeholder="Confirm Password">
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
