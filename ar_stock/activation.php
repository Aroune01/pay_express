<?php
// Connexion à la base de données
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "gestion_stock_db";

try {
    $bdd = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$message = "";

if (isset($_POST['activate'])) {
    $input_key = trim($_POST['license_key']);
    
    // On vérifie si la clé existe dans notre table settings
    $query = $bdd->prepare("SELECT * FROM settings WHERE license_key = ?");
    $query->execute([$input_key]);
    $license = $query->fetch();

    if ($license) {
        // Si la clé est valide, on active pour 30 jours à partir d'aujourd'hui
        $new_expiry = date('Y-m-d', strtotime('+30 days'));
        
        $update = $bdd->prepare("UPDATE settings SET expiry_date = ?, is_active = 1 WHERE license_key = ?");
        $update->execute([$new_expiry, $input_key]);
        
        $message = "<p style='color:green; font-weight:bold;'>Licence activée avec succès ! Redirection...</p>";
        header("Refresh: 2; url=index.php");
    } else {
        $message = "<p style='color:red; font-weight:bold;'>Clé de licence invalide. Veuillez réessayer.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Activation AR Stock</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; text-align: center; margin-top: 100px; }
        .box { background: white; padding: 30px; display: inline-block; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); }
        input[type="text"] { padding: 10px; width: 250px; font-size: 16px; text-transform: uppercase; text-align: center; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Activation de la Licence - AR Stock</h2>
        <p>Votre abonnement est expiré ou inactif. Veuillez entrer votre clé :</p>
        
        <?= $message ?>
        
        <form method="POST" action="">
            <input type="text" name="license_key" placeholder="AR-XXXX-XXXX" required><br><br>
            <button type="submit" name="activate">Activer le logiciel</button>
        </form>
    </div>
</body>
</html>