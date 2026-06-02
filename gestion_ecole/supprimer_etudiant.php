<?php
include_once 'config/database.php';
include_once 'classes/Auth.php';
Auth::checkAuth();

if(isset($_GET['id'])){
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "DELETE FROM etudiants WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute(['id' => $_GET['id']]);
}
header("Location: etudiants.php?delete=1");
exit();
?>