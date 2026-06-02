<?php
// =========================================================================
// 1. CONNEXION À LA BASE DE DONNÉES & INITIALISATION
// =========================================================================
$host = 'localhost';
$dbname = 'gestion_stock_db';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (Exception $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Traitement AJAX en arrière-plan pour stocker dans la session au cas où on rafraîchit
if (isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    if ($_POST['ajax_action'] === 'ajouter') {
        $ref = $_POST['reference'] ?? '';
        if (!isset($_SESSION['proforma_items'])) $_SESSION['proforma_items'] = [];
        
        if (isset($_SESSION['proforma_items'][$ref])) {
            $_SESSION['proforma_items'][$ref]['quantite'] += intval($_POST['quantite']);
        } else {
            $_SESSION['proforma_items'][$ref] = [
                'designation' => $_POST['designation'],
                'prix' => floatval($_POST['prix']),
                'quantite' => intval($_POST['quantite'])
            ];
        }
        echo json_encode(['status' => 'success']);
        exit;
    }
    if ($_POST['ajax_action'] === 'vider') {
        $_SESSION['proforma_items'] = [];
        echo json_encode(['status' => 'success']);
        exit;
    }
}

// Récupération des articles
$query = $db->query("SELECT id, designation, reference, prix_vente, quantite_stock FROM produits ORDER BY designation ASC");
$tous_les_produits = $query->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture Proforma - AR Stock</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; color: #212529; font-family: 'Segoe UI', sans-serif; }
        .header-box { background-color: #ffffff; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #0d6efd; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .section-box { background-color: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #dee2e6; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .product-card { background-color: #ffffff; border: 1px solid #ced4da; border-radius: 8px; padding: 15px; height: 100%; transition: all 0.2s; }
        .product-card:hover { border-color: #0d6efd; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .table th { background-color: #f1f3f5 !important; color: #0d6efd !important; font-size: 0.85rem; }
        .indisponible-msg { color: #dc3545; font-weight: bold; text-align: center; padding: 20px; display: none; }
        .sticky-bill { position: sticky; top: 20px; }
        .input-qte { text-align: center; width: 60px; }
    </style>
</head>
<body>

<div class="container-fluid px-4 py-3">
    
    <div class="header-box">
        <h1 class="h4 mb-1 text-dark">📄 Facture Proforma</h1>
        <p class="text-muted small mb-0">SANS IMPACT STOCK — Créez des devis et factures proforma sans modifier votre inventaire.</p>
    </div>

    <div class="row g-3">
        
        <div class="col-lg-7">
            <div class="section-box">
                <label for="search-stock" class="form-label fw-bold mb-2 text-secondary">🔍 Filtrer par nom ou code...</label>
                <input type="text" id="search-stock" class="form-control form-control-lg" placeholder="Tapez au moins 2 lettres pour filtrer vos articles...">
            </div>

            <div class="section-box">
                <h3 class="h6 mb-3 text-muted fw-bold">📦 Catalogue produits</h3>
                
                <div id="msg-indisponible" class="indisponible-msg border border-danger rounded bg-light">
                    ❌ Marchandise indisponible
                </div>

                <div class="row g-2" id="grid-produits">
                    <?php foreach ($tous_les_produits as $produit): ?>
                        <div class="col-md-6 product-item" data-nom="<?= strtolower(htmlspecialchars($produit['designation'])) ?>" data-ref="<?= strtolower(htmlspecialchars($produit['reference'])) ?>">
                            <div class="product-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <span class="badge bg-light text-dark border border-secondary small">Code: <?= htmlspecialchars($produit['reference']) ?></span>
                                    <small class="text-muted">Stock: <span class="text-danger fw-bold"><?= $produit['quantite_stock'] ?></span></small>
                                </div>
                                <h5 class="h6 text-dark my-2 fw-bold"><?= htmlspecialchars($produit['designation']) ?></h5>
                                <div class="h6 text-primary fw-bold mb-3"><?= number_format($produit['prix_vente'], 0, ',', ' ') ?> F</div>
                                
                                <div class="d-flex gap-1">
                                    <input type="number" id="qte-<?= htmlspecialchars($produit['reference']) ?>" class="form-control form-control-sm input-qte" value="1" min="1">
                                    <button type="button" class="btn btn-sm btn-primary w-100 fw-bold btn-ajouter-direct"
                                            data-ref="<?= htmlspecialchars($produit['reference']) ?>"
                                            data-des="<?= htmlspecialchars($produit['designation']) ?>"
                                            data-px="<?= htmlspecialchars($produit['prix_vente']) ?>">
                                        + Ajouter
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="section-box sticky-bill">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h3 class="h6 mb-0 text-success fw-bold">📑 Articles ajoutés à la proforma</h3>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="btn-vider-panier">Vider</button>
                </div>
                
                <div class="table-responsive">
                    <table class="table align-middle table-striped" id="table-proforma">
                        <thead>
                            <tr>
                                <th>Réf / Désignation</th>
                                <th class="text-end">P.U</th>
                                <th class="text-center">Qté</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody id="proforma-tbody">
                            <tr id="row-empty"><td colspan="4" class="text-center text-muted py-4 small">Aucun article sur la facture. Cliquez sur "+ Ajouter" à gauche.</td></tr>
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="3" class="text-end fw-bold text-secondary">NET À PAYER :</td>
                                <td class="text-end fw-bold text-success h5 text-nowrap" id="grand-total-facture">0 F</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-stock');
    const cards = document.querySelectorAll('.product-item');
    const msgIndisponible = document.getElementById('msg-indisponible');
    
    const proformaTbody = document.getElementById('proforma-tbody');
    const grandTotalFacture = document.getElementById('grand-total-facture');
    const rowEmpty = document.getElementById('row-empty');

    // Objet global temporaire pour stocker les éléments affichés à l'écran
    let itemsFacture = {};

    // Fonction pour recalculer et mettre à jour le tableau HTML à droite
    function rafraichirTableauVisuel() {
        const keys = Object.keys(itemsFacture);
        if (keys.length === 0) {
            if (rowEmpty) rowEmpty.style.display = '';
            proformaTbody.innerHTML = '<tr id="row-empty"><td colspan="4" class="text-center text-muted py-4 small">Aucun article sur la facture. Cliquez sur "+ Ajouter" à gauche.</td></tr>';
            grandTotalFacture.innerText = "0 F";
            return;
        }

        let html = '';
        let totalGeneral = 0;

        keys.forEach(ref => {
            const item = itemsFacture[ref];
            const totalLigne = item.prix * item.quantite;
            totalGeneral += totalLigne;

            html += `
                <tr>
                    <td>
                        <small class="text-muted d-block">${ref}</small>
                        <span class="fw-bold text-dark">${item.designation}</span>
                    </td>
                    <td class="text-end text-nowrap">${item.prix.toLocaleString('fr-FR')} F</td>
                    <td class="text-center fw-bold text-info">${item.quantite}</td>
                    <td class="text-end fw-bold text-primary text-nowrap">${totalLigne.toLocaleString('fr-FR')} F</td>
                </tr>
            `;
        });

        proformaTbody.innerHTML = html;
        grandTotalFacture.innerText = totalGeneral.toLocaleString('fr-FR') + " F";
    }

    // CLIC SUR AJOUTER : INCORPORATION DIRECTE DANS LE TABLEAU
    document.querySelectorAll('.btn-ajouter-direct').forEach(btn => {
        btn.addEventListener('click', function() {
            const ref = this.getAttribute('data-ref');
            const designation = this.getAttribute('data-des');
            const prix = parseFloat(this.getAttribute('data-px'));
            const inputQte = document.getElementById('qte-' + ref);
            const qte = parseInt(inputQte.value) || 1;

            // 1. Mise à jour immédiate de l'affichage à droite (Zéro bug possible)
            if (itemsFacture[ref]) {
                itemsFacture[ref].quantite += qte;
            } else {
                itemsFacture[ref] = {
                    designation: designation,
                    prix: prix,
                    quantite: qte
                };
            }
            rafraichirTableauVisuel();

            // 2. Sauvegarde discrète en arrière-plan dans la session PHP
            const formData = new FormData();
            formData.append('ajax_action', 'ajouter');
            formData.append('reference', ref);
            formData.append('designation', designation);
            formData.append('prix', prix);
            formData.append('quantite', qte);
            fetch(window.location.href, { method: 'POST', body: formData });
        });
    });

    // BOUTON VIDER
    document.getElementById('btn-vider-panier').addEventListener('click', function() {
        itemsFacture = {};
        rafraichirTableauVisuel();
        
        const formData = new FormData();
        formData.append('ajax_action', 'vider');
        fetch(window.location.href, { method: 'POST', body: formData });
    });

    // FILTRAGE VISUEL EN TEMPS RÉEL (RECHERCHE)
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        let visibleCards = 0;

        if (query.length < 2) {
            cards.forEach(card => card.style.display = '');
            msgIndisponible.style.display = 'none';
            return;
        }

        cards.forEach(card => {
            const nom = card.getAttribute('data-nom');
            const ref = card.getAttribute('data-ref');
            if (nom.includes(query) || ref.includes(query)) {
                card.style.display = '';
                visibleCards++;
            } else {
                card.style.display = 'none';
            }
        });

        msgIndisponible.style.display = (visibleCards === 0) ? 'block' : 'none';
    });
});
</script>
</body>
</html>