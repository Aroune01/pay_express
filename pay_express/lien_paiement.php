<?php
// On inclut la connexion à la base de données
require_once 'config.php';

// On vérifie si la référence est bien présente dans l'URL
if (!isset($_GET['ref']) || empty($_GET['ref'])) {
    die("Erreur : Référence de paiement manquante.");
}

$reference = htmlspecialchars($_GET['ref']);

// On cherche la transaction correspondante dans la base de données
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE reference_unique = ?");
$stmt->execute([$reference]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

// Si la transaction n'existe pas dans notre base
if (!$transaction) {
    die("Erreur : Ce lien de paiement n'existe pas ou a expiré.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Payer votre facture</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    /* Ce code cache tout le reste de la page lors de l'impression, sauf le reçu */
    @media print {
        body * { display: none; }
        #recu-impression, #recu-impression * { display: block; }
        #recu-impression { width: 100%; border: none !important; box-shadow: none !important; }
    }
</style>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <h5 class="text-muted small text-uppercase">Facture pour :</h5>
                        <h4 class="fw-bold mb-4 text-dark"><?php echo $transaction['description']; ?></h4>
                        
                        <div class="bg-primary text-white p-3 rounded mb-4">
                            <span class="small d-block text-white-50">Montant à verser</span>
                            <span class="display-6 fw-bold"><?php echo number_format($transaction['montant'], 0, ',', ' '); ?> FCFA</span>
                        </div>
<?php if ($transaction['statut'] === 'SUCCES'): ?>
    <div class="border border-success rounded p-3 bg-white mb-4 shadow-sm" id="recu-impression">
        <div class="text-success fw-bold mb-2">✓ TRANSACTION RÉUSSIE</div>
        <hr>
        <div class="text-start small">
            <p class="mb-1"><strong>Date :</strong> <?php echo date('d/m/Y à H:i', strtotime($transaction['date_creation'])); ?></p>
            <p class="mb-1"><strong>Client :</strong> +225 <?php echo $transaction['telephone_client']; ?></p>
            <p class="mb-1"><strong>Service :</strong> <?php echo $transaction['description']; ?></p>
            <p class="mb-1"><strong>Réf Facture :</strong> <?php echo $transaction['reference_unique']; ?></p>
        </div>
        <hr>
        <h4 class="fw-bold text-success mb-0"><?php echo number_format($transaction['montant'], 0, ',', ' '); ?> FCFA</h4>
        <div class="badge bg-success mt-2">Statut : PAYÉ</div>
    </div>

    <button onclick="window.print()" class="btn btn-outline-dark w-100 mb-3">
        🖨 Imprimer le reçu de paiement
    </button>
    
    <a href="index.php" class="btn btn-link text-muted small">Retour à l'accueil commerçant</a>

<?php else: ?>
                        
       <form action="simulateur_banque.php" method="POST" onsubmit="return validerNumero()">
    <input type="hidden" name="reference" value="<?php echo $transaction['reference_unique']; ?>">
    
    <p class="text-muted small mb-3">Choisissez votre réseau de paiement :</p>
    
    <div class="mb-3">
        <select name="operateur" class="form-select form-select-lg text-center fw-bold" required>
            <option value="Wave">Wave</option>
            <option value="Orange">Orange Money</option>
            <option value="MTN">MTN MoMo</option>
        </select>
    </div>

    <div class="mb-4">
        <label class="form-label small text-muted">Votre numéro Mobile Money (10 chiffres)</label>
        <input type="tel" name="telephone" id="telephone" class="form-control form-control-lg text-center" 
               pattern="^(01|05|07)[0-9]{8}$" 
               placeholder="Ex: 0707XXXXXX" required>
        <div class="form-text text-danger small" id="error-msg" style="display:none;">Le numéro doit commencer par 01, 05 ou 07 et faire 10 chiffres.</div>
    </div>

    <button type="submit" class="btn btn-primary w-100 btn-lg fw-bold">Valider le paiement</button>
</form>

<script>
function validerNumero() {
    var phone = document.getElementById('telephone').value;
    var regex = /^(01|05|07)[0-9]{8}$/;
    if(!regex.test(phone)) {
        document.getElementById('error-msg').style.display = 'block';
        return false;
    }
    return true;
}
</script>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer text-center text-muted small">
                        Réf : <?php echo $transaction['reference_unique']; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>