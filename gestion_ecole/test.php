<?php
include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if($db) {
    echo "Bravo ! Connexion à la base de données réussie 🎉";
} else {
    echo "Erreur de connexion";
}
?>