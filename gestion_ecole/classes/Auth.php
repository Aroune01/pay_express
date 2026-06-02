<?php
class Auth {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function login($email, $password) {
        $query = "SELECT * FROM utilisateurs WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $row['mot_de_passe'])) {
                session_start();
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_role'] = $row['role'];
                $_SESSION['user_nom'] = $row['nom_complet'];
                $_SESSION['user_email'] = $row['email'];
                return true;
            }
        }
        return false;
    }
    
    public static function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if(!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }
    }
    
    public static function logout() {
        session_start();
        session_destroy();
        header("Location: login.php");
        exit();
    }
}
?>