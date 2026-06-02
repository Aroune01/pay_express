<?php
require 'config/database.php';

// Ajouter matière
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = trim($_POST['nom']);
    $coef = (float)$_POST['coef'];
    if (!empty($nom)) {
        $pdo->prepare("INSERT INTO matieres (nom, coef) VALUES (?, ?)")->execute([$nom, $coef]);
        header("Location: matieres.php");
        exit;
    }
}

// Supprimer matière
if (isset($_GET['supprimer'])) {
    $pdo->prepare("DELETE FROM matieres WHERE id = ?")->execute([(int)$_GET['supprimer']]);
    header("Location: matieres.php");
    exit;
}

$matieres = $pdo->query("SELECT * FROM matieres ORDER BY nom")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Matières - AR School</title>
<style>
body{font-family:Arial,sans-serif;background:#f5f7fa;margin:0;padding:40px}
.container{max-width:800px;margin:auto;background:white;padding:30px;border-radius:8px}
h1,h2{color:#2563eb}
table{width:100%;border-collapse:collapse;margin-top:20px}
th,td{padding:12px;border-bottom:1px solid #e5e7eb}
th{background:#f9fafb}
input,button{padding:10px;border:1px solid #d1d5db;border-radius:6px}
.btn{background:#2563eb;color:white;border:none;cursor:pointer}
.btn-danger{background:#dc2626;color:white;padding:6px 12px;text-decoration:none;border-radius:4px}
.form{display:flex;gap:10px;margin:20px 0}
.nav{margin-bottom:20px}
.nav a{margin-right:15px;text-decoration:none;color:#2563eb}
</style>
</head>
<body>
<div class="container">
    <div class="nav">
        <a href="index.php">Classes</a> | <a href="eleves.php">Élèves</a> | <a href="matieres.php">Matières</a> | <a href="notes.php">Notes</a>
    </div>
    
    <h1>📖 Matières</h1>
    
    <h2>Ajouter une matière</h2>
    <form method="POST" class="form">
        <input type="text" name="nom" placeholder="Nom matière" required>
        <input type="number" name="coef" placeholder="Coef" step="0.1" value="1" required>
        <button type="submit" name="ajouter" class="btn">Ajouter</button>
    </form>
    
    <h2>Liste des matières</h2>
    <table>
        <tr><th>ID</th><th>Matière</th><th>Coefficient</th><th>Action</th></tr>
        <?php foreach($matieres as $m): ?>
        <tr>
            <td><?= $m['id'] ?></td>
            <td><?= htmlspecialchars($m['nom']) ?></td>
            <td><?= $m['coef'] ?></td>
            <td><a href="?supprimer=<?= $m['id'] ?>" class="btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>