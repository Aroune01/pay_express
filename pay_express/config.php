<?php
// config.php - Configuration Intelligente (Local vs Production)

// On détecte si on est sur la machine locale (XAMPP) ou sur internet (Render)
if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1') {
    // 1. CONFIGURATION LOCAL (XAMPP)
    $host = 'localhost';
    $dbname = 'gestion_paiement_db';
    $username = 'root';
    $password = '';
    define('BASE_URL', 'http://localhost/pay_express/');
} else {
    // 2. CONFIGURATION EN LIGNE (Render)
    // On récupère les accès de la vraie base de données en ligne (via variables d'environnement)
    $host = getenv('DB_HOST');
    $dbname = getenv('DB_NAME');
    $username = getenv('DB_USER');
    $password = getenv('DB_PASSWORD');
    
    // Détection automatique de ton vrai nom de domaine en ligne
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    define('BASE_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/');
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>