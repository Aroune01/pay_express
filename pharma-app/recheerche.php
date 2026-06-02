<?php include("connexion.php"); ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Recherche de Médicaments</title>
</head>
<body>
    <h2>Rechercher un médicament</h2>
    <form method="POST">
        <input type="text" name="motcle" placeholder="Entrez le nom du médicament" required>
        <button type="submit">Rechercher</button>
    </form>

    <?php
    if (isset(_POST['motcle']))motcle = htmlspecialchars(_POST['motcle']);sql = conn->prepare("SELECT * FROM medicaments WHERE nom LIKE ?");sql->execute(["%motcleresultats = sql->fetchAll();
       if (resultats) {
            echo "<h3>Résultats :</h3>";
            echo "<ul>";
            foreach (resultats asmedoc) {
                echo "<li><strong>" . medoc['nom'] . "</strong> - Disponible à : " .medoc['pharmacie'] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "Aucun médicament trouvé.";
        }
    }
    ?>
</body>
</html>


