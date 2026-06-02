<?php
require_once 'db.php';

try {
    echo "<h3>--- Initialisation de la Base de Données AR STOCK (Version Vierge) ---</h3>";

    // 1. Création de la table des produits (Vide)
    $sql_produits = "CREATE TABLE IF NOT EXISTS `produits` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `designation` VARCHAR(255) NOT NULL,
        `reference` VARCHAR(100) NULL,
        `categorie` VARCHAR(100) NULL,
        `prix_achat` INT DEFAULT 0,
        `prix_vente` INT DEFAULT 0,
        `quantite_stock` INT DEFAULT 0,
        `seuil_alerte` INT DEFAULT 5,
        `image_url` VARCHAR(255) DEFAULT 'default_product.png'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $pdo->exec($sql_produits);
    echo "✅ Table 'produits' configurée en ligne (Zéro produit enregistré).<br>";

    // 2. Création de la table des ventes (Vide)
    $sql_ventes = "CREATE TABLE IF NOT EXISTS `ventes` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `produit` VARCHAR(255) NULL,
        `quantite` INT DEFAULT 0,
        `montant_total` INT DEFAULT 0,
        `mode_paiement` VARCHAR(50) DEFAULT 'Espèce',
        `date_vente` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `id_utilisateur` INT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $pdo->exec($sql_ventes);
    echo "✅ Table 'ventes' configurée en ligne (Zéro vente enregistrée).<br>";

    echo "<br>🚀 **Félicitations ! Ta base de données Cloud est initialisée à blanc et prête pour l'utilisation.**";
    echo "<br>Tu peux aller sur ton application pour commencer à enregistrer tes vrais produits !";

} catch (PDOException $e) {
    die("<br>❌ Erreur durant l'initialisation : " . $e->getMessage());
}
?>