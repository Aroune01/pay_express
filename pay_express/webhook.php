<?php
require_once 'config.php';

// On vérifie qu'on reçoit bien les données du simulateur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $reference = htmlspecialchars($_POST['reference']);
    $telephone = htmlspecialchars($_POST['telephone']);
    $statut_banque = htmlspecialchars($_POST['statut_banque']);

    // Si la banque/simulateur confirme le succès
    if ($statut_banque === 'SUCCESS') {
        
        // On met à jour la ligne dans la base de données
        $stmt = $pdo->prepare("UPDATE transactions SET statut = 'SUCCES', telephone_client = ? WHERE reference_unique = ?");
        $stmt->execute([$telephone, $reference]);
    }

    // Une fois la base de données mise à jour, on redirige le client vers sa facture
    // pour qu'il voie le message de succès.
    header("Location: " . BASE_URL . "lien_paiement.php?ref=" . $reference);
    exit();
} else {
    die("Accès interdit.");
}
?>