<?php
class DBSessionHandler implements SessionHandlerInterface {
    
    private $conn;

    // Ouverture de la session
    #[\ReturnTypeWillChange]
    public function open($savePath, $sessionName): bool {
        // Connexion à la base de données
        $servername = getenv('MYSQL_HOST') . ":" . getenv('MYSQL_PORT');
        $db_username = getenv('MYSQL_USER');
        $db_password = getenv('MYSQL_PASSWORD');
        $dbname = getenv('MYSQL_DATABASE');

        $this->conn = new mysqli($servername, $db_username, $db_password, $dbname);

        if ($this->conn->connect_error) {
            return false;
        }
        return true;
    }

    // Fermeture de la session
    #[\ReturnTypeWillChange]
    public function close(): bool {
        // Fermer la connexion à la base de données
        return $this->conn->close();
    }

    // Lire une session
    #[\ReturnTypeWillChange]
    public function read(string $id): string|false {
        $stmt = $this->conn->prepare("SELECT session_data FROM sessions WHERE session_id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->bind_result($session_data);
        $stmt->fetch();
        $stmt->close();
        
        return $session_data ?? '';
    }

    // Écrire la session
    #[\ReturnTypeWillChange]
    public function write(string $id, string $data): bool {
        $stmt = $this->conn->prepare("REPLACE INTO sessions (session_id, session_data) VALUES (?, ?)");
        $stmt->bind_param("ss", $id, $data);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    // Détruire une session
    #[\ReturnTypeWillChange]
    public function destroy(string $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM sessions WHERE session_id = ?");
        $stmt->bind_param("s", $id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    // Collecter les sessions expirées
    #[\ReturnTypeWillChange]
    public function gc(int $max_lifetime): int|false {
        $stmt = $this->conn->prepare("DELETE FROM sessions WHERE last_access < NOW() - INTERVAL ? SECOND");
        $stmt->bind_param("i", $max_lifetime);
        $stmt->execute();
        $stmt->close();

        // Retourner le nombre de sessions nettoyées
        return $this->conn->affected_rows;
    }
}
?>
