<?php
require 'config/database.php';
$annee = '2025-2026';
$trimestre = (int)($_GET['trimestre'] ?? 1);
$eleve_id = (int)($_GET['eleve_id'] ?? 0);

$eleves=$pdo->query("SELECT e.*,c.nom as classe_nom FROM eleves e LEFT JOIN classes c ON e.classe_id=c.id ORDER BY c.nom,e.nom")->fetchAll();
$eleve=$notes=$rang=null;
$total_points=$total_coefs=0;
$moyenne=0;

if($eleve_id){
    $stmt=$pdo->prepare("SELECT e.*,c.nom as classe_nom FROM eleves e LEFT JOIN classes c ON e.classe_id=c.id WHERE e.id=?");
    $stmt->execute([$eleve_id]);
    $eleve=$stmt->fetch();
    
    $stmt=$pdo->prepare("SELECT n.note,m.nom as matiere,m.coef FROM notes n JOIN matieres m ON n.matiere_id=m.id WHERE n.eleve_id=? AND n.trimestre=? AND n.annee_scolaire=? ORDER BY m.nom");
    $stmt->execute([$eleve_id,$trimestre,$annee]);
    $notes=$stmt->fetchAll();
    
    foreach($notes as $n){$total_points+=$n['note']*$n['coef'];$total_coefs+=$n['coef'];}
    $moyenne=$total_coefs>0?round($total_points/$total_coefs,2):0;
    
    if($eleve && $eleve['classe_id']){
        $stmt=$pdo->prepare("SELECT e.id,ROUND(SUM(n.note*m.coef)/SUM(m.coef),2) as moy FROM eleves e JOIN notes n ON e.id=n.eleve_id JOIN matieres m ON n.matiere_id=m.id WHERE e.classe_id=? AND n.trimestre=? AND n.annee_scolaire=? GROUP BY e.id ORDER BY moy DESC");
        $stmt->execute([$eleve['classe_id'],$trimestre,$annee]);
        $classement=$stmt->fetchAll();
        foreach($classement as $i=>$r){if($r['id']==$eleve_id){$rang=$i+1;break;}}
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Bulletin</title>
<style>
body{font-family:Arial,sans-serif;margin:40px}
table{width:100%;border-collapse:collapse;margin-top:20px}
th,td{padding:12px;border:1px solid #ddd}
th{background:#f5f5f5}
.moyenne-box{background:#eff6ff;padding:20px;text-align:center;margin-top:20px}
.moyenne{font-size:36px;font-weight:bold;color:#2563eb}
.btn{background:#2563eb;color:white;padding:10px 20px;border:none;cursor:pointer}
@media print{.noprint{display:none}}
</style>
</head>
<body>
<h1 class="noprint">Bulletins</h1>
<form method="GET" class="noprint">
<select name="eleve_id" onchange="this.form.submit()" required>
<option value="">Choisir élève</option>
<?php foreach($eleves as $e):?>
<option value="<?=$e['id']?>" <?=$eleve_id==$e['id']?'selected':''?>><?=$e['classe_nom'].' - '.$e['nom'].' '.$e['prenom']?></option>
<?php endforeach;?>
</select>
<select name="trimestre" onchange="this.form.submit()">
<option value="1" <?=$trimestre==1?'selected':''?>>Trimestre 1</option>
<option value="2" <?=$trimestre==2?'selected':''?>>Trimestre 2</option>
<option value="3" <?=$trimestre==3?'selected':''?>>Trimestre 3</option>
</select>
</form>

<?php if($eleve):?>
<div style="text-align:center;border-bottom:2px solid #2563eb;padding-bottom:20px">
<h2>AR SCHOOL</h2>
<p>Bulletin <?=$annee?> - Trimestre <?=$trimestre?></p>
<button onclick="window.print()" class="btn noprint">Imprimer</button>
</div>
<p><strong>Élève:</strong> <?=htmlspecialchars($eleve['nom'].' '.$eleve['prenom'])?> | 
<strong>Classe:</strong> <?=htmlspecialchars($eleve['classe_nom'])?> | 
<strong>Rang:</strong> <?=$rang?><?=$rang?'/'.count($classement):'-'?></p>
<table>
<tr><th>Matière</th><th>Note</th><th>Coef</th><th>Points</th></tr>
<?php foreach($notes as $n):?>
<tr><td><?=htmlspecialchars($n['matiere'])?></td><td><?=$n['note']?></td><td><?=$n['coef']?></td><td><?=$n['note']*$n['coef']?></td></tr>
<?php endforeach;?>
<tr style="font-weight:bold;background:#f9fafb"><td colspan="2">Total</td><td><?=$total_coefs?></td><td><?=$total_points?></td></tr>
</table>
<div class="moyenne-box">
<div>Moyenne Générale</div>
<div class="moyenne"><?=$moyenne?>/20</div>
</div>
<?php endif;?>
</body>
</html>