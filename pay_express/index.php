<?php
// On inclut le fichier de connexion à la base de données
require_once 'config.php';

$lien_genere = "";

// Si le commerçant a validé le formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $montant = intval($_POST['montant']);
    $description = htmlspecialchars($_POST['description']);
    
    // Génération d'une référence unique (ex: PAY-65a8e3f2b)
    $reference = 'PAY-' . uniqid();

    // Insertion dans la base de données
    $stmt = $pdo->prepare("INSERT INTO transactions (reference_unique, montant, description, statut) VALUES (?, ?, ?, 'EN_ATTENTE')");
    $stmt->execute([$reference, $montant, $description]);

    // On fabrique le lien unique que le client va recevoir
    $lien_genere = BASE_URL . "lien_paiement.php?ref=" . $reference;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>PayExpress - Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0">Générateur de Lien de Paiement</h4>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Montant (FCFA)</label>
                                <input type="number" name="montant" class="form-control" placeholder="Ex: 5000" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Description du produit / service</label>
                                <input type="text" name="description" class="form-control" placeholder="Ex: Abonnement AR STOCK, Sacoche..." required>
                            </div>
                            <button type="submit" class="btn btn-success w-100 btn-lg">Générer le lien</button>
                        </form>

                        <?php if (!empty($lien_genere)): ?>
                            <div class="alert alert-success mt-4">
                                <h6 class="fw-bold">Lien généré avec succès !</h6>
                                <p class="small text-muted mb-2">Copiez ce lien et envoyez-le à votre client :</p>
                                <a href="<?php echo $lien_genere; ?>" target="_blank" class="text-break fw-bold"><?php echo $lien_genere; ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>