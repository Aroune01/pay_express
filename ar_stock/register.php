<?php
session_start();

try {
    $bdd = new PDO('mysql:host=127.0.0.1;dbname=gestion_stock_db;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

$message = "";
$status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $req = $bdd->prepare("INSERT INTO utilisateurs (username, password) VALUES (?, ?)");
            $req->execute([$username, $password_hash]);
            
            // Récupérer l'ID du compte qui vient d'être créé
            $user_id = $bdd->lastInsertId();

            // CONNEXION AUTOMATIQUE : On remplit la session immédiatement
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $username;
            $_SESSION['user_role'] = 'Employé';

            // Redirection directe et professionnelle vers le tableau de bord
            header("Location: index.php");
            exit();

        } catch (PDOException $e) {
            $message = ($e->getCode() == 23000) ? "Ce nom d'utilisateur existe déjà." : "Erreur lors de l'inscription.";
            $status = "danger";
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
        $status = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>AR Stock - Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #e0e0e0; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: sans-serif; }
        .register-card { background-color: #1e1e1e; border: 1px solid #2d2d2d; border-radius: 12px; padding: 40px; width: 100%; max-width: 400px; }
        .form-control { background-color: #2a2a2a; border: 1px solid #3a3a3a; color: #fff; padding: 12px; }
        .form-control:focus { background-color: #2a2a2a; color: #fff; border-color: #0d6efd; box-shadow: none; }
        .btn-register { background-color: #0d6efd; border: none; width: 100%; padding: 12px; font-weight: bold; margin-top: 15px; }
    </style>
</head>
<body>
<div class="register-card">
    <h3 class="text-center text-white mb-4">AR STOCK - Inscription</h3>
    <?php if(!empty($message)): ?>
        <div class="alert alert-<?php echo $status; ?> text-center small py-2"><?php echo $message; ?></div>
    <?php endif; ?>
    <form action="register.php" method="POST">
        <div class="mb-3">
            <label class="form-label text-secondary small">Nom d'utilisateur</label>
            <input type="text" class="form-control" name="username" required autocomplete="off">
        </div>
        <div class="mb-4">
            <label class="form-label text-secondary small">Mot de passe</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-register">Créer mon compte</button>
        <div class="text-center mt-3"><a href="login.php" class="text-decoration-none small text-primary">Se connecter plutôt</a></div>
    </form>
</div>
</body>
</html>