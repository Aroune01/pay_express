<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>CV Master IDA - Complet & Ordonné</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="editor">
        <h2 style="font-size: 20px;">⚙️ Panneau de Contrôle CV</h2>

        <div class="card">
            <h3>1. Identité & Bio</h3>
            <input type="file" onchange="previewImg(this)">
            <input type="text" id="in_nom" placeholder="Nom Complet" oninput="sync()">
            <input type="text" id="in_titre" placeholder="Titre (ex: Développeur d'Applications)" oninput="sync()">
            <textarea id="in_bio" placeholder="Objectif professionnel..." oninput="sync()"></textarea>
        </div>

        <div class="card">
            <h3>2. Compétences Techniques</h3>
            <input type="text" id="in_skills" placeholder="PHP, Python, MySQL, Réseaux (séparez par virgule)" oninput="sync()">
            <p style="font-size:10px; color:#999;">Ex: HTML, CSS, WinDev, Java</p>
        </div>

        <div class="card">
            <h3>3. Expériences</h3>
            <div id="exp_list">
                <div class="dynamic-item">
                    <input type="text" placeholder="Poste" class="exp_p" oninput="sync()">
                    <input type="text" placeholder="Entreprise" class="exp_e" oninput="sync()">
                    <input type="text" placeholder="Dates (ex: 2024 - Présent)" class="exp_d" oninput="sync()">
                    <textarea placeholder="Missions..." class="exp_m" oninput="sync()"></textarea>
                </div>
            </div>
            <button class="btn-add" onclick="addItem('exp_list')">+ Ajouter Expérience</button>
        </div>

        <div class="card">
            <h3>4. Langues & Loisirs</h3>
            <label>Langues</label>
            <input type="text" id="in_lang" placeholder="Français (Maternel), Anglais (Intermédiaire)" oninput="sync()">
            <label>Loisirs / Centres d'intérêt</label>
            <input type="text" id="in_hobbies" placeholder="Voyage, Football, Lecture" oninput="sync()">
        </div>

        <div class="card">
            <h3>5. Formations</h3>
            <div id="form_list">
                <div class="dynamic-item">
                    <input type="text" placeholder="Diplôme" class="f_d" oninput="sync()">
                    <input type="text" placeholder="Établissement" class="f_e" oninput="sync()">
                    <input type="text" placeholder="Année" class="f_a" oninput="sync()">
                </div>
            </div>
            <button class="btn-add" onclick="addItem('form_list')">+ Ajouter Formation</button>
        </div>

        <button class="btn-add" style="background:var(--primary); padding:15px;" onclick="window.print()">📥 TÉLÉCHARGER LE CV PDF</button>
    </div>

    <div class="preview">
        <div class="cv-page">
            <div class="cv-sidebar">
                <div class="photo-wrapper">
                    <div class="texture-ring"></div>
                    <div class="photo-inner"><img id="out_photo" style="display:none"></div>
                </div>

                <div class="cv-sec-title">Compétences</div>
                <div id="out_skills_box"></div>

                <div class="cv-sec-title">Langues</div>
                <div id="out_lang" class="cv-contact">---</div>

                <div class="cv-sec-title">Loisirs</div>
                <div id="out_hobbies" class="cv-contact">---</div>
            </div>

            <div class="cv-main">
                <h1 id="out_nom">VOTRE NOM</h1>
                <div id="out_titre" class="cv-tagline">VOTRE TITRE</div>

                <div class="cv-sec-title">À Propos</div>
                <div id="out_bio" class="block-desc">Description...</div>

                <div class="cv-sec-title">Parcours Professionnel</div>
                <div id="out_exps"></div>

                <div class="cv-sec-title">Diplômes & Formations</div>
                <div id="out_forms"></div>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>

</body>

</html>