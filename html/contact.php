<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="fr"></html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

    <?php include 'phpfunc/navbar.php'; ?>    
    <h1 id="titre-centre-nautique">Contact</h1>

    <strong>Téléphone : </strong>06 95 09 74 97<br>
    <strong>Email : </strong>les.flots.bleus@gmail.com<br>
    <strong>Adresse : </strong>36 Impasse des Ecumes, 06300 Nice

    <h1 id="titre-centre-nautique">Vous souhaitez nous écrire ?</h1>
    <section id="mail">
        <form action="mailto:contact@lesflotsbleus.com" method="post" enctype="text/plain">
            <label for="name">Votre nom :</label><br>
            <input type="text" id="name" name="name" placeholder="Entrez votre nom" required><br><br>
          
            <label for="email">Votre adresse e-mail :</label><br>
            <input type="email" id="email" name="email" placeholder="Entrez votre e-mail" required><br><br>
          
            <label for="phone">Votre numéro de téléphone :</label><br>
            <input type="tel" id="phone" name="phone" placeholder="Entrez votre numéro de téléphone" required><br><br>
          
            <label for="message">Votre message :</label><br>
            <textarea id="message" name="message" rows="6" cols="50" placeholder="Écrivez votre message ici..." required></textarea><br><br>
          
            <button type="envoyer" class="bouton-envoyer">Envoyer</button>
        </form>
    </section>

    <div class="bloc-FAQ">
      <?php
        $db_host = getenv('MYSQL_HOST') ?: 'localhost';
        $db_port = getenv('MYSQL_PORT') ?: '3306';
        $servername = $db_host . ":" . $db_port;
        $db_username = getenv('MYSQL_USER') ?: 'bmanem';
        $db_password = getenv('MYSQL_PASSWORD') ?: 'jeXja8-septuf-fitsuj';
        $dbname = getenv('MYSQL_DATABASE') ?: 'bmanem';
    
        // Create connection
        $conn = new mysqli($servername, $db_username, $db_password, $dbname);
    
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        $sqlUser = "SELECT * FROM FAQ";
        $stmt = $conn->prepare($sqlUser);
        $stmt->execute();
        $resultTypes = $stmt->get_result();

        while ($res = $resultTypes->fetch_assoc()) {
          echo '<div class="item-FAQ">';
          echo '<button class="question-FAQ" id="q' . htmlspecialchars($res["idFAQ"]) . '">' . htmlspecialchars($res["question"]) . '</button>';
          echo '<div class="reponse-FAQ" id="r' . htmlspecialchars($res["idFAQ"]) . '">';
            echo '<p>' . htmlspecialchars($res["reponse"]) . '</p>';
          echo '</div>';
          echo '</div>';
        }
      ?>
    </div>

    <script src="script/display.js"></script>
    <script src="script.js"></script>
</body>
</html>