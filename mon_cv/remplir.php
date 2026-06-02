<?php $modele = $_GET['modele'] ?? 1; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer votre CV - Modèle <?=$modele?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-3xl mx-auto p-6">
        <a href="index.php" class="text-blue-600 mb-4 inline-block">← Choisir un autre modèle</a>
        <h1 class="text-2xl font-bold mb-6">Complétez votre CV - Modèle <?=$modele?></h1>
        
        <form action="generate.php" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow space-y-5">
            <input type="hidden" name="modele" value="<?=$modele?>">
            
            <h2 class="font-bold text-lg border-b pb-2">1. État Civil</h2>
            <div class="grid grid-cols-2 gap-4">
                <input name="prenom" placeholder="Prénom" required class="p-2 border rounded">
                <input name="nom" placeholder="Nom" required class="p-2 border rounded">
            </div>
            <input name="titre_pro" placeholder="Titre professionnel : ex. Assistant Administratif, Comptable Junior" required class="w-full p-2 border rounded">
            <textarea name="profil" placeholder="Résumé de profil : ex. Diplômé en Gestion, rigoureux et organisé, je recherche un poste en..." class="w-full p-2 border rounded" rows="3"></textarea>
            
            <h2 class="font-bold text-lg border-b pb-2 pt-4">2. Coordonnées</h2>
            <input name="adresse" placeholder="Adresse : Ville, Pays" required class="w-full p-2 border rounded">
            <div class="grid grid-cols-2 gap-4">
                <input name="telephone" placeholder="Téléphone : +225 07 XX XX XX XX" required class="p-2 border rounded">
                <input name="email" type="email" placeholder="Email : exemple@mail.com" required class="p-2 border rounded">
            </div>
            <label class="block">Photo professionnelle : <input type="file" name="photo" accept="image/*" class="mt-1"></label>

            <h2 class="font-bold text-lg border-b pb-2 pt-4">3. Expérience Professionnelle</h2>
            <input name="exp1_poste" placeholder="Poste occupé : ex. Stagiaire en Comptabilité" class="w-full p-2 border rounded">
            <input name="exp1_entreprise" placeholder="Entreprise / Organisation" class="w-full p-2 border rounded">
            <input name="exp1_date" placeholder="Période : ex. Janv 2023 - Juin 2024" class="w-full p-2 border rounded">
            <textarea name="exp1_desc" placeholder="Missions réalisées : ex. Saisie comptable, gestion des factures, accueil..." class="w-full p-2 border rounded"></textarea>

            <h2 class="font-bold text-lg border-b pb-2 pt-4">4. Formation</h2>
            <input name="form1_diplome" placeholder="Diplôme : ex. BTS Comptabilité et Gestion" class="w-full p-2 border rounded">
            <input name="form1_ecole" placeholder="Établissement : ex. Université de Cocody" class="w-full p-2 border rounded">
            <input name="form1_annee" placeholder="Année : ex. 2022 - 2024" class="w-full p-2 border rounded">

            <h2 class="font-bold text-lg border-b pb-2 pt-4">5. Compétences</h2>
            <textarea name="competences" placeholder="Ex: Pack Office, Comptabilité, Gestion administrative, Rédaction, Anglais professionnel, Analyse de données..." class="w-full p-2 border rounded" rows="3"></textarea>

            <h2 class="font-bold text-lg border-b pb-2 pt-4">6. Langues</h2>
            <div class="grid grid-cols-3 gap-2">
                <label><input type="checkbox" name="langues[]" value="Français"> Français</label>
                <label><input type="checkbox" name="langues[]" value="Anglais"> Anglais</label>
                <label><input type="checkbox" name="langues[]" value="Espagnol"> Espagnol</label>
                <label><input type="checkbox" name="langues[]" value="Allemand"> Allemand</label>
                <label><input type="checkbox" name="langues[]" value="Chinois"> Chinois</label>
            </div>
            <input name="autre_langue" placeholder="Autre langue + niveau" class="w-full p-2 border rounded mt-2">

            <h2 class="font-bold text-lg border-b pb-2 pt-4">7. Centres d'intérêt</h2>
            <textarea name="loisirs" placeholder="Ex: Lecture, Veille technologique, Bénévolat, Sport..." class="w-full p-2 border rounded"></textarea>

            <button type="submit" class="w-full bg-blue-600 text-white font-bold p-3 rounded-lg hover:bg-blue-700 mt-6">
                Générer mon CV en PDF →
            </button>
        </form>
    </div>
</body>
</html>