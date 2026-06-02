<?php
include('db.php');
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    try {
        $pdo->beginTransaction();

     date_default_timezone_set('Africa/Abidjan');
        $noms_articles = "";
        $total_qte = 0;
        foreach ($data['panier'] as $item) {
            $noms_articles .= $item['nom'] . " "; 
            $total_qte += $item['qte'];
        }

        $reqVente = $pdo->prepare("INSERT INTO ventes (date_vente, montant_total, vendeur, quantite) VALUES (NOW(), ?, ?, ?)");
        $reqVente->execute([$data['total'], trim($noms_articles), $total_qte]);
        $idVente = $pdo->lastInsertId();

        // 3. Mise à jour du stock
        foreach ($data['panier'] as $item) {
            $reqStock = $pdo->prepare("UPDATE produits SET quantite_stock = quantite_stock - ? WHERE id = ?");
            $reqStock->execute([$item['qte'], $item['id']]);
        }
        // 2. Enregistrer les détails et REDUIRE le stock
        foreach ($data['panier'] as $item) {
            // On réduit la quantité dans la table produits
            $reqStock = $pdo->prepare("UPDATE produits SET quantite_stock = quantite_stock - ? WHERE id = ?");
            $reqStock->execute([$item['qte'], $item['id']]);
        }

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>