<?php
include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// On supprime l'ancien admin s'il existe
$db->exec("DELETE FROM utilisateurs WHERE email = 'admin@bts.ci'");

// On crée le bon hash pour "admin123"
$password_clair = "admin123";
$hash = password_hash($password_clair, PASSWORD_DEFAULT);

// On insère
$sql = "INSERT INTO utilisateurs (nom_complet, email, mot_de_passe, role) 
        VALUES ('Admin', 'admin@bts.ci', :hash, 'admin')";
$stmt = $db->prepare($sql);
$stmt->execute(['hash' => $hash]);

echo "ADMIN RESET OK <br>";
echo "Email: admin@bts.ci <br>";
echo "Mot de passe: admin123 <br>";
echo "<a href='login.php'>Aller au login</a>";
?>