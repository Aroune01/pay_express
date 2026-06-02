<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['proforma_items'])) {
    $_SESSION['proforma_items'] = [];
}

// Gestion de l'ajout d'un produit
if (isset($_POST['action']) && $_POST['action'] === 'ajouter') {
    $ref = $_POST['reference'] ?? '';
    $designation = $_POST['designation'] ?? '';
    $prix = floatval($_POST['prix'] ?? 0);
    $qte = intval($_POST['quantite'] ?? 1);
    if ($qte <= 0) $qte = 1;

    if (!empty($ref)) {
        if (isset($_SESSION['proforma_items'][$ref])) {
            $_SESSION['proforma_items'][$ref]['quantite'] += $qte;
        } else {
            $_SESSION['proforma_items'][$ref] = [
                'designation' => $designation,
                'prix' => $prix,
                'quantite' => $qte
            ];
        }
    }
}

// Gestion pour vider la liste
if (isset($_POST['action']) && $_POST['action'] === 'vider') {
    $_SESSION['proforma_items'] = [];
}

// Une fois le traitement fini, on retourne TOUJOURS sur la page principale
header("Location: proforma.php");
exit;