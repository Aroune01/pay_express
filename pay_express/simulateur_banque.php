<?php
require_once 'config.php';

// On récupère les données envoyées par le formulaire du client
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Accès refusé.");
}

$reference = htmlspecialchars($_POST['reference']);
$operateur = htmlspecialchars($_POST['operateur']);
$telephone = htmlspecialchars($_POST['telephone']);

// On va chercher la transaction pour afficher le montant sur l'écran de la "banque"
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE reference_unique = ?");
$stmt->execute([$reference]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    die("Transaction introuvable.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Passerelle de Paiement Sécurisée</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Petite animation de chargement */
        .spinner { width: 3rem; height: 3rem; }
    </style>
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5 text-center">
                
                <div class="p-4 mb-4 rounded bg-secondary">
                    <h2 class="fw-bold text-warning"><?php echo $operateur; ?> Pay</h2>
                    <p class="text-white-50 small">Sécurisation des transactions mobiles</p>
                </div>

                <div class="card bg-secondary border-0 shadow p-4 mb-4">
                    <h5 class="mb-3">Traitement de la demande...</h5>
                    <p>Un message de confirmation a été envoyé au <strong class="text-info"><?php echo $telephone; ?></strong></p>
                    
                    <div class="my-4">
                        <div class="spinner-border text-info spinner" role="status"></div>
                    </div>

                    <h3 class="fw-bold text-white mb-3"><?php echo number_format($transaction['montant'], 0, ',', ' '); ?> FCFA</h3>
                    
                    <p class="small text-white-50">Veuillez patienter 5 secondes pendant la simulation de la validation...</p>
                </div>

                <form id="form-validation" action="webhook.php" method="POST">
                    <input type="hidden" name="reference" value="<?php echo $reference; ?>">
                    <input type="hidden" name="telephone" value="<?php echo $telephone; ?>">
                    <input type="hidden" name="statut_banque" value="SUCCESS"> </form>

            </div>
        </div>
    </div>

    <script>
        // JavaScript pour attendre 5 secondes (5000 ms) puis valider automatiquement le formulaire
        setTimeout(function() {
            document.getElementById('form-validation').submit();
        }, 5000);
    </script>
</body>
</html>