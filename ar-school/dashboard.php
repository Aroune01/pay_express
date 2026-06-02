<?php
session_start();
require 'config/database.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
$nom = $_SESSION['nom'];
$page = $_GET['page'] ?? 'accueil';
$annee = '2025-2026';
$trimestre = (int)($_GET['trimestre'] ?? 1);

// Traitement POST
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(isset($_POST['add_classe']) && $role=='directeur'){
        $pdo->prepare("INSERT INTO classes(nom) VALUES(?)")->execute([trim($_POST['nom'])]);
        header("Location:?page=classes");exit;
    }
    if(isset($_POST['add_matiere']) && $role=='directeur'){
        $pdo->prepare("INSERT INTO matieres(nom,coef) VALUES(?,?)")->execute([trim($_POST['nom']),$_POST['coef']]);
        header("Location:?page=matieres");exit;
    }
    if(isset($_POST['add_eleve']) && $role=='directeur'){
        $pdo->prepare("INSERT INTO eleves(matricule,nom,prenom,classe_id) VALUES(?,?,?,?)")
            ->execute(['ELE'.time(),trim($_POST['nom']),trim($_POST['prenom']),$_POST['classe_id']]);
        header("Location:?page=eleves&classe_id=".$_POST['classe_id']);exit;
    }
    if(isset($_POST['save_notes']) && ($role=='prof' || $role=='directeur')){
        foreach($_POST['notes'] as $eid=>$note){
            if($note!==''){
                $pdo->prepare("INSERT INTO notes(eleve_id,matiere_id,note,trimestre,annee_scolaire) VALUES(?,?,?,?,?)
                    ON DUPLICATE KEY UPDATE note=VALUES(note)")
                    ->execute([$eid,$_POST['matiere_id'],$note,$_POST['trimestre'],$annee]);
            }
        }
        header("Location:?page=notes&classe_id=".$_POST['classe_id']."&matiere_id=".$_POST['matiere_id']."&trimestre=".$_POST['trimestre']);exit;
    }
    if(isset($_POST['add_paiement']) && ($role=='comptable' || $role=='directeur')){
        $pdo->prepare("INSERT INTO paiements(eleve_id,montant,type,date_paiement,annee_scolaire) VALUES(?,?,?,?,?)")
            ->execute([$_POST['eleve_id'],$_POST['montant'],$_POST['type'],$_POST['date_paiement'],$annee]);
        header("Location:?page=compta&classe_id=".$_POST['classe_id']);exit;
    }
    if(isset($_POST['add_conduite']) && ($role=='educateur' || $role=='directeur')){
        $pdo->prepare("INSERT INTO conduite(eleve_id,motif,sanction,date_incident,annee_scolaire) VALUES(?,?,?,?,?)")
            ->execute([$_POST['eleve_id'],trim($_POST['motif']),trim($_POST['sanction']),$_POST['date_incident'],$annee]);
        header("Location:?page=conduite&classe_id=".$_POST['classe_id']);exit;
    }
}

// Suppression
if(isset($_GET['del']) && isset($_GET['table']) && $role=='directeur'){
    $pdo->prepare("DELETE FROM ".$_GET['table']." WHERE id=?")->execute([(int)$_GET['del']]);
    header("Location:?page=".$_GET['page']);exit;
}

