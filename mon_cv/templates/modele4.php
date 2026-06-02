<?php
// CHEMIN DIRECT SANS COMPOSER - 0 INSTALLATION
require_once __DIR__. '/../dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$data = $_POST; // Ou d'où viennent tes données

$nom = $data['nom']?? 'NOM ICI';
$poste = $data['poste']?? 'INTITULÉ DU POSTE ICI';
$photo = $data['photo']?? ''; 
$profil = $data['profil']?? 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Maecenas porttitor congue massa.';
$telephone = $data['telephone']?? '+6 78 65 43 21';
$site = $data['site']?? 'Emplacement du site web';
$email = $data['email']?? 'someone@example.com';

$formations = $data['formations']?? [
    ['etablissement' => '[Nom de l\'établissement]', 'date_debut' => 'Date début', 'date_fin' => 'Date fin', 'description' => 'Description formation'],
];
$experiences = $data['experiences']?? [
    ['societe' => '[Nom société]', 'poste' => '[Poste]', 'date_debut' => 'Date début', 'date_fin' => 'Date fin', 'description' => 'Description poste'],
];
$competences = $data['competences']?? [
    ['nom' => 'Compétence #1', 'niveau' => 25],
    ['nom' => 'Compétence #2', 'niveau' => 75],
    ['nom' => 'Compétence #3', 'niveau' => 100],
];

$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { margin: 0px; }
body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; margin: 0; padding: 0; color: #2F2F2F; }
.left { width: 35%; background: #E8F0E3; float: left; padding: 20px 15px; height: 1117px; }
.right { width: 65%; float: right; padding: 20px 20px 20px 10px; }
.photo-losange { width: 110px; height: 110px; margin: 10px auto 20px auto; transform: rotate(45deg); overflow: hidden; border: 4px solid #6B8E23; background: #fff; }
.photo-losange img { width: 156px; height: 156px; margin: -23px 0 0 -23px; transform: rotate(-45deg); }
.nom { font-size: 22pt; font-weight: bold; text-align: center; margin: 20px 0 5px 0; line-height: 1.1; }
.poste { font-size: 9pt; text-align: center; color: #555; margin-bottom: 25px; }
.section-left { margin-bottom: 18px; }
.titre-left { font-size: 11pt; font-weight: bold; border-bottom: 2px solid #6B8E23; padding-bottom: 3px; margin-bottom: 8px; }
.titre-right { background: #2F4F2F; color: white; font-size: 12pt; font-weight: bold; padding: 7px 10px; margin: 0 0 12px 12px; position: relative; }
.titre-right::before { content: ""; position: absolute; left: -10px; top: 0; width: 0; height: 0; border-top: 15px solid transparent; border-bottom: 15px solid transparent; border-right: 10px solid #6B8E23; }
.bloc-right { margin-bottom: 18px; padding-left: 12px; }
.item-title { font-weight: bold; margin-bottom: 1px; }
.item-date { color: #666; font-size: 9pt; margin-bottom: 4px; }
.item-text { margin-bottom: 12px; text-align: justify; font-size: 9pt; }
.barre-comp { background: #D3D3D3; height: 7px; margin: 3px 0 10px 0; }
.barre-fill { background: #6B8E23; height: 7px; }
.coord-item { margin-bottom: 5px; font-size: 9pt; }
.coord-label { font-weight: bold; }
.profil-text { font-size: 9pt; text-align: justify; }
</style>
</head>
<body>
<div class="left">
    <div class="photo-losange">'.($photo? '<img src="'.$photo.'">' : '').'</div>
    <div class="nom">'.strtoupper($nom).'</div>
    <div class="poste">'.strtoupper($poste).'</div>
    <div class="section-left">
        <div class="titre-left">PROFIL</div>
        <div class="profil-text">'.$profil.'</div>
    </div>
    <div class="section-left">
        <div class="titre-left">COORDONNÉES</div>
        <div class="coord-item"><span class="coord-label">TÉLÉPHONE :</span><br>'.$telephone.'</div>
        <div class="coord-item"><span class="coord-label">SITE WEB :</span><br>'.$site.'</div>
        <div class="coord-item"><span class="coord-label">E-MAIL :</span><br>'.$email.'</div>
    </div>
</div>
<div class="right">
    <div class="bloc-right">
        <div class="titre-right">FORMATION</div>';
        foreach($formations as $f){
            $html.= '<div class="item-title">'.$f['etablissement'].'</div>
            <div class="item-date">'.$f['date_debut'].' - '.$f['date_fin'].'</div>
            <div class="item-text">'.$f['description'].'</div>';
        }
$html.= '</div>
    <div class="bloc-right">
        <div class="titre-right">PARCOURS PROFESSIONNEL</div>';
        foreach($experiences as $e){
            $html.= '<div class="item-title">'.$e['societe'].' | '.$e['poste'].'</div>
            <div class="item-date">'.$e['date_debut'].' - '.$e['date_fin'].'</div>
            <div class="item-text">'.$e['description'].'</div>';
        }
$html.= '</div>
    <div class="bloc-right">
        <div class="titre-right">COMPÉTENCES</div>';
        foreach($competences as $c){
            $html.= '<div style="margin-bottom: 8px;">
                <div style="font-size: 9pt;">'.$c['nom'].'</div>
                <div class="barre-comp"><div class="barre-fill" style="width: '.$c['niveau'].'%;"></div></div>
            </div>';
        }
$html.= '</div>
</div>
</body>
</html>';

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("cv_modele4.pdf", ["Attachment" => false]);
?>