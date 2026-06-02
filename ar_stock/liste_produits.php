<?php
// 1. Démarrage de la session propre
session_start();

// Protection : Si l'utilisateur n'est pas connecté, redirection forcée
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id_connecte = $_SESSION['user_id'];
$nom_utilisateur = $_SESSION['user_name'];
$role_utilisateur = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : "Employé";

// 2. Connexion à la base de données (On utilise $bdd comme dans ton index.php)
try {
    $bdd = new PDO('mysql:host=127.0.0.1;dbname=gestion_stock_db;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

// 3. Calcul du nombre total de produits
$stmt_count = $bdd->query("SELECT COUNT(*) FROM produits");
$count = $stmt_count->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Commandes - AR STOCK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-bg: #ffffff;
            --primary-color: #0d6efd;
            --text-muted: #6c757d;
        }
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        
        /* Sidebar Design Blanc Chic */
        .sidebar { 
            width: 260px; 
            height: 100vh; 
            position: fixed; 
            background: var(--sidebar-bg); 
            border-right: 1px solid #e9ecef; 
        }
        .sidebar .nav-link { 
            color: #444444; 
            padding: 10px 20px; 
            display: flex; 
            align-items: center; 
            font-weight: 500;
            text-decoration: none;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { 
            background: #f0f7ff; 
            color: var(--primary-color); 
        }
        .sidebar .nav-link i { margin-right: 10px; font-size: 1.1rem; }
        .sidebar .section-title { 
            font-size: 0.75rem; 
            text-transform: uppercase; 
            color: #6c757d; 
            padding: 20px 20px 10px; 
            font-weight: bold;
        }
        
        .main-content { margin-left: 260px; padding: 30px; }
        
        /* Style des Cartes Produits */
        .header-blue { background: #2563eb; color: white; border-radius: 15px; padding: 20px; margin-bottom: 25px; }
        .card-produit { border: none; border-radius: 15px; background: #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .stock-badge { border-radius: 5px; padding: 2px 10px; font-size: 0.85rem; font-weight: bold; }
        .action-btn { border-radius: 8px; width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
        
        @media (max-width: 768px) { .sidebar { display: none; } .main-content { margin-left: 0; } }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="p-4 text-center border-bottom border-light">
            <h6 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($nom_utilisateur); ?></h6>
            <small class="text-muted d-block"><?php echo htmlspecialchars($role_utilisateur); ?></small>
        </div>
        
        <nav class="nav flex-column">
            <a class="nav-link mt-3" href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            
            <div class="section-title">Gestion des Commandes</div>
            <a class="nav-link" href="ajouter_produit.php"><i class="bi bi-plus-circle me-2 text-primary"></i> Ajouter</a>
            <a class="nav-link active fw-bold text-primary" href="liste_produits.php"><i class="bi bi-list-ul me-2"></i> Liste</a>
            
            <div class="section-title">Gestion des Ventes</div>
            <a class="nav-link" href="vente_comptoir.php"><i class="bi bi-calculator me-2"></i> Au comptoir</a>
            <a class="nav-link" href="proforma.php"><i class="bi bi-file-earmark-text"></i> Facture proforma</a>
            <a class="nav-link" href="historique_jours.php"><i class="bi bi-clock-history"></i> Historique jours</a>
            <a class="nav-link" href="#"><i class="bi bi-wallet2"></i> Les crédits du stock</a>
            
            <a class="nav-link text-danger mt-4" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Déconnexion</a>
        </nav>
    </div>

    <div class="main-content">
        
        <div class="header-blue d-flex justify-content-between align-items-center shadow-sm">
            <div>
                <h4 class="mb-0 fw-bold"><i class="bi bi-box-seam"></i> Mes commandes</h4>
                <small><?php echo $count; ?> produits dans votre inventaire</small>
            </div>
        </div>

        <div class="d-flex gap-2 mb-4">
            <button class="btn btn-primary btn-sm rounded-pill px-3">Tous</button>
            <button class="btn btn-light btn-sm rounded-pill px-3 shadow-sm border">En stock</button>
            <button class="btn btn-light btn-sm rounded-pill px-3 shadow-sm border">Stock faible</button>
            <button class="btn btn-light btn-sm rounded-pill px-3 shadow-sm border">Rupture</button>
        </div>

        <div class="row g-3">
            <?php
            // Sélection de tous les produits triés par ID décroissant
            $stmt = $bdd->query("SELECT * FROM produits ORDER BY id DESC");
            
            while($p = $stmt->fetch()):
                $is_low = ($p['quantite_stock'] <= $p['seuil_alerte']);
                $stock_color = $is_low ? 'text-danger bg-danger-subtle' : 'text-success bg-success-subtle';
            ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card card-produit p-3">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($p['designation']); ?></h6>
                                <small class="text-muted">COM N° <?php echo htmlspecialchars($p['id']); ?></small>
                            </div>
                            <div class="d-flex gap-1">
                                <a href="liste_produits.php" class="action-btn text-primary bg-white shadow-sm border"><i class="bi bi-arrow-repeat"></i></a>
                                <a href="modifier_produit.php?id=<?php echo $p['id']; ?>" class="action-btn text-warning bg-white shadow-sm border"><i class="bi bi-pencil"></i></a>
                                <a href="supprimer_produit.php?id=<?php echo $p['id']; ?>" onclick="return confirm('Voulez-vous vraiment supprimer ce produit ?')" class="action-btn text-danger bg-white shadow-sm border"><i class="bi bi-trash"></i></a>
                            </div>
                        </div>

                        <div class="mt-2">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Stock</span>
                                <span class="stock-badge <?php echo $stock_color; ?>"><?php echo htmlspecialchars($p['quantite_stock']); ?> unités</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Prix d'achat</span>
                                <span class="fw-bold small"><?php echo number_format($p['prix_achat'], 0, ',', ' '); ?> F</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Prix de vente</span>
                                <span class="fw-bold small"><?php echo number_format($p['prix_vente'], 0, ',', ' '); ?> F</span>
                            </div>
                            <div class="d-flex justify-content-between pt-2 border-top">
                                <span class="text-muted small">Bénéfice</span>
                                <span class="text-success fw-bold small">
                                    <?php 
                                    $benefice_unitaire = $p['prix_vente'] - $p['prix_achat'];
                                    $benefice_total = $benefice_unitaire * $p['quantite_stock'];
                                    echo number_format($benefice_total, 0, ',', ' '); 
                                    ?> F
                                </span>
                            </div>
                        </div>

                        <div class="mt-3">
                            <small class="text-muted small"><i class="bi bi-shop"></i> SOCIETE</small>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    </div>

</body>
</html>