function getClasses($pdo){return $pdo->query("SELECT * FROM classes ORDER BY nom")->fetchAll();}
function getMatieres($pdo){return $pdo->query("SELECT * FROM matieres ORDER BY nom")->fetchAll();}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>AR School</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:Arial,sans-serif;background:#f5f7fa;display:flex;height:100vh}
.sidebar{width:240px;background:#1e293b;color:white;padding:20px 0;overflow-y:auto}
.sidebar h2{padding:0 20px 20px;border-bottom:1px solid #334155}
.sidebar a{display:block;padding:12px 20px;color:#cbd5e1;text-decoration:none}
.sidebar a:hover,.sidebar a.active{background:#334155;color:white}
.sidebar .section{padding:15px 20px;font-size:12px;color:#64748b;text-transform:uppercase}
.user-info{padding:15px 20px;border-top:1px solid #334155;margin-top:20px}
.user-info small{color:#94a3b8}
.main{flex:1;overflow-y:auto;padding:30px}
.container{background:white;padding:30px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1)}
h1,h2{color:#2563eb;margin-bottom:20px}
table{width:100%;border-collapse:collapse;margin-top:20px}
th,td{padding:12px;border-bottom:1px solid #e5e7eb;text-align:left}
th{background:#f9fafb}
input,select,button,textarea{padding:10px;border:1px solid #d1d5db;border-radius:6px}
.btn{background:#2563eb;color:white;border:none;cursor:pointer;padding:10px 20px;border-radius:6px}
.btn-danger{background:#dc2626;color:white;padding:6px 12px;text-decoration:none;border-radius:4px}
.form{display:flex;gap:10px;margin:20px 0;flex-wrap:wrap}
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:30px}
.stat-card{background:#eff6ff;padding:20px;border-radius:8px;text-align:center}
.stat-card h3{margin:0;font-size:32px;color:#2563eb}
.locked{text-align:center;padding:60px;color:#64748b}
.badge{background:#fee2e2;color:#991b1b;padding:4px 8px;border-radius:4px;font-size:12px}
@media print{.sidebar,.noprint{display:none}.main{padding:0}}
</style>
</head>
<body>

<div class="sidebar">
    <h2>📚 AR School</h2>
    <div class="section">Général</div>
    <?php if($role=='directeur'): ?>
    <a href="?page=accueil" class="<?=$page=='accueil'?'active':''?>">Tableau de bord</a>
    <?php endif; ?>
    
    <div class="section">Gestion</div>
    <?php if($role=='directeur'): ?>
    <a href="?page=classes" class="<?=$page=='classes'?'active':''?>">Classes</a>
    <a href="?page=eleves" class="<?=$page=='eleves'?'active':''?>">Élèves</a>
    <a href="?page=matieres" class="<?=$page=='matieres'?'active':''?>">Matières</a>
    <?php endif; ?>
    
    <div class="section">Scolarité</div>
    <?php if($role=='prof' || $role=='directeur'): ?>
    <a href="?page=notes" class="<?=$page=='notes'?'active':''?>">Saisie Notes</a>
    <?php endif; ?>
    <?php if($role=='directeur'): ?>
    <a href="?page=bulletin" class="<?=$page=='bulletin'?'active':''?>">Bulletins</a>
    <?php endif; ?>
    <?php if($role=='educateur' || $role=='directeur'): ?>
    <a href="?page=conduite" class="<?=$page=='conduite'?'active':''?>">Conduite</a>
    <?php endif; ?>
    <?php if($role=='comptable' || $role=='directeur'): ?>
    <a href="?page=compta" class="<?=$page=='compta'?'active':''?>">Comptabilité</a>
    <?php endif; ?>
    
    <div class="user-info">
        <div><?=htmlspecialchars($nom)?></div>
        <small><?=$role?></small><br>
        <a href="login.php?logout=1" style="color:#f87171">Déconnexion</a>
    </div>
</div>

<div class="main">
<div class="container">

<?php
switch($page){

case 'accueil':
if($role!='directeur'){echo "<div class='locked'>⛔ Accès réservé au directeur</div>";break;}
$nb_classes = $pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn();
$nb_eleves = $pdo->query("SELECT COUNT(*) FROM eleves")->fetchColumn();
$nb_matieres = $pdo->query("SELECT COUNT(*) FROM matieres")->fetchColumn();
?>
<h1>Tableau de bord</h1>
<div class="stats">
    <div class="stat-card"><div>Classes</div><h3><?=$nb_classes?></h3></div>
    <div class="stat-card"><div>Élèves</div><h3><?=$nb_eleves?></h3></div>
    <div class="stat-card"><div>Matières</div><h3><?=$nb_matieres?></h3></div>
    <div class="stat-card"><div>Année</div><h3><?=$annee?></h3></div>
</div>
<?php break;

case 'classes':
if($role!='directeur'){echo "<div class='locked'>⛔ Accès réservé au directeur</div>";break;}
$classes=getClasses($pdo);
?>
<h1>Gestion des Classes</h1>
<form method="POST" class="form"><input name="nom" placeholder="Nom classe" required><button name="add_classe" class="btn">Ajouter</button></form>
<table><tr><th>Nom</th><th>Action</th></tr>
<?php foreach($classes as $c):?>
<tr><td><a href="?page=eleves&classe_id=<?=$c['id']?>"><?=htmlspecialchars($c['nom'])?></a></td>
<td><a href="?page=classes&del=<?=$c['id']?>&table=classes" class="btn-danger" onclick="return confirm('Supprimer?')">Supprimer</a></td></tr>
<?php endforeach;?></table>
<?php break;

case 'notes':
if($role!='prof' && $role!='directeur'){echo "<div class='locked'>⛔ Accès réservé aux profs</div>";break;}
$classe_id=(int)($_GET['classe_id']??0);
$matiere_id=(int)($_GET['matiere_id']??0);
$classes=getClasses($pdo);
$matieres=getMatieres($pdo);
$eleves=$notes=[];
if($classe_id){
    $stmt=$pdo->prepare("SELECT * FROM eleves WHERE classe_id=? ORDER BY nom");
    $stmt->execute([$classe_id]);
    $eleves=$stmt->fetchAll();
    if($matiere_id && $eleves){
        $ids=array_column($eleves,'id');
        $ph=implode(',',array_fill(0,count($ids),'?'));
        $stmt=$pdo->prepare("SELECT eleve_id,note FROM notes WHERE matiere_id=? AND trimestre=? AND annee_scolaire=? AND eleve_id IN($ph)");
        $stmt->execute(array_merge([$matiere_id,$trimestre,$annee],$ids));
        foreach($stmt->fetchAll() as $n){$notes[$n['eleve_id']]=$n['note'];}
    }
}
?>
<h1>Saisie des Notes - <?=$annee?></h1>
<form method="GET" class="form"><input type="hidden" name="page" value="notes">
<select name="classe_id" onchange="this.form.submit()" required><option value="">Classe</option>
<?php foreach($classes as $c):?><option value="<?=$c['id']?>" <?=$classe_id==$c['id']?'selected':''?>><?=$c['nom']?></option><?php endforeach;?>
</select>
<select name="matiere_id" onchange="this.form.submit()" required><option value="">Matière</option>
<?php foreach($matieres as $m):?><option value="<?=$m['id']?>" <?=$matiere_id==$m['id']?'selected':''?>><?=$m['nom']?> (<?=$m['coef']?>)</option><?php endforeach;?>
</select>
<select name="trimestre" onchange="this.form.submit()">
<option value="1" <?=$trimestre==1?'selected':''?>>T1</option>
<option value="2" <?=$trimestre==2?'selected':''?>>T2</option>
<option value="3" <?=$trimestre==3?'selected':''?>>T3</option>
</select></form>
<?php if($classe_id && $matiere_id && $eleves):?>
<form method="POST"><input type="hidden" name="save_notes" value="1">
<input type="hidden" name="classe_id" value="<?=$classe_id?>">
<input type="hidden" name="matiere_id" value="<?=$matiere_id?>">
<input type="hidden" name="trimestre" value="<?=$trimestre?>">
<table><tr><th>#</th><th>Élève</th><th>Note /20</th></tr>
<?php foreach($eleves as $i=>$e):?>
<tr><td><?=$i+1?></td><td><?=htmlspecialchars($e['nom'].' '.$e['prenom'])?></td>
<td><input type="number" name="notes[<?=$e['id']?>]" value="<?=htmlspecialchars($notes[$e['id']]??'')?>" min="0" max="20" step="0.25"></td></tr>
<?php endforeach;?></table>
<button class="btn" style="margin-top:20px">Enregistrer</button></form>
<?php endif; break;

case 'compta':
if($role!='comptable' && $role!='directeur'){echo "<div class='locked'>⛔ Accès réservé à la comptabilité</div>";break;}
$classe_id=(int)($_GET['classe_id']??0);
$classes=getClasses($pdo);
$eleves=[];
if($classe_id){
    $stmt=$pdo->prepare("SELECT e.*,c.nom as classe_nom FROM eleves e LEFT JOIN classes c ON e.classe_id=c.id WHERE e.classe_id=? ORDER BY e.nom");
    $stmt->execute([$classe_id]);
    $eleves=$stmt->fetchAll();
}
?>
<h1>Gestion Comptabilité - <?=$annee?></h1>
<form method="GET" class="form">
<input type="hidden" name="page" value="compta">
<select name="classe_id" onchange="this.form.submit()" required>
<option value="">Choisir une classe</option>
<?php foreach($classes as $c):?>
<option value="<?=$c['id']?>" <?=$classe_id==$c['id']?'selected':''?>><?=$c['nom']?></option>
<?php endforeach;?>
</select>
</form>
<?php if($classe_id && $eleves): ?>
<table>
<tr><th>Élève</th><th>Total payé</th><th>Action</th></tr>
<?php 
foreach($eleves as $e):
$stmt=$pdo->prepare("SELECT SUM(montant) as total FROM paiements WHERE eleve_id=? AND annee_scolaire=?");
$stmt->execute([$e['id'],$annee]);
$total=$stmt->fetchColumn()??0;
?>
<tr>
<td><?=htmlspecialchars($e['nom'].' '.$e['prenom'])?></td>
<td><strong><?=number_format($total,0,',',' ')?> FCFA</strong></td>
<td>
<form method="POST" style="display:flex;gap:5px;flex-wrap:wrap">
<input type="hidden" name="classe_id" value="<?=$classe_id?>">
<input type="hidden" name="eleve_id" value="<?=$e['id']?>">
<input type="number" name="montant" placeholder="Montant" required style="width:120px">
<select name="type" required>
<option value="Inscription">Inscription</option>
<option value="Scolarité T1">Scolarité T1</option>
<option value="Scolarité T2">Scolarité T2</option>
<option value="Scolarité T3">Scolarité T3</option>
</select>
<input type="date" name="date_paiement" value="<?=date('Y-m-d')?>" required>
<button name="add_paiement" class="btn">Valider</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
<?php break;

case 'conduite':
if($role!='educateur' && $role!='directeur'){echo "<div class='locked'>⛔ Accès réservé à l'éducateur</div>";break;}
$classe_id=(int)($_GET['classe_id']??0);
$classes=getClasses($pdo);
$eleves=[];
if($classe_id){
    $stmt=$pdo->prepare("SELECT e.* FROM eleves e WHERE e.classe_id=? ORDER BY e.nom");
    $stmt->execute([$classe_id]);
    $eleves=$stmt->fetchAll();
}
?>
<h1>Gestion Conduite - <?=$annee?></h1>
<form method="GET" class="form">
<input type="hidden" name="page" value="conduite">
<select name="classe_id" onchange="this.form.submit()" required>
<option value="">Choisir une classe</option>
<?php foreach($classes as $c):?>
<option value="<?=$c['id']?>" <?=$classe_id==$c['id']?'selected':''?>><?=$c['nom']?></option>
<?php endforeach;?>
</select>
</form>
<?php if($classe_id && $eleves): ?>
<table>
<tr><th>Élève</th><th>Incidents</th><th>Action</th></tr>
<?php 
foreach($eleves as $e):
$stmt=$pdo->prepare("SELECT * FROM conduite WHERE eleve_id=? AND annee_scolaire=? ORDER BY date_incident DESC");
$stmt->execute([$e['id'],$annee]);
$incidents=$stmt->fetchAll();
?>
<tr>
<td><?=htmlspecialchars($e['nom'].' '.$e['prenom'])?></td>
<td>
<?php if($incidents): foreach($incidents as $inc): ?>
<div style="margin-bottom:8px">
<span class="badge"><?=htmlspecialchars($inc['date_incident'])?></span> 
<strong><?=htmlspecialchars($inc['motif'])?></strong>
<?php if($inc['sanction']): ?> - <i><?=htmlspecialchars($inc['sanction'])?></i><?php endif; ?>
</div>
<?php endforeach; else: ?>
<span style="color:#10b981">Aucun incident</span>
<?php endif; ?>
</td>
<td>
<form method="POST" style="display:flex;gap:5px;flex-wrap:wrap">
<input type="hidden" name="classe_id" value="<?=$classe_id?>">
<input type="hidden" name="eleve_id" value="<?=$e['id']?>">
<input type="text" name="motif" placeholder="Motif" required style="width:180px">
<input type="text" name="sanction" placeholder="Sanction" style="width:180px">
<input type="date" name="date_incident" value="<?=date('Y-m-d')?>" required>
<button name="add_conduite" class="btn">Ajouter</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
<?php break;

default:
echo "<div class='locked'>Page non disponible</div>";
break;

}
?>
</div></div></body></html>