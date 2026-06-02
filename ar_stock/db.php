<?php
$host = "localhost";
$user = "root";
$pass = ""; // Laisse vide si tu es sur Laragon ou XAMPP par défaut
$dbname = "gestion_stock_db"; // Le nom exact vu sur ton phpMyAdmin

try {
    // En local, on retire le port spécifique et l'option SSL
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
    $bdd = $pdo;
    $conn = $pdo;

    // --- VÉRIFICATION DE LA LICENCE (ABONNEMENT) ---
    $check = $bdd->query("SELECT expiry_date, is_active FROM settings LIMIT 1");
    $data = $check->fetch(PDO::FETCH_ASSOC);

    $today = date('Y-m-d');
    $current_page = basename($_SERVER['PHP_SELF']);

    // Si la licence est expirée ET qu'on n'est pas déjà sur la page d'activation, on redirige vers l'activation
    if ((!$data || $data['is_active'] == 0 || $today > $data['expiry_date']) && $current_page != 'activation.php') {
        header("Location: activation.php");
        exit();
    }
    // --- FIN DE LA VÉRIFICATION ---

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>