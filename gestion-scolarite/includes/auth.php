<?php
class Auth {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function login($pseudo, $password) {
        $query = "SELECT * FROM login WHERE pseudo = :pseudo AND passe = :password";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':pseudo', $pseudo);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Set session variables
            $_SESSION['user_id'] = $user['Num'];
            $_SESSION['user_type'] = $user['type'];
            $_SESSION['username'] = $user['pseudo'];
            $_SESSION['login_time'] = time();
            
            // Update last login time (if you want to track this)
            $this->updateLastLogin($user['id']);
            
            return true;
        }
        
        return false;
    }
    
    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
    }
    
    public function hasPermission($required_type) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $user_type = $_SESSION['user_type'];
        
        // Admin has access to everything
        if ($user_type === 'admin') {
            return true;
        }
        
        // Check specific permissions
        if ($required_type === $user_type) {
            return true;
        }
        
        return false;
    }
    
    private function updateLastLogin($user_id) {
        // This would require adding a last_login column to the login table
        // For now, we'll skip this functionality
        return true;
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'type' => $_SESSION['user_type'],
            'login_time' => $_SESSION['login_time'] ?? null
        ];
    }
}
?>
