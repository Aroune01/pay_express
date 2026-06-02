<?php
include_once 'config/database.php';
include_once 'classes/Auth.php';
Auth::checkAuth();

$database = new Database();
$db = $database->getConnection();

$message = '';

// Récupère les classes pour le select
$query_classes = "SELECT * FROM classes ORDER BY nom_classe";
$stmt_classes = $db->prepare($query_classes);
$stmt_classes->execute();

if($_POST){
    $matricule = $_POST['matricule'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $date_naissance = $_POST['date_naissance'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $id_classe = $_POST['id_classe'];

    $query = "INSERT INTO etudiants (matricule, nom, prenom, date_naissance, email, telephone, id_classe) 
              VALUES (:matricule, :nom, :prenom, :date_naissance, :email, :telephone, :id_classe)";
    $stmt = $db->prepare($query);
    
    if($stmt->execute([
        'matricule' => $matricule,
        'nom' => $nom,
        'prenom' => $prenom,
        'date_naissance' => $date_naissance,
        'email' => $email,
        'telephone' => $telephone,
        'id_classe' => $id_classe
    ])){
        header("Location: etudiants.php?success=1");
        exit();
    } else {
        $message = "Erreur : Matricule ou email déjà utilisé.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Étudiant - Gestion BTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Gestion BTS</a>
            <span class="text-white">
                <?php echo $_SESSION['user_nom']; ?> | 
                <a href="logout.php" class="text-white">Déconnexion</a>
            </span>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h2>Ajouter un Étudiant</h2>
        
        <?php if($message): ?>
            <div class="alert alert-danger"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Matricule *</label>
                            <input type="text" name="matricule" class="form-control" placeholder="BTS25001" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Classe *</label>
                            <select name="id_classe" class="form-select" required>
                                <option value="">Choisir une classe</option>
                                <?php while($classe = $stmt_classes->fetch(PDO::FETCH_ASSOC)): ?>
                                    <option value="<?php echo $classe['id']; ?>"><?php echo $classe['nom_classe']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénom *</label>
                            <input type="text" name="prenom" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date de naissance</label>
                            <input type="date" name="date_naissance" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="telephone" class="form-control">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                    <a href="etudiants.php" class="btn btn-secondary">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>