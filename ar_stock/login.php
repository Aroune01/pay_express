<?php
session_start();

try {
    $bdd = new PDO('mysql:host=localhost;dbname=gestion_stock_db;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        // Recherche de l'utilisateur
        $req = $bdd->prepare("SELECT * FROM utilisateurs WHERE username = ?");
        $req->execute([$username]);
        $user = $req->fetch();

        // Vérification du mot de passe haché
        if ($user && password_verify($password, $user['password'])) {
            // ON ENREGISTRE LES INFOS DANS LA SESSION
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];

            // Redirection vers le tableau de bord
            header("Location: index.php");
            exit();
        } else {
            $erreur = "Identifiants incorrects.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AR Stock - Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #e0e0e0; font-family: 'Poppins', sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .login-card { background-color: #1e1e1e; border: 1px solid #2d2d2d; border-radius: 12px; padding: 40px; width: 100%; max-width: 400px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5); }
        .brand-title { font-weight: 600; letter-spacing: 1px; color: #ffffff; text-align: center; }
        .form-control { background-color: #2a2a2a; border: 1px solid #3a3a3a; color: #ffffff; border-radius: 8px; padding: 12px; }
        .form-control:focus { background-color: #2a2a2a; border-color: #0d6efd; color: #ffffff; box-shadow: none; }
        .btn-login { background-color: #0d6efd; border: none; border-radius: 8px; padding: 12px; font-weight: 600; width: 100%; margin-top: 15px; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-title h3 mb-2">AR STOCK</div>
    <p class="text-center text-muted small mb-4">Connexion à votre espace de gestion</p>

    <?php if(!empty($erreur)): ?>
        <div class="alert alert-danger text-center small py-2"><?php echo $erreur; ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="mb-3">
            <label class="form-label text-secondary small">Nom d'utilisateur</label>
            <input type="text" class="form-control" name="username" required autocomplete="off">
        </div>
        <div class="mb-4">
            <label class="form-label text-secondary small">Mot de passe</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-login">Se connecter</button>
        <div class="text-center mt-3">
            <a href="register.php" class="text-decoration-none small text-primary">Créer un compte</a>
        </div>
    </form>
</div>

</body>
</html>