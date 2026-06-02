<!DOCTYPE html>
<html>
<head>
    <title>Ajout d'un élève</title>
</head>
<body>
    <h2>Ajouter un élève</h2>
    <form method="POST" action="">
        Nom : <input type="text" name="nom" required><br><br>
        Âge : <input type="number" name="age" required><br><br>
        Classe : <input type="text" name="classe" required><br><br>
        Sexe :
        <select name="sexe" required>
            <option value="Masculin">Masculin</option>
            <option value="Féminin">Féminin</option>
        </select><br><br>
        <input type="submit" name="ajouter" value="Ajouter">
    </form>
  <?php
    if (isset(_POST['ajouter']))conn = new mysqli("localhost", "root", "", "suivi_eleves");

        if (conn->connect_error) 
            die("Connexion échouée : " .conn->connect_error);
        }

        nom =_POST['nom'];
        age =_POST['age'];
        classe =_POST['classe'];
        sexe =_POST['sexe'];

        sql = "INSERT INTO eleves (nom, age, classe, sexe) VALUES ('nom', age, 'classe', '$sexe')";
        if (conn->query(sql) === TRUE) 
            echo "Élève ajouté avec succès.";
         else 
            echo "Erreur : " .conn->error;
        }

        $conn->close();
    }
    ?>
</body>
</html>
