<?php
require_once 'config/database.php';
require_once 'classes/Auth.php';

Auth::checkAuth();
$database = new Database();
$db = $database->getConnection();

$message = '';

// AJOUT D'UNE CLASSE
if(isset($_POST['ajouter'])){
    $nom_classe = $_POST['nom_classe'];
    $niveau = $_POST['niveau'];
    
    $stmt = $db->prepare("INSERT INTO classes (nom_classe, niveau) VALUES (?, ?)");
    if($stmt->execute([$nom_classe, $niveau])){
        $message = '<div class="alert alert-success">Classe ajoutée avec succès</div>';
    } else {
        $message = '<div class="alert alert-danger">Erreur lors de l\'ajout</div>';
    }
}

// SUPPRESSION D'UNE CLASSE
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $stmt = $db->prepare("DELETE FROM classes WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: classes.php');
    exit();
}

// LISTE DES CLASSES
$stmt = $db->query("SELECT * FROM classes ORDER BY niveau, nom_classe");
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Classes - ESSECT POINCARE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">ESSECT POINCARE DE BOUAKE</a>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">Retour Dashboard</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2><i class="bi bi-building"></i> Gestion des Classes</h2>
        <?php echo $message; ?>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-plus-circle"></i> Ajouter une classe
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nom de la classe</label>
                                <input type="text" name="nom_classe" class="form-control" placeholder="Ex: BTS 2 IDA" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Niveau</label>
                                <select name="niveau" class="form-select" required>
                                    <option value="">Choisir...</option>
                                    <option value="BTS 1">BTS 1</option>
                                    <option value="BTS 2">BTS 2</option>
                                    <option value="Licence 1">Licence 1</option>
                                    <option value="Licence 2">Licence 2</option>
                                    <option value="Licence 3">Licence 3</option>
                                    <option value="Master 1">Master 1</option>
                                    <option value="Master 2">Master 2</option>
                                </select>
                            </div>
                            <button type="submit" name="ajouter" class="btn btn-primary w-100">
                                <i class="bi bi-save"></i> Enregistrer
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <i class="bi bi-list"></i> Liste des classes
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nom de la classe</th>
                                    <th>Niveau</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($classes) > 0): ?>
                                    <?php foreach($classes as $cl): ?>
                                    <tr>
                                        <td><?php echo $cl['id']; ?></td>
                                        <td><?php echo $cl['nom_classe']; ?></td>
                                        <td><span class="badge bg-info"><?php echo $cl['niveau']; ?></span></td>
                                        <td class="text-center">
                                            <a href="?delete=<?php echo $cl['id']; ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Supprimer cette classe ?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Aucune classe enregistrée</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>