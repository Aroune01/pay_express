<?php
include_once 'config/database.php';
include_once 'classes/Auth.php';
Auth::checkAuth();

$database = new Database();
$db = $database->getConnection();

$id_classe = $_GET['classe']?? null;
$id_matiere = $_GET['matiere']?? null;
$semestre = $_GET['semestre']?? 1;
$annee = '2024-2025';

// Récupère classes et matières
$stmt_classes = $db->prepare("SELECT * FROM classes ORDER BY nom_classe");
$stmt_classes->execute();
$stmt_matieres = $db->prepare("SELECT * FROM matieres ORDER BY nom_matiere");
$stmt_matieres->execute();

// Ajouter une évaluation
if(isset($_POST['add_eval'])){
    $query = "INSERT INTO notes (id_etudiant, id_matiere, semestre, libelle, type_eval, note, coefficient, annee_scolaire)
              VALUES (:id_etudiant, :id_matiere, :semestre, :libelle, :type_eval, :note, :coeff, :annee)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        'id_etudiant' => $_POST['id_etudiant'],
        'id_matiere' => $id_matiere,
        'semestre' => $semestre,
        'libelle' => $_POST['libelle'],
        'type_eval' => $_POST['type_eval'],
        'note' => str_replace(',', '.', $_POST['note']),
        'coeff' => $_POST['coefficient'],
        'annee' => $annee
    ]);
    header("Location: notes.php?classe=$id_classe&matiere=$id_matiere&semestre=$semestre");
    exit();
}

// Supprimer une note
if(isset($_GET['del_note'])){
    $del = $db->prepare("DELETE FROM notes WHERE id =?");
    $del->execute([$_GET['del_note']]);
    header("Location: notes.php?classe=$id_classe&matiere=$id_matiere&semestre=$semestre");
    exit();
}

$etudiants = [];
$evals = [];
$moyennes = [];

if($id_classe && $id_matiere){
    // Récupère étudiants
    $q_et = $db->prepare("SELECT * FROM etudiants WHERE id_classe =? ORDER BY nom, prenom");
    $q_et->execute([$id_classe]);
    $etudiants = $q_et->fetchAll(PDO::FETCH_ASSOC);

    // Récupère toutes les évaluations de cette matière/semestre
    $q_eval = $db->prepare("SELECT * FROM notes WHERE id_matiere =? AND semestre =? AND annee_scolaire =? ORDER BY id");
    $q_eval->execute([$id_matiere, $semestre, $annee]);
    $evals = $q_eval->fetchAll(PDO::FETCH_ASSOC);

    // Calcule les moyennes par étudiant
    foreach($etudiants as $et){
        $notes_et = array_filter($evals, fn($n) => $n['id_etudiant'] == $et['id']);
        $somme = 0; $somme_coeff = 0;
        foreach($notes_et as $n){
            $somme += $n['note'] * $n['coefficient'];
            $somme_coeff += $n['coefficient'];
        }
        $moyennes[$et['id']] = $somme_coeff > 0? round($somme / $somme_coeff, 2) : null;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Notes Interro/Devoir - Gestion BTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Gestion BTS</a>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">Retour Dashboard</a>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <h2><i class="bi bi-pencil-square"></i> Notes : Interrogations & Devoirs</h2>

        <!-- Filtres -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2">
                    <div class="col-md-4">
                        <select name="classe" class="form-select" required>
                            <option value="">Choisir Classe...</option>
                            <?php foreach($stmt_classes->fetchAll() as $c):?>
                                <option value="<?php echo $c['id'];?>" <?php if($id_classe==$c['id']) echo 'selected';?>>
                                    <?php echo $c['nom_classe'];?>
                                </option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="matiere" class="form-select" required>
                            <option value="">Choisir Matière...</option>
                            <?php foreach($stmt_matieres->fetchAll() as $m):?>
                                <option value="<?php echo $m['id'];?>" <?php if($id_matiere==$m['id']) echo 'selected';?>>
                                    <?php echo $m['nom_matiere'];?>
                                </option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="semestre" class="form-select">
                            <option value="1" <?php if($semestre==1) echo 'selected';?>>Semestre 1</option>
                            <option value="2" <?php if($semestre==2) echo 'selected';?>>Semestre 2</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Afficher</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if($id_classe && $id_matiere && count($etudiants) > 0):?>
        <div class="row">
            <!-- Tableau des notes -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <i class="bi bi-table"></i> Tableau des notes - Semestre <?php echo $semestre;?>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover mb-0">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>Étudiant</th>
                                        <?php 
                                        $libelles = array_unique(array_column($evals, 'libelle'));
                                        foreach($libelles as $lib):?>
                                            <th class="text-center"><?php echo $lib;?></th>
                                        <?php endforeach;?>
                                        <th class="text-center bg-warning">Moyenne</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <?php foreach($evals as $ev): 
                                            if(in_array($ev['libelle'], $libelles)){ 
                                                unset($libelles[array_search($ev['libelle'], $libelles)]);
                                        ?>
                                            <th class="text-center">
                                                <small><?php echo ucfirst($ev['type_eval']);?> x<?php echo $ev['coefficient'];?></small>
                                            </th>
                                        <?php } endforeach;?>
                                        <th class="text-center bg-warning"><small>Sur 20</small></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($etudiants as $et):?>
                                    <tr>
                                        <td><strong><?php echo strtoupper($et['nom']).' '.$et['prenom'];?></strong></td>
                                        <?php 
                                        $et_notes = array_filter($evals, fn($n) => $n['id_etudiant'] == $et['id']);
                                        foreach($evals as $ev): 
                                            $note_et = array_filter($et_notes, fn($n) => $n['libelle'] == $ev['libelle']);
                                            $note_et = reset($note_et);
                                        ?>
                                            <td class="text-center">
                                                <?php if($note_et):?>
                                                    <?php echo $note_et['note'];?>
                                                    <a href="?classe=<?php echo $id_classe;?>&matiere=<?php echo $id_matiere;?>&semestre=<?php echo $semestre;?>&del_note=<?php echo $note_et['id'];?>" 
                                                       class="text-danger ms-1" onclick="return confirm('Supprimer?')">
                                                        <i class="bi bi-x-circle"></i>
                                                    </a>
                                                <?php else: echo '-'; endif;?>
                                            </td>
                                        <?php endforeach;?>
                                        <td class="text-center fw-bold bg-warning">
                                            <?php echo $moyennes[$et['id']]?? '-';?>
                                        </td>
                                    </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire ajout note -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-plus-circle"></i> Ajouter une note
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="add_eval" value="1">
                            <div class="mb-2">
                                <label>Étudiant</label>
                                <select name="id_etudiant" class="form-select form-select-sm" required>
                                    <?php foreach($etudiants as $et):?>
                                        <option value="<?php echo $et['id'];?>">
                                            <?php echo $et['nom'].' '.$et['prenom'];?>
                                        </option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label>Libellé</label>
                                <input type="text" name="libelle" class="form-control form-control-sm" 
                                       placeholder="Ex: Interro 1" required>
                            </div>
                            <div class="mb-2">
                                <label>Type</label>
                                <select name="type_eval" class="form-select form-select-sm" required>
                                    <option value="interrogation">Interrogation</option>
                                    <option value="devoir">Devoir</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label>Note /20</label>
                                <input type="number" step="0.25" min="0" max="20" 
                                       name="note" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-3">
                                <label>Coefficient</label>
                                <select name="coefficient" class="form-select form-select-sm" required>
                                    <option value="1">x1</option>
                                    <option value="2">x2</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-save"></i> Ajouter
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif;?>
    </div>
</body>
</html>