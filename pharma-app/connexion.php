<?php
host = "localhost";dbname = "pharmacie_db"; // remplace par le nom de ta base si différent
username = "root";password = "";

// Connexion à la base de données
try {
    conn = new PDO("mysql:host=host;dbname=dbname",username, password);
    // Activer les erreurs PDOconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connexion réussie";
} catch (PDOException e) 
    echo "Erreur : " .e->getMessage();
}
?>-
