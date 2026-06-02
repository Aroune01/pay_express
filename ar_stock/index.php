<?php
session_start();

// 1. Protection : Si l'utilisateur n'est pas connecté, retour forcé à la page login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Récupération des informations de l'utilisateur connecté
$id_connecte = $_SESSION['user_id'];
$nom_utilisateur = $_SESSION['user_name'];
$role_utilisateur = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : "Employé";

// 3. Connexion à la base de données
try {
    $bdd = new PDO('mysql:host=127.0.0.1;dbname=gestion_stock_db;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

// ------------------------------------------------------------------
// REQUÊTES GLOBALISÉES (Pour coller exactement avec l'historique)
// ------------------------------------------------------------------

// CA du jour global
$stmt_ca = $bdd->query("SELECT SUM(montant_total) AS ca_du_jour FROM ventes WHERE DATE(date_vente) = CURDATE()");
$res_ca = $stmt_ca->fetch();
$ca_du_jour = $res_ca['ca_du_jour'] ?? 0;

// Bénéfice restant global
$stmt_ben = $bdd->query("SELECT SUM(montant_total) AS benefice_total FROM ventes WHERE DATE(date_vente) = CURDATE()");
$res_ben = $stmt_ben->fetch();
$benefice = $res_ben['benefice_total'] ?? 0;

// Nombre total de produits en stock
$stmt_prod = $bdd->query("SELECT COUNT(*) AS total_produits FROM produits");
$res_prod = $stmt_prod->fetch();
$total_produits = $res_prod['total_produits'] ?? 0;

// Liste des ventes du jour (sans le filtre id_utilisateur pour que ça s'affiche !)
$stmt_ventes = $bdd->query("SELECT * FROM ventes WHERE DATE(date_vente) = CURDATE() ORDER BY id DESC LIMIT 10");
$liste_ventes = $stmt_ventes->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AR STOCK - Dashboard Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-bg: #ffffff;
            --primary-color: #0d6efd;
            --text-muted: #6c757d;
        }
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }
        
        /* Sidebar Design Blanc Chic Uniforme */
        .sidebar { 
            width: 260px; 
            height: 100vh; 
            position: fixed; 
            background: var(--sidebar-bg); 
            border-right: 1px solid #e9ecef; 
            box-shadow: 2px 0 5px rgba(0,0,0,0.02);
        }
        .sidebar .nav-link { 
            color: #444444; 
            padding: 10px 20px; 
            display: flex; 
            align-items: center; 
            font-weight: 500;
            text-decoration: none;
        }
        .sidebar .nav-link:hover { 
            background: #f0f7ff; 
            color: var(--primary-color); 
        }
        .sidebar .nav-link i { margin-right: 10px; font-size: 1.1rem; }
        .sidebar .section-title { 
            font-size: 0.75rem; 
            text-transform: uppercase; 
            color: var(--text-muted); 
            padding: 20px 20px 10px; 
            font-weight: bold;
        }
        
        .main-content { margin-left: 260px; padding: 30px; }
        .stat-card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="p-4 text-center border-bottom border-light">
            <h6 class="text-dark fw-bold mb-0"><?php echo htmlspecialchars($nom_utilisateur); ?></h6>
            <small class="text-muted d-block"><?php echo htmlspecialchars($role_utilisateur); ?></small>
        </div>
        
        <nav class="nav flex-column">
            <a class="nav-link mt-3 active fw-bold text-primary" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
            
            <div class="section-title">Gestion des Commandes</div>
            <a class="nav-link" href="ajouter_produit.php"><i class="bi bi-plus-circle text-primary"></i> Ajouter</a>
            <a class="nav-link" href="liste_produits.php"><i class="bi bi-list-ul"></i> Liste</a>
            
            <div class="section-title">Gestion des Ventes</div>
            <a class="nav-link" href="vente_comptoir.php"><i class="bi bi-calculator"></i> Au comptoir</a>
            <a class="nav-link" href="proforma.php"><i class="bi bi-file-earmark-text"></i> Facture proforma</a>
            <a class="nav-link" href="historique_jours.php"><i class="bi bi-clock-history"></i> Historique jours</a>
            <a class="nav-link" href="#"><i class="bi bi-wallet2"></i> Les crédits du stock</a>
            
            <a class="nav-link text-danger mt-4" href="logout.php"><i class="bi bi-box-arrow-right"></i> Déconnexion</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark">Tableau de bord</h4>
            <div class="text-end">
                <span class="fw-semibold text-dark"><?php echo htmlspecialchars($nom_utilisateur); ?></span>
                <small class="text-muted d-block" style="font-size: 0.8rem;"><?php echo htmlspecialchars($role_utilisateur); ?></small>
            </div>
        </div>

        <div class="row g-3 mb-4 text-center">
            <div class="col-md-4">
                <div class="p-4 bg-white border-top border-primary border-4 rounded stat-card">
                    <small class="text-muted d-block text-uppercase fw-semibold">CA du jour</small>
                    <h3 class="fw-bold text-primary my-2" style="font-size: 1.8rem;">
                        <?php echo number_format($ca_du_jour, 0, ',', ' '); ?> FCFA
                    </h3>
                    <small class="text-muted">En direct du comptoir</small>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="p-4 bg-white border-top border-success border-4 rounded stat-card">
                    <small class="text-muted d-block text-uppercase fw-semibold">Bénéfice restant</small>
                    <h3 class="fw-bold text-success my-2" style="font-size: 1.8rem;">
                        <?php echo number_format($benefice, 0, ',', ' '); ?> FCFA
                    </h3>
                    <small class="text-muted">après dépenses</small>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="p-4 bg-white border-top border-info border-4 rounded stat-card">
                    <small class="text-muted d-block text-uppercase fw-semibold">Total Produits</small>
                    <h3 class="fw-bold text-info my-2" style="font-size: 1.8rem;">
                        <?php echo $total_produits; ?>
                    </h3>
                    <small class="text-muted">Articles enregistrés</small>
                </div>
            </div>
        </div>

        <div class="bg-white border border-light-subtle rounded shadow-sm p-3 card overflow-hidden">
            <div class="card-header bg-transparent border-0 text-dark py-3 fw-bold text-start">
                <i class="bi bi-cart-check me-2 text-primary"></i> Ventes du jour
            </div>
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
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
                    <?php if (count($liste_ventes) > 0): ?>
                        <?php foreach ($liste_ventes as $v): ?>
                            <?php 
                            $pu = $v['quantite'] > 0 ? ($v['montant_total'] / $v['quantite']) : 0;
                            ?>
                            <tr>
                                <td><strong><?php echo $v['id']; ?></strong></td>
                                <td><?php echo date('H:i', strtotime($v['date_vente'])); ?></td>
                                <td><?php echo htmlspecialchars($v['produit'] ?? 'Produit'); ?></td>
                                <td class="fw-bold"><?php echo $v['quantite']; ?></td>
                                <td><?php echo number_format($pu, 0, ',', ' '); ?> FCFA</td>
                                <td class="fw-bold text-success"><?php echo number_format($v['montant_total'], 0, ',', ' '); ?> FCFA</td>
                                <td>
                                    <span class="badge bg-success text-white px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.8rem;">
                                        <?php echo htmlspecialchars($v['mode_paiement'] ?? 'Espèce'); ?>
                                    </span>
                                </td>
                                <td class="text-muted"><?php echo htmlspecialchars($nom_utilisateur); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Aucune vente enregistrée aujourd'hui.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end p-3 bg-transparent mt-3">
            <div class="p-2 border border-success bg-success-subtle text-success rounded fw-bold">
                TOTAL DU JOUR : <?php echo number_format($ca_du_jour, 0, ',', ' '); ?> FCFA
            </div>
        </div>
    </div>

</body>
</html>