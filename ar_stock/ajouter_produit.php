<?php 
include('db.php'); 

$message = "";
// Logique d'enregistrement
if(isset($_POST['enregistrer'])) {
    $designation = $_POST['designation'];
    $reference   = $_POST['reference'];
    $categorie   = $_POST['categorie'];
    $prix_achat  = $_POST['prix_achat'];
    $prix_vente  = $_POST['prix_vente'];
    $quantite    = $_POST['quantite'];
    $seuil       = $_POST['seuil'];

    try {
        $sql = "INSERT INTO produits (designation, reference, categorie, prix_achat, prix_vente, quantite_stock, seuil_alerte) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$designation, $reference, $categorie, $prix_achat, $prix_vente, $quantite, $seuil]);
        $message = "<div class='alert alert-success shadow-sm'>✔️ Produit enregistré avec succès !</div>";
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger shadow-sm'>❌ Erreur : " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter - AR STOCK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --sidebar-bg: #ffffff; --primary-color: #0d6efd; }
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .sidebar { width: 260px; height: 100vh; position: fixed; background: var(--sidebar-bg); border-right: 1px solid #e9ecef; }
        .sidebar .nav-link { color: #444; padding: 10px 20px; font-weight: 500; display: flex; align-items: center; text-decoration: none; }
        .sidebar .nav-link:hover, .sidebar .active { background: #f0f7ff; color: var(--primary-color); }
        .sidebar .section-title { font-size: 0.75rem; text-transform: uppercase; color: #6c757d; padding: 20px 20px 10px; font-weight: bold; }
        .main-content { margin-left: 260px; padding: 40px; }
        .form-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: #fff; }
        .form-control { border-radius: 10px; padding: 12px; border: 1px solid #eee; background: #f9f9f9; }
        .form-control:focus { background: #fff; box-shadow: none; border-color: var(--primary-color); }
        @media (max-width: 768px) { .sidebar { display: none; } .main-content { margin-left: 0; } }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="p-4 text-center border-bottom">
        <h5 class="fw-bold text-primary mb-0"></h5>
        <small class="text-muted">Employé</small>
    </div>
    <nav class="nav flex-column">
        <a class="nav-link mt-3" href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
        <div class="section-title">Gestion des Commandes</div>
        <a class="nav-link active" href="ajouter_produit.php"><i class="bi bi-plus-circle me-2"></i> Ajouter</a>
        <a class="nav-link" href="liste_produits.php"><i class="bi bi-list-ul me-2"></i> Liste</a>
        <div class="section-title">Gestion des Ventes</div>
        <a class="nav-link" href="vente_comptoir.php"><i class="bi bi-calculator me-2"></i> Au comptoir</a>
    </nav>
</div>

<div class="main-content">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="form-card p-4 p-md-5">
                <h3 class="fw-bold mb-4 text-dark"><i class="bi bi-box-seam text-primary me-2"></i> Enregistrer une commande</h3>
                
                <?php echo $message; ?>

                <form method="POST">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Désignation du produit *</label>
                            <input type="text" name="designation" class="form-control" placeholder="Ex: Panneau Solaire 450W" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Référence / Modèle</label>
                            <input type="text" name="reference" class="form-control" placeholder="Ex: PS-450-MAX">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Catégorie</label>
                            <select name="categorie" class="form-select form-control">
                                <option value="Solaire">Panneau Solaire</option>
                                <option value="Batterie">Batterie</option>
                                <option value="Accessoire">Accessoire</option>
                                <option value="Divers">Divers</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Prix d'achat (F CFA)</label>
                            <input type="number" name="prix_achat" class="form-control" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Prix de vente (F CFA) *</label>
                            <input type="number" name="prix_vente" class="form-control" value="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Quantité ajoutée *</label>
                            <input type="number" name="quantite" class="form-control" value="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Seuil d'alerte (Stock minimum)</label>
                            <input type="number" name="seuil" class="form-control" value="5">
                        </div>
                    </div>

                    <div class="mt-5 text-end">
                        <button type="submit" name="enregistrer" class="btn btn-primary px-5 py-3 fw-bold rounded-pill shadow">
                            <i class="bi bi-check-lg me-2"></i> VALIDER L'ENREGISTREMENT
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>