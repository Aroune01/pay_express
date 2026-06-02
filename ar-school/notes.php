<?php
require 'config/database.php';

$annee = '2025-2026'; // Change l'année ici quand il faut
$trimestre = isset($_GET['trimestre']) ? (int)$_GET['trimestre'] : 1;
$classe_id = isset($_GET['classe_id']) ? (int)$_GET['classe_id'] : 0;
$matiere_id = isset($_GET['matiere_id']) ? (int)$_GET['matiere_id'] : 0;

// Enregistrer les notes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_notes'])) {
    foreach ($_POST['notes'] as $eleve_id => $note) {
        if ($note !== '' && is_numeric($note)) {
            $stmt = $pdo->prepare("
                INSERT INTO notes (eleve_id, matiere_id, note, trimestre, annee_scolaire) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE note = VALUES(note)
            ");
            $stmt->execute([$eleve_id, $matiere_id, $note, $trimestre, $annee]);
        }
    }
    header("Location: notes.php?classe_id=$classe_id&matiere_id=$matiere_id&trimestre=$trimestre");
    exit;
}

// Récupération des données
$classes = $pdo->query("SELECT * FROM classes ORDER BY nom")->fetchAll();
$matieres = $pdo->query("SELECT * FROM matieres ORDER BY nom")->fetchAll();

$classe = $matiere = null;
$eleves = $notes = [];

if ($classe_id > 0) {
    $classe = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
    $classe->execute([$classe_id]);
    $classe = $classe->fetch();
    
    $stmt = $pdo->prepare("SELECT * FROM eleves WHERE classe_id = ? ORDER BY nom, prenom");
    $stmt->execute([$classe_id]);
    $eleves = $stmt->fetchAll();
}

if ($matiere_id > 0) {
    $matiere = $pdo->prepare("SELECT * FROM matieres WHERE id = ?");
    $matiere->execute([$matiere_id]);
    $matiere = $matiere->fetch();
    
    // Récupère les notes existantes
    if (count($eleves) > 0) {
        $ids = array_column($eleves, 'id');
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("
            SELECT eleve_id, note FROM notes 
            WHERE matiere_id = ? AND trimestre = ? AND annee_scolaire = ? 
            AND eleve_id IN ($placeholders)
        ");
        $stmt->execute(array_merge([$matiere_id, $trimestre, $annee], $ids));
        foreach ($stmt->fetchAll() as $n) {
            $notes[$n['eleve_id']] = $n['note'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Saisie Notes - AR School</title>
<style>
body{font-family:Arial,sans-serif;background:#f5f7fa;margin:0;padding:40px}
.container{max-width:1000px;margin:auto;background:white;padding:30px;border-radius:8px}
h1,h2{color:#2563eb}
table{width:100%;border-collapse:collapse;margin-top:20px}
th,td{padding:12px;text-align:left;border-bottom:1px solid #e5e7eb}
th{background:#f9fafb}
input,select,button{padding:10px;border:1px solid #d1d5db;border-radius:6px}
input[type="number"]{width:80px}
.btn{background:#2563eb;color:white;border:none;cursor:pointer;padding:10px 20px;border-radius:6px}
.btn:hover{background:#1d4ed8}
.form-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin:20px 0}
.nav{margin-bottom:20px}
.nav a{margin-right:15px;text-decoration:none;color:#2563eb}
.info{background:#eff6ff;padding:15px;border-radius:6px;margin:20px 0}
</style>
</head>
<body>
<div class="container">
    <div class="nav">
        <a href="index.php">Classes</a> | <a href="eleves.php">Élèves</a> | 
        <a href="matieres.php">Matières</a> | <a href="notes.php">Notes</a>
    </div>

    <h1>✏️ Saisie des Notes - <?= $annee ?></h1>

    <form method="GET" class="form-grid">
        <select name="classe_id" onchange="this.form.submit()" required>
            <option value="">1. Choisir Classe</option>
            <?php foreach($classes as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $classe_id == $c['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['nom']) ?>
            </option>
            <?php endforeach; ?>
        </select>

        <select name="matiere_id" onchange="this.form.submit()" required>
            <option value="">2. Choisir Matière</option>
            <?php foreach($matieres as $m): ?>
            <option value="<?= $m['id'] ?>" <?= $matiere_id == $m['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['nom']) ?> (coef <?= $m['coef'] ?>)
            </option>
            <?php endforeach; ?>
        </select>

        <select name="trimestre" onchange="this.form.submit()">
            <option value="1" <?= $trimestre==1?'selected':'' ?>>Trimestre 1</option>
            <option value="2" <?= $trimestre==2?'selected':'' ?>>Trimestre 2</option>
            <option value="3" <?= $trimestre==3?'selected':'' ?>>Trimestre 3</option>
        </select>
    </form>

    <?php if($classe && $matiere && count($eleves) > 0): ?>
        <div class="info">
            <strong>Classe :</strong> <?= htmlspecialchars($classe['nom']) ?> | 
            <strong>Matière :</strong> <?= htmlspecialchars($matiere['nom']) ?> (coef <?= $matiere['coef'] ?>) | 
            <strong>Trimestre :</strong> <?= $trimestre ?>
        </div>

        <form method="POST">
            <input type="hidden" name="save_notes" value="1">
            <table>
                <tr><th>#</th><th>Matricule</th><th>Nom Prénom</th><th>Note /20</th></tr>
                <?php foreach($eleves as $i => $e): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= $e['matricule'] ?></td>
                    <td><?= htmlspecialchars($e['nom'].' '.$e['prenom']) ?></td>
                    <td>
                        <input type="number" name="notes[<?= $e['id'] ?>]" 
                               value="<?= isset($notes[$e['id']]) ? $notes[$e['id']] : '' ?>"
                               min="0" max="20" step="0.25" placeholder="--">
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <br>
            <button type="submit" class="btn">💾 Enregistrer les notes</button>
        </form>
    <?php elseif($classe_id && $matiere_id): ?>
        <p>Aucun élève dans cette classe.</p>
    <?php endif; ?>
</div>
</body>
</html>