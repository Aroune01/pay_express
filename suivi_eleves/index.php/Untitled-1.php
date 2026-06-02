<?php
conn = new mysqli('localhost', 'root', ”, 'suivi_eleves');

if (conn->connect_error) {
    die("Connexion échouée : " . conn->connect_error);result = conn->query("SELECT * FROM eleves");

while (row = result->fetch_assoc()) 
    echorow['nom'] . " " . row['prenom'] . " - " .row['classe'] . " - Note: " . row['note'] . "<br>";conn->close();
?>
