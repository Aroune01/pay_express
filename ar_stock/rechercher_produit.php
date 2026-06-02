<?php
include('db.php');
if(isset($_GET['q'])) {
    $q = $_GET['q'] . '%';
    $stmt = $pdo->prepare("SELECT id, designation, prix_vente, quantite_stock FROM produits WHERE designation LIKE ? LIMIT 5");
    $stmt->execute([$q]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
?>