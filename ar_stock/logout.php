<?php
session_start();

// On vide toutes les variables de session
$_SESSION = array();

// On détruit complètement la session active
session_destroy();

// Redirection immédiate et propre vers le formulaire de connexion
header("Location: login.php");
exit();
?>