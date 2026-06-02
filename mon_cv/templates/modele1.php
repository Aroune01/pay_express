<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 10px; 
            margin: 0; 
            padding: 25px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #1e3a8a;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 { 
            font-size: 24px; 
            margin: 0; 
            color: #1e3a8a;
            text-transform: uppercase;
        }
        .header .poste {
            font-size: 14px;
            color: #555;
            margin: 5px 0;
        }
        .contact {
            font-size: 9px;
            margin-top: 8px;
        }
        .photo {
            width: 90px;
            height: 110px;
            object-fit: cover;
            border: 2px solid #1e3a8a;
            float: right;
            margin: 0 0 10px 15px;
        }
        .section {
            margin-bottom: 12px;
        }
        .section h2 {
            font-size: 13px;
            color: #1e3a8a;
            border-bottom: 1px solid #1e3a8a;
            padding-bottom: 2px;
            margin: 10px 0 6px 0;
            text-transform: uppercase;
        }
        p { 
            margin: 2px 0; 
            line-height: 1.3; 
        }
        .exp-titre {
            font-weight: bold;
        }
        .exp-date {
            font-style: italic;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
    <?php if(!empty($data['photo_path'])): ?>
        <img src="<?=$data['photo_path']?>" class="photo">
    <?php endif; ?>

    <div class="header">
        <h1><?=htmlspecialchars($data['prenom'])?> <?=htmlspecialchars($data['nom'])?></h1>
        <div class="poste"><?=htmlspecialchars($data['titre_pro'])?></div>
        <div class="contact">
            <?=htmlspecialchars($data['adresse'])?> | 
            <?=htmlspecialchars($data['telephone'])?> | 
            <?=htmlspecialchars($data['email'])?>
        </div>
    </div>

    <div class="section">
        <h2>Profil</h2>
        <p><?=nl2br(htmlspecialchars($data['profil']))?></p>
    </div>

    <?php if(!empty($data['exp1_poste'])): ?>
    <div class="section">
        <h2>Expérience Professionnelle</h2>
        <p class="exp-titre"><?=htmlspecialchars($data['exp1_poste'])?> - <?=htmlspecialchars($data['exp1_entreprise'])?></p>
        <p class="exp-date"><?=htmlspecialchars($data['exp1_date'])?></p>
        <p><?=nl2br(htmlspecialchars($data['exp1_desc']))?></p>
    </div>
    <?php endif; ?>

    <?php if(!empty($data['form1_diplome'])): ?>
    <div class="section">
        <h2>Formation</h2>
        <p><strong><?=htmlspecialchars($data['form1_diplome'])?></strong></p>
        <p><?=htmlspecialchars($data['form1_ecole'])?> - <?=htmlspecialchars($data['form1_annee'])?></p>
    </div>
    <?php endif; ?>

    <div class="section">
        <h2>Compétences</h2>
        <p><?=nl2br(htmlspecialchars($data['competences']))?></p>
    </div>

    <div class="section">
        <h2>Langues</h2>
        <p><?=htmlspecialchars($data['langues_str'])?></p>
    </div>

    <div class="section">
        <h2>Centres d'intérêt</h2>
        <p><?=nl2br(htmlspecialchars($data['loisirs']))?></p>
    </div>
</body>
</html>