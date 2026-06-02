<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 10px; 
            margin: 0; 
            padding: 0;
        }
        .sidebar { 
            background: #ea580c; 
            color: white; 
            width: 35%; 
            height: 297mm; 
            float: left; 
            padding: 20px; 
            box-sizing: border-box; 
        }
        .main { 
            margin-left: 35%; 
            padding: 20px; 
            box-sizing: border-box; 
        }
        .sidebar h1 { 
            font-size: 20px; 
            margin: 0 0 5px 0; 
            text-transform: uppercase; 
            line-height: 1.1;
        }
        .sidebar .poste {
            font-size: 11px;
            margin-bottom: 15px;
            font-style: italic;
        }
        .sidebar h3 { 
            font-size: 11px; 
            border-bottom: 1px solid white; 
            margin: 18px 0 6px 0; 
            padding-bottom: 3px; 
            text-transform: uppercase;
        }
        .main h2 { 
            color: #ea580c; 
            border-bottom: 2px solid #ea580c; 
            font-size: 13px; 
            margin: 12px 0 6px 0; 
            text-transform: uppercase;
        }
        .photo { 
            width: 110px; 
            height: 130px; 
            object-fit: cover; 
            border: 3px solid white; 
            margin: 10px 0 15px 0; 
        }
        p { 
            margin: 2px 0; 
            line-height: 1.3; 
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <?php if(!empty($data['photo_path'])): ?>
            <img src="<?=$data['photo_path']?>" class="photo">
        <?php endif; ?>
        
        <h1><?=htmlspecialchars($data['prenom'])?><br><?=htmlspecialchars($data['nom'])?></h1>
        <p class="poste"><?=htmlspecialchars($data['titre_pro'])?></p>
        
        <h3>Contact</h3>
        <p><?=htmlspecialchars($data['adresse'])?></p>
        <p><?=htmlspecialchars($data['telephone'])?></p>
        <p><?=htmlspecialchars($data['email'])?></p>
        
        <h3>Langues</h3>
        <p><?=htmlspecialchars($data['langues_str'])?></p>
        
        <h3>Centres d'intérêt</h3>
        <p><?=nl2br(htmlspecialchars($data['loisirs']))?></p>
    </div>
    
    <div class="main">
        <h2>Profil</h2>
        <p><?=nl2br(htmlspecialchars($data['profil']))?></p>
        
        <?php if(!empty($data['exp1_poste'])): ?>
        <h2>Expérience Professionnelle</h2>
        <p><strong><?=htmlspecialchars($data['exp1_poste'])?> - <?=htmlspecialchars($data['exp1_entreprise'])?></strong></p>
        <p><em><?=htmlspecialchars($data['exp1_date'])?></em></p>
        <p><?=nl2br(htmlspecialchars($data['exp1_desc']))?></p>
        <?php endif; ?>

        <?php if(!empty($data['form1_diplome'])): ?>
        <h2>Formation</h2>
        <p><strong><?=htmlspecialchars($data['form1_diplome'])?></strong></p>
        <p><?=htmlspecialchars($data['form1_ecole'])?> - <?=htmlspecialchars($data['form1_annee'])?></p>
        <?php endif; ?>

        <h2>Compétences</h2>
        <p><?=nl2br(htmlspecialchars($data['competences']))?></p>
    </div>
</body>
</html>