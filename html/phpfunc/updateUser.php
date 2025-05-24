<?php
session_start();

// Vérifie si l'utilisateur est admin
if (!isset($_SESSION['group']) || $_SESSION['group'] !== "Administrateur") {
    die("Accès interdit.");
}

// Connexion à la BDD
$db_host = getenv('MYSQL_HOST') ?: 'localhost';
$db_port = getenv('MYSQL_PORT') ?: '3306';
$servername = $db_host . ":" . $db_port;
$db_username = getenv('MYSQL_USER') ?: 'bmanem';
$db_password = getenv('MYSQL_PASSWORD') ?: 'jeXja8-septuf-fitsuj';
$dbname = getenv('MYSQL_DATABASE') ?: 'bmanem';

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Vérifie la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupère les données du formulaire
$idAdherent = intval($_POST['idAdherent']);
$nom = trim($_POST['nom']);
$prenom = trim($_POST['prenom']);
$mail = trim($_POST['mail']);
$password = trim($_POST['password']);

// Vérifie les champs obligatoires
if (empty($nom) || empty($prenom) || empty($mail)) {
    die("Tous les champs (sauf mot de passe) sont obligatoires.");
}

// Si un nouveau mot de passe est fourni, le hasher
if (!empty($password)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE Adherents SET nom=?, prenom=?, mail=?, password=? WHERE idAdherent=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nom, $prenom, $mail, $hashed_password, $idAdherent);
} else {
    // Sinon, on n'update pas le mot de passe
    $sql = "UPDATE Adherents SET nom=?, prenom=?, mail=? WHERE idAdherent=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nom, $prenom, $mail, $idAdherent);
}

// Exécute la requête
if ($stmt->execute()) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
} else {
    echo "Erreur lors de la mise à jour : " . $conn->error;
}

$stmt->close();
$conn->close();
?>
