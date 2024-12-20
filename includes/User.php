<?php
require_once 'dbcon.php';

class User {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function login($username, $password) {
        // Check the username and password against the database
        // Example query to check if the username and password match
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // If user exists and password matches
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables after successful login
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id']; // Store user id in session (optional)
            $_SESSION['username'] = $user['username']; // Store username in session (optional)
            return true;
        }
        return false;
    }
    

    public function signup($username, $password, $role) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hashed_password, $role]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
