<?php
if (!empty($_POST['pseudo']) && !empty($_POST['message'])) {
    $pseudo = htmlspecialchars($_POST['pseudo']);
    $message = htmlspecialchars($_POST['message']);

    $conn = new mysqli("localhost", "root", "", "chat_db");

    if ($conn->connect_error) {
        die("Erreur de connexion : " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO messages (pseudo, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $pseudo, $message);
    $stmt->execute();

    $stmt->close();
    $conn->close();
}

header("Location: index.php"); // Redirige vers l'accueil après envoi
exit();
?>
