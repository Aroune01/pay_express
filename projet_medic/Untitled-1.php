<?php
$conn=new mysqli("locast","root","","rendez_vous");
if($conn->connect_error){
    die("connexion échouée: " . $conn->connect_error);
}
$sql="SELECT* FORM rendezvous";
$result=$conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charsert="UTF-81">
        <meta name="viewpoint" content="width=device-width,initial-scale=1.0">
        <title>Liste des rendez-vous</title>
        <style>
            table {
                width:100%
                border-collapse:collapse;
            }
            table,th,td{
                border:1px solid black;
            }
            th,td{
                padding:10px;
                text-align:left;
            }
        </style>
    </head>
    <body>
        <h1>Listes des rendez-vous</h1>
        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Date</th>
                <th>Heure</th>  
                <th>Medecin</th>
            </tr> 
<?php
if($result->0) {
    while($row=$result->fetch_assoc())
    echo"<tr>
         <td>" . $row["id"]."</td>
         <td>" . $row["nom"]."</td> 
         <td>" . $row["date"]."</td>
         <td>" . $row["heure"]."</td>
         <td>" . $row["medecin"]."</td>
         </tr>;
    } 
} else{
   echo"<tr><td colspan='5'>Auncun rendez-vous</td></tr>";       
}
?>
        </table>
</body>
</html>       
