<?php
$conn = new mysqli("localhost", "root", "", "chat_db");

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$sql = "SELECT pseudo, message, date_envoi FROM messages ORDER BY id DESC LIMIT 20";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "<p><strong>" . htmlspecialchars($row['pseudo']) . "</strong> : " .
         htmlspecialchars($row['message']) .
         "<br><span class='date'>" . $row['date_envoi'] . "</span></p>";
}

$conn->close();
?>
