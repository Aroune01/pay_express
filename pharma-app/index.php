<?php include('db.php'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche de Médicaments</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Rechercher un médicament</h1>
    <form method="GET">
        <input type="text" name="medicament" placeholder="Nom du médicament" required>
        <button type="submit">Rechercher</button>
    </form>

    <?php
    if (isset(_GET['medicament'])) {
        nom =conn->real_escape_string(_GET['medicament']);sql = "SELECT p.nom AS pharmacie, p.adresse, d.quantite 
                FROM disponibilites d
                JOIN pharmacies p ON d.pharmacie_id = p.id
                JOIN medicaments m ON d.medicament_id = m.id
                WHERE m.nom LIKE '%nomresult = conn->query(sql);

        if (result->num_rows > 0) 
            echo "<h2>Résultats :</h2><ul>";
            while (row = result->fetch_assoc()) 
                echo "<li><strong>" .row['pharmacie'] . "</strong> - " . row['adresse'] . " (Quantité : " .row['quantite'] . ")</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Aucun résultat trouvé.</p>";
        }
    }
    ?>
</body>
</html>
