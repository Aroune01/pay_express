<?php
// ==========================================
// 1. CONNEXION À LA BASE DE DONNÉES
// ==========================================
try {
    $pdo = new PDO("mysql:host=localhost;dbname=gestion_stock_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$date_selectionnee = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// ==========================================
// 2. REQUÊTE POUR LES 3 BLOCS (KPI)
// ==========================================
$query_stats = $pdo->prepare("
    SELECT 
        IFNULL(SUM(v.montant_total), 0) AS ca_jour,
        COUNT(v.id) AS total_transactions
    FROM ventes v
    WHERE DATE(v.date_vente) = :date_vente
");
$query_stats->execute(['date_vente' => $date_selectionnee]);
$stats = $query_stats->fetch(PDO::FETCH_ASSOC);

$benefice_net = $stats['ca_jour']; 

// ==========================================
// 3. REQUÊTE POUR LE TABLEAU DES VENTES
// ==========================================
$query_ventes = $pdo->prepare("
    SELECT 
        v.id,
        DATE_FORMAT(v.date_vente, '%H:%i') AS heure,
        v.vendeur AS designation_produit, 
        v.quantite,
        (v.montant_total / v.quantite) AS prix_unitaire,
        v.montant_total AS total_ligne,
        IFNULL(v.mode_paiement, 'Espèce') AS mode_paiement
    FROM ventes v
    WHERE DATE(v.date_vente) = :date_vente
    ORDER BY v.date_vente DESC
");
$query_ventes->execute(['date_vente' => $date_selectionnee]);
$ventes = $query_ventes->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AR STOCK - Historique Jours</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        /* Style d'impression pour masquer le bouton sur le papier */
        @media print {
            .btn, button { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body>

<div class="container py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">Ventes du <?php echo date('d/m/Y', strtotime($date_selectionnee)); ?></h2>
        <button onclick="window.print();" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
            <i class="bi bi-printer me-2"></i> Imprimer
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="p-4 bg-white border-md border-top border-primary border-4 rounded text-center shadow-sm">
                <small class="text-muted d-block text-uppercase fw-semibold">CA du jour</small>
                <h3 class="fw-bold text-primary my-2" style="font-size: 1.8rem;"><?php echo number_format($stats['ca_jour'], 0, ',', ' '); ?> FCFA</h3>
                <small class="text-muted"><?php echo $stats['total_transactions']; ?> ventes</small>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="p-4 bg-white border-md border-top border-success border-4 rounded text-center shadow-sm">
                <small class="text-muted d-block text-uppercase fw-semibold">Bénéfice net</small>
                <h3 class="fw-bold text-success my-2" style="font-size: 1.8rem;"><?php echo number_format($benefice_net, 0, ',', ' '); ?> FCFA</h3>
                <small class="text-muted">après coût d'achat</small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="p-4 bg-white border-md border-top border-info border-4 rounded text-center shadow-sm">
                <small class="text-muted d-block text-uppercase fw-semibold">Transactions</small>
                <h3 class="fw-bold text-info my-2" style="font-size: 1.8rem;"><?php echo $stats['total_transactions']; ?></h3>
                <small class="text-muted">Tous modes</small>
            </div>
        </div>
    </div>

    <div class="bg-white border rounded shadow-sm table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Heure</th>
                    <th>Produit</th>
                    <th>Qté</th>
                    <th>Prix unitaire</th>
                    <th>Total</th>
                    <th>Paiement</th>
                    <th>Vendeur</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ventes)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Aucune vente enregistrée pour ce jour.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($ventes as $vente): ?>
                        <tr>
                            <td><strong><?php echo $vente['id']; ?></strong></td>
                            <td><?php echo $vente['heure']; ?></td>
                            <td><?php echo htmlspecialchars($vente['designation_produit']); ?></td>
                            <td class="fw-bold"><?php echo $vente['quantite']; ?></td>
                            <td><?php echo number_format($vente['prix_unitaire'], 0, ',', ' '); ?> FCFA</td>
                            <td class="fw-bold text-success"><?php echo number_format($vente['total_ligne'], 0, ',', ' '); ?> FCFA</td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold">
                                    <?php echo htmlspecialchars($vente['mode_paiement']); ?>
                                </span>
                            </td>
                            <td class="text-muted"></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="d-flex justify-content-end p-3 border-top bg-light">
            <div class="p-2 border border-success bg-success-subtle text-success rounded fw-bold" style="font-size: 1.1rem;">
                Total jour : <?php echo number_format($stats['ca_jour'], 0, ',', ' '); ?> FCFA
            </div>
        </div>
    </div>
</div>

</body>
</html>