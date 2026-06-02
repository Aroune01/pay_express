<?php 
include('db.php'); 

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();

if(isset($_POST['modifier'])) {
    $designation = $_POST['designation'];
    $prix_vente = $_POST['prix_vente'];
    $quantite = $_POST['quantite'];

    $sql = "UPDATE produits SET designation=?, prix_vente=?, quantite_stock=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$designation, $prix_vente, $quantite, $id]);
    header("Location: liste_produits.php");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le produit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container">
        <div class="card p-4 shadow-sm border-0 mx-auto" style="max-width: 500px; border-radius: 15px;">
            <h4 class="fw-bold mb-4">Modifier : <?php echo $p['designation']; ?></h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Désignation</label>
                    <input type="text" name="designation" class="form-control" value="<?php echo $p['designation']; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Prix de vente</label>
                    <input type="number" name="prix_vente" class="form-control" value="<?php echo $p['prix_vente']; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Quantité en stock</label>
                    <input type="number" name="quantite" class="form-control" value="<?php echo $p['quantite_stock']; ?>">
                </div>
                <button type="submit" name="modifier" class="btn btn-primary w-100 fw-bold">METTRE À JOUR</button>
                <a href="liste_produits.php" class="btn btn-light w-100 mt-2">Annuler</a>
            </form>
        </div>
    </div>
</body>
</html>