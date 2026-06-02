<?php
require 'config/database.php';
$message = '';
$classe_id = isset($_GET['classe_id']) ? (int)$_GET['classe_id'] : 0;

// Ajouter un élève
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_eleve'])) {
    $matricule = 'ELE' . time();
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $classe_id = (int)$_POST['classe_id'];
    if (!empty($nom) && !empty($prenom)) {
        $stmt = $pdo->prepare("INSERT INTO eleves (matricule, nom, prenom, classe_id) VALUES (?, ?, ?)");
        $stmt->execute([$matricule, $nom, $prenom, $classe_id]);
        header("Location: eleves.php?classe_id=$classe_id");
        exit;
    }
}

// Supprimer un élève
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    $pdo->prepare("DELETE FROM eleves WHERE id = ?")->execute([$id]);
    header("Location: eleves.php?classe_id=$classe_id");
    exit;
}

// Récupérer classe actuelle
$classe = null;
if ($classe_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
    $stmt->execute([$classe_id]);
    $classe = $stmt->fetch();
}

// Récupérer élèves de la classe
$eleves = [];
if ($classe_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM eleves WHERE classe_id = ? ORDER BY nom, prenom");
    $stmt->execute([$classe_id]);
    $eleves = $stmt->fetchAll();
}

$classes = $pdo->query("SELECT * FROM classes ORDER BY nom")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>AR School - Élèves</title>
<style>
body{font-family:Arial,sans-serif;background:#f5f7fa;margin:0;padding:40px}
.container{max-width:900px;margin:auto;background:white;padding:30px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1)}
h1,h2,h3{color:#2563eb}
table{width:100%;border-collapse:collapse;margin-top:20px}
th,td{padding:12px;text-align:left;border-bottom:1px solid #e5e7eb}
th{background:#f9fafb}
input,select,button{padding:10px;border:1px solid #d1d5db;border-radius:6px}
.btn{background:#2563eb;color:white;border:none;cursor:pointer;padding:10px 20px;border-radius:6px}
.btn:hover{background:#1d4ed8}
.btn-danger{background:#dc2626;color:white;padding:6px 12px;text-decoration:none;border-radius:4px}
.btn-danger:hover{background:#b91c1c}
.form{display:flex;gap:10px;margin:20px 0;flex-wrap:wrap}
.nav{margin-bottom:20px}
.nav a{margin-right:15px;text-decoration:none;color:#2563eb}
</style>
</head>
<body>
<div class="container">
    <div class="nav">
        <a href="index.php">← Classes</a> | <a href="eleves.php">Élèves</a>
    </div>

    <h1>👨‍🎓 Gestion des Élèves</h1>

    <h2>Choisir une classe</h2>
    <form method="GET">
        <select name="classe_id" onchange="this.form.submit()">
            <option value="">-- Sélectionner --</option>
            <?php foreach($classes as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $classe_id == $c['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['nom']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if($classe): ?>
        <h2>Élèves de <?= htmlspecialchars($classe['nom']) ?></h2>
        
        <h3>Ajouter un élève</h3>
        <form method="POST" class="form">
            <input type="hidden" name="classe_id" value="<?= $classe_id ?>">
            <input type="text" name="nom" placeholder="Nom" required>
            <input type="text" name="prenom" placeholder="Prénom" required>
            <button type="submit" name="ajouter_eleve" class="btn">Ajouter</button>
        </form>

        <table>
            <tr><th>Matricule</th><th>Nom</th><th>Prénom</th><th>Action</th></tr>
            <?php if(count($eleves) > 0): ?>
                <?php foreach($eleves as $e): ?>
                <tr>
                    <td><?= $e['matricule'] ?></td>
                    <td><?= htmlspecialchars($e['nom']) ?></td>
                    <td><?= htmlspecialchars($e['prenom']) ?></td>
                    <td><a href="?supprimer=<?= $e['id'] ?>&classe_id=<?= $classe_id ?>" class="btn-danger" onclick="return confirm('Supprimer cet élève ?')">Supprimer</a></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">Aucun élève dans cette classe</td></tr>
            <?php endif; ?>
        </table>
    <?php endif; ?>
</div>
</body>
</html>