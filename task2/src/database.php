<?php
class Database {
    private string $host;
    private string $user;
    private string $pass;
    private string $database;
    private ?PDO $connection = null;
    
    public function __construct(
        string $host = 'localhost',
        string $user = 'root',
        string $pass = 'root',
        string $database = 'website'
    ) {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->database = $database;
        if ($this->connection === null) {
            try {
                $this->connection = new PDO(
                    "mysql:host={$this->host};dbname={$this->database}",
                    $this->user,
                    $this->pass,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (PDOException $e) {
                throw new Exception("Connection failed: " . $e->getMessage());
            }
        }
    }
    
    public function getConnection(): PDO {
        if ($this->connection === null) {
            throw new Exception("Database connection was not established in constructor.");
        }
        return $this->connection;
    }

    public function emailExists(string $email) : bool {
        if (!isset($email) || empty($email)){
            throw new InvalidArgumentException("Missing or empty required field: $email");
        }
        try{
            $pdo = $this->getConnection();
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            return $count > 0;
        } catch(PDOException $e){
            throw new RuntimeException('Database error: ' . $e->getMessage());
        }
    }

    public function getEmailPaswd(string $email) : string|false {
        if (!isset($email) || empty($email)){
            throw new InvalidArgumentException("Missing or empty required field: $email");
        }
        try{
            $pdo = $this->getConnection();
            $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $hashedPaswd = $stmt->fetchColumn();
            return $hashedPaswd;
        } catch(PDOException $e){
            throw new RuntimeException('Database error: ' . $e->getMessage());
        }
    }

    public function insertRow(array $arguments){
        $requiredFields = ['first_name', 'last_name', 'phone', 'email', 'password'];
        foreach($requiredFields as $field){
            if (!isset($arguments[$field]) || empty($arguments[$field])){
                throw new InvalidArgumentException("Missing or empty required field: $field");
            }
        }
        try{
            $pdo = $this->getConnection();
            $stmt = $pdo->prepare('INSERT INTO users (first_name, last_name, phone_number, email, password_hash) VALUES (?, ?, ?, ?, ?)');
            
            $stmt->execute([
                $arguments['first_name'],
                $arguments['last_name'],
                $arguments['phone'],
                $arguments['email'],
                password_hash($arguments["password"], PASSWORD_DEFAULT)
            ]);

            return $pdo->lastInsertId();
        } catch(PDOException $e){
            throw new RuntimeException('Database error: ' . $e->getMessage());
        }
    }
}