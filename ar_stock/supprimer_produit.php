<?php
include('db.php');

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM produits WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: liste_produits.php");
exit();
?>