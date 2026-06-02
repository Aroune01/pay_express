<?php
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    $modele = intval($data['modele']) ?? 1;
    
    // Gérer l'upload photo avec chemin complet
    $photo_path = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo_name = 'photo_' . time() . '.' . $ext;
        $photo_path = 'uploads/' . $photo_name;
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path);
        $data['photo_path'] = __DIR__ . '/' . $photo_path;
    }
    
    // Gérer les langues cochées
    $data['langues_str'] = '';
    if (!empty($data['langues'])) {
        $data['langues_str'] = implode(', ', $data['langues']);
    }
    if (!empty($data['autre_langue'])) {
        $data['langues_str'] .= ', ' . $data['autre_langue'];
    }

    // Charger le template HTML
    ob_start();
    include "templates/modele{$modele}.php";
    $html = ob_get_clean();

    // Options DomPDF pour autoriser les images locales
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('chroot', __DIR__);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    $filename = 'CV_' . $data['nom'] . '_' . $data['prenom'] . '.pdf';
    $dompdf->stream($filename, ['Attachment' => true]);
    exit;
}
?>