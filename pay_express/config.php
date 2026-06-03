<?php
// config.php - Configuration Intelligente (Local XAMPP vs En Ligne Render)

if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1') {
    // 1. CONFIGURATION LOCAL (Ton XAMPP actuel)
    $host = 'localhost';
    $dbname = 'gestion_paiement_db';
    $username = 'root';
    $password = '';
    
    define('BASE_URL', 'http://localhost/pay_express/');
    
    // Connexion MySQL pour le local
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erreur de connexion MySQL locale : " . $e->getMessage());
    }

} else {
    // 2. CONFIGURATION EN LIGNE (Ton serveur Render)
    // COLLER ICI l'URL que tu as copiée sur Render entre les guillemets simples
    $external_url = 'postgresql://gestion_stock_db_5q2x_user:hXdhmPFhIp8r63mjnNmbEPSNGN3zTriO@dpg-d8g5ife47okc73f0bvdg-a.oregon-postgres.render.com/gestion_stock_db_5q2x'; 
    
    define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/');

    // Connexion PostgreSQL pour la production en ligne
    try {
        $pdo = new PDO($external_url);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erreur de connexion PostgreSQL en ligne : " . $e->getMessage());
    }
}
?>