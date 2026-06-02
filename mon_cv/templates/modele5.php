<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        .header { background: #2563eb; color: white; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0 0 0; font-size: 14px; }
        .container { padding: 20px; }
        .section { margin-bottom: 15px; }
        .section h2 { color: #2563eb; border-bottom: 2px solid #2563eb; font-size: 14px; padding-bottom: 3px; margin-bottom: 8px; }
        .photo { float: right; width: 100px; height: 120px; object-fit: cover; border: 2px solid #2563eb; }
        .contact p { margin: 2px 0; }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?=htmlspecialchars($data['prenom'])?> <?=htmlspecialchars($data['nom'])?></h1>
        <p><?=htmlspecialchars($data['titre_pro'])?></p>
    </div>
    
    <div class="container">
        <?php if(!empty($data['photo_path'])): ?>
            <img src="<?=$data['photo_path']?>" class="photo">
        <?php endif; ?>
        
        <div class="section contact">
            <h2>COORDONNÉES</h2>
            <p><strong>Adresse :</strong> <?=htmlspecialchars($data['adresse'])?></p>
            <p><strong>Tél :</strong> <?=htmlspecialchars($data['telephone'])?></p>
            <p><strong>Email :</strong> <?=htmlspecialchars($data['email'])?></p>
        </div>
        <div class="clear"></div>

        <div class="section">
            <h2>PROFIL</h2>
            <p><?=nl2br(htmlspecialchars($data['profil']))?></p>
        </div>

        <?php if(!empty($data['exp1_poste'])): ?>
        <div class="section">
            <h2>EXPÉRIENCE PROFESSIONNELLE</h2>
            <p><strong><?=htmlspecialchars($data['exp1_poste'])?></strong> - <?=htmlspecialchars($data['exp1_entreprise'])?></p>
            <p><em><?=htmlspecialchars($data['exp1_date'])?></em></p>
            <p><?=nl2br(htmlspecialchars($data['exp1_desc']))?></p>
        </div>
        <?php endif; ?>

        <?php if(!empty($data['form1_diplome'])): ?>
        <div class="section">
            <h2>FORMATION</h2>
            <p><strong><?=htmlspecialchars($data['form1_diplome'])?></strong></p>
            <p><?=htmlspecialchars($data['form1_ecole'])?> - <?=htmlspecialchars($data['form1_annee'])?></p>
        </div>
        <?php endif; ?>

        <div class="section">
            <h2>COMPÉTENCES</h2>
            <p><?=nl2br(htmlspecialchars($data['competences']))?></p>
        </div>

        <?php if(!empty($data['langues_str'])): ?>
        <div class="section">
            <h2>LANGUES</h2>
            <p><?=htmlspecialchars($data['langues_str'])?></p>
        </div>
        <?php endif; ?>

        <?php if(!empty($data['loisirs'])): ?>
        <div class="section">
            <h2>CENTRES D'INTÉRÊT</h2>
            <p><?=nl2br(htmlspecialchars($data['loisirs']))?></p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>