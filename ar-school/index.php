<?php
require 'config/database.php';
$message = '';

// Ajouter une classe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_classe'])) {
    $nom = trim($_POST['nom_classe']);
    if (!empty($nom)) {
        $stmt = $pdo->prepare("INSERT INTO classes (nom) VALUES (?)");
        $stmt->execute([$nom]);
        header("Location: index.php");
        exit;
    }
}

// Supprimer une classe
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    $pdo->prepare("DELETE FROM classes WHERE id = ?")->execute([$id]);
    header("Location: index.php");
    exit;
}

$classes = $pdo->query("SELECT * FROM classes ORDER BY nom ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>AR School - Classes</title>
<style>
body{font-family:Arial,sans-serif;background:#f5f7fa;margin:0;padding:40px}
.container{max-width:900px;margin:auto;background:white;padding:30px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1)}
h1,h2{color:#2563eb}
table{width:100%;border-collapse:collapse;margin-top:20px}
th,td{padding:12px;text-align:left;border-bottom:1px solid #e5e7eb}
th{background:#f9fafb}
input,button{padding:10px;border:1px solid #d1d5db;border-radius:6px}
.btn{background:#2563eb;color:white;border:none;cursor:pointer;padding:10px 20px;border-radius:6px}
.btn:hover{background:#1d4ed8}
.btn-danger{background:#dc2626;color:white;padding:6px 12px;text-decoration:none;border-radius:4px}
.btn-danger:hover{background:#b91c1c}
.form{display:flex;gap:10px;margin:20px 0}
a{color:#2563eb;text-decoration:none}
a:hover{text-decoration:underline}
.nav{margin-bottom:20px}
</style>
</head>
<body>
<div class="container">
    <div class="nav">
        <a href="index.php">Classes</a> | <a href="eleves.php">Élèves</a>
    </div>
    
    <h1>📚 AR School - Gestion des Classes</h1>
    
    <h2>Ajouter une classe</h2>
    <form method="POST" class="form">
        <input type="text" name="nom_classe" placeholder="Ex: 6ème A, Terminale D" required>
        <button type="submit" name="ajouter_classe" class="btn">Ajouter</button>
    </form>
    
    <h2>Liste des classes</h2>
    <table>
        <tr><th>ID</th><th>Nom</th><th>Action</th></tr>
        <?php if(count($classes) > 0): ?>
            <?php foreach($classes as $classe): ?>
            <tr>
                <td><?= $classe['id'] ?></td>
                <td><a href="eleves.php?classe_id=<?= $classe['id'] ?>"><?= htmlspecialchars($classe['nom']) ?></a></td>
                <td><a href="?supprimer=<?= $classe['id'] ?>" class="btn-danger" onclick="return confirm('Supprimer cette classe ?')">Supprimer</a></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">Aucune classe enregistrée</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>