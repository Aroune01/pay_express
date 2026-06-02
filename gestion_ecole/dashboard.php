<?php
include_once 'config/database.php';
include_once 'classes/Auth.php';
Auth::checkAuth();

$database = new Database();
$db = $database->getConnection();

$query_nb = "SELECT COUNT(*) as nb FROM etudiants";
$stmt_nb = $db->prepare($query_nb);
$stmt_nb->execute();
$nb_etudiants = $stmt_nb->fetch(PDO::FETCH_ASSOC)['nb'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gestion BTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-mortarboard-fill"></i> Gestion ESSECT POINCARE
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="etudiants.php">Étudiants</a></li>
                    <li class="nav-item"><a class="nav-link" href="classes.php">Classes</a></li>
                    <li class="nav-item"><a class="nav-link" href="notes.php">Notes</a></li>
                </ul>
                <span class="navbar-text">
                    <i class="bi bi-person-circle"></i> <?php echo $_SESSION['user_nom']; ?> | 
                    <a href="logout.php" class="text-white text-decoration-none">Déconnexion</a>
                </span>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h2><i class="bi bi-speedometer2"></i> Tableau de bord</h2>
        <p class="text-muted">Bienvenue <?php echo $_SESSION['user_nom']; ?>, gérez votre établissement ESSECT.</p>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card text-bg-primary mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Étudiants</h4>
                                <p class="card-text fs-2 fw-bold mb-0"><?php echo $nb_etudiants; ?></p>
                                <p class="card-text">Inscrits dans l'établissement</p>
                            </div>
                            <div>
                                <i class="bi bi-people-fill" style="font-size: 4rem;"></i>
                            </div>
                        </div>
                        <a href="etudiants.php" class="btn btn-light mt-3"><i class="bi bi-arrow-right-circle"></i> Gérer les étudiants</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card text-bg-success mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Classes</h4>
                                <p class="card-text fs-5 mb-0">BTS 1 & 2</p>
                                <p class="card-text">IDA, RHCOM, TH, AD, MSP, RIT, SEI</p>
                            </div>
                            <div>
                                <i class="bi bi-building" style="font-size: 4rem;"></i>
                            </div>
                        <a href="classes.php" class="btn btn-light mt-3"><i class="bi bi-arrow-right-circle"></i> Gérer les classes</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card text-bg-warning mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Notes</h4>
                                <p class="card-text fs-5 mb-0">Contrôle Continu</p>
                                <p class="card-text">Semestre 1 & 2</p>
                            </div>
                            <div>
                                <i class="bi bi-journal-text" style="font-size: 4rem;"></i>
                            </div>
                        <a href="notes.php" class="btn btn-dark mt-3"><i class="bi bi-arrow-right-circle"></i> Saisir les notes</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card text-bg-danger mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Bulletins</h4>
                                <p class="card-text fs-5 mb-0">Génération PDF</p>
                                <p class="card-text">Semestre 1 & 2 avec moyennes</p>
                            </div>
                            <div>
                                <i class="bi bi-file-earmark-pdf" style="font-size: 4rem;"></i>
                            </div>
                        </div>
                        <a href="bulletins.php" class="btn btn-light mt-3"><i class="bi bi-arrow-right-circle"></i> Générer bulletins</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>