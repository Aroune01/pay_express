<?php
include_once 'config/database.php';
include_once 'classes/Auth.php';
Auth::checkAuth();

$database = new Database();
$db = $database->getConnection();

$id = $_GET['id'];
$message = '';

// Récupère l'étudiant
$query = "SELECT * FROM etudiants WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->execute(['id' => $id]);
$etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$etudiant){
    header("Location: etudiants.php");
    exit();
}

// Récupère les classes
$query_classes = "SELECT * FROM classes ORDER BY nom_classe";
$stmt_classes = $db->prepare($query_classes);
$stmt_classes->execute();

if($_POST){
    $query_update = "UPDATE etudiants SET 
        matricule=:matricule, nom=:nom, prenom=:prenom, 
        date_naissance=:date_naissance, email=:email, 
        telephone=:telephone, id_classe=:id_classe 
        WHERE id=:id";
    
    $stmt_update = $db->prepare($query_update);
    if($stmt_update->execute([
        'matricule' => $_POST['matricule'],
        'nom' => $_POST['nom'],
        'prenom' => $_POST['prenom'],
        'date_naissance' => $_POST['date_naissance'],
        'email' => $_POST['email'],
        'telephone' => $_POST['telephone'],
        'id_classe' => $_POST['id_classe'],
        'id' => $id
    ])){
        header("Location: etudiants.php?update=1");
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
    <title>Modifier Étudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Gestion BTS</a>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h2>Modifier Étudiant</h2>
        <?php if($message): ?><div class="alert alert-danger"><?php echo $message; ?></div><?php endif; ?>
        <div class="card"><div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Matricule *</label>
                        <input type="text" name="matricule" class="form-control" value="<?php echo $etudiant['matricule']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Classe *</label>
                        <select name="id_classe" class="form-select" required>
                            <?php while($classe = $stmt_classes->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?php echo $classe['id']; ?>" <?php if($classe['id']==$etudiant['id_classe']) echo 'selected'; ?>>
                                    <?php echo $classe['nom_classe']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Nom *</label>
                        <input type="text" name="nom" class="form-control" value="<?php echo $etudiant['nom']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Prénom *</label>
                        <input type="text" name="prenom" class="form-control" value="<?php echo $etudiant['prenom']; ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Date de naissance</label>
                        <input type="date" name="date_naissance" class="form-control" value="<?php echo $etudiant['date_naissance']; ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $etudiant['email']; ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Téléphone</label>
                        <input type="text" name="telephone" class="form-control" value="<?php echo $etudiant['telephone']; ?>">
                    </div>
                <button type="submit" class="btn btn-primary">Modifier</button>
                <a href="etudiants.php" class="btn btn-secondary">Annuler</a>
            </form>
        </div></div>
    </div>
</body>
</html>