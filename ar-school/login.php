<?php
session_start();
require 'config/database.php';

if(isset($_POST['login'])){
    $role = $_POST['role'];
    $code = $_POST['code_acces'];
    
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE role=? AND code_acces=? AND actif=1");
    $stmt->execute([$role, $code]);
    $user = $stmt->fetch();
    
    if($user){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nom'] = $user['nom'];
        
        if($role=='directeur'){
            header("Location: dashboard.php?page=accueil");
        }elseif($role=='prof'){
            header("Location: dashboard.php?page=notes");
        }elseif($role=='comptable'){
            header("Location: dashboard.php?page=compta");
        }elseif($role=='educateur'){
            header("Location: dashboard.php?page=conduite");
        }
        exit;
    }else{
        $erreur = "Code ou rôle incorrect";
    }
}

if(isset($_GET['logout'])){
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Connexion AR School</title>
<style>
body{display:flex;align-items:center;justify-content:center;height:100vh;background:#f5f7fa;font-family:Arial;margin:0}
.login{background:white;padding:40px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);width:350px}
h2{text-align:center;color:#2563eb;margin-bottom:20px}
input,select,button{width:100%;padding:12px;margin:10px 0;border:1px solid #ddd;border-radius:6px;box-sizing:border-box}
button{background:#2563eb;color:white;border:none;cursor:pointer;font-weight:bold}
button:hover{background:#1d4ed8}
.erreur{color:red;text-align:center;background:#fee;padding:10px;border-radius:6px}
</style>
</head>
<body>
<div class="login">
<h2>📚 AR School</h2>
<?php if(isset($erreur)) echo "<p class='erreur'>$erreur</p>"; ?>
<form method="POST">
<select name="role" required>
<option value="">Choisir votre rôle</option>
<option value="directeur">Directeur</option>
<option value="prof">Professeur</option>
<option value="comptable">Comptable</option>
<option value="educateur">Educateur</option>
</select>
<input type="password" name="code_acces" placeholder="Code d'accès" required>
<button name="login">Se connecter</button>
</form>
</div>
</body>
</html>