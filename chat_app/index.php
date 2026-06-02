<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mini Chat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>💬 Espace de discussion</h1>

    <form action="chat.php" method="post">
        <input type="text" name="pseudo" placeholder="Votre pseudo" required>
        <textarea name="message" placeholder="Votre message" required></textarea>
        <button type="submit">Envoyer</button>
    </form>

    <div class="messages" id="messages">
    Chargement des messages...
</div>

<script>
function chargerMessages() {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "load_messages.php", true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            document.getElementById("messages").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}

// Charger les messages toutes les 3 secondes
setInterval(chargerMessages, 3000);

// Charger une première fois immédiatement
chargerMessages();
</script>

</body>
</html>
