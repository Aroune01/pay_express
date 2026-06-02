<?php
include_once 'config/database.php';
include_once 'classes/Auth.php';
Auth::checkAuth();

$database = new Database();
$db = $database->getConnection();

$query = "SELECT e.*, c.nom_classe FROM etudiants e 
          LEFT JOIN classes c ON e.id_classe = c.id 
          ORDER BY e.id DESC";
$stmt = $db->prepare($query);
$stmt->execute();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Étudiants - Gestion BTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
        <div class="d-flex justify-content-between mb-3">
            <h2>Liste des Étudiants</h2>
            <a href="ajouter_etudiant.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Ajouter Étudiant</a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <table id="tableEtudiants" class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Matricule</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Classe</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo $row['matricule']; ?></td>
                            <td><?php echo $row['nom']; ?></td>
                            <td><?php echo $row['prenom']; ?></td>
                            <td><?php echo $row['nom_classe']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td>
                                <a href="modifier_etudiant.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></a>
                                <a href="supprimer_etudiant.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cet étudiant ?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tableEtudiants').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/fr-FR.json' },
            order: [[0, 'desc']]
        });
    });
</script>
</body>
</html>