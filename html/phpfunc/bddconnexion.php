<?php
$db_host = getenv('MYSQL_HOST') ?: 'localhost';
$db_port = getenv('MYSQL_PORT') ?: '3306';
$servername = $db_host . ":" . $db_port;
$db_username = getenv('MYSQL_USER') ?: 'bmanem';
$db_password = getenv('MYSQL_PASSWORD') ?: 'jeXja8-septuf-fitsuj';
$dbname = getenv('MYSQL_DATABASE') ?: 'bmanem';


$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

?>