<?php include('db.php'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vente au comptoir - AR STOCK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .main-wrapper { display: flex; height: 100vh; margin-left: 260px; }
        .sidebar { width: 260px; height: 100vh; position: fixed; background: #fff; border-right: 1px solid #ddd; left:0; }
        .pos-container { flex: 1; padding: 20px; overflow-y: auto; }
        .cart-sidebar { width: 380px; background: white; border-left: 1px solid #ddd; padding: 20px; display: flex; flex-direction: column; }
        .card-section { background: white; border-radius: 10px; padding: 20px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); position: relative; }
        #suggestions { position: absolute; width: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 0 0 10px 10px; display: none; }
        .suggestion-item { padding: 10px; cursor: pointer; border-bottom: 1px solid #eee; }
        .suggestion-item:hover { background: #f8f9fa; }
        .pay-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .pay-btn { border: 1px solid #ddd; border-radius: 8px; padding: 10px; text-align: center; cursor: pointer; }
        .pay-btn.active { border-color: #0d47a1; background: #e3f2fd; color: #0d47a1; font-weight: bold; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="p-4 text-center border-bottom">
        
        <small class="text-muted">Employé</small>
    </div>
    <nav class="nav flex-column p-2 mt-3">
        <a class="nav-link text-dark" href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
        <a class="nav-link active bg-light fw-bold text-primary rounded" href="#"><i class="bi bi-calculator me-2"></i> Au comptoir</a>
    </nav>
</div>

<div class="main-wrapper">
    <div class="pos-container">
        <div class="card-section">
            <label class="small fw-bold mb-2">RECHERCHER UN PRODUIT</label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" id="recherche" class="form-control" placeholder="Tapez le nom du produit...">
            </div>
            <div id="suggestions"></div>
        </div>

        <div class="card-section">
            <div class="row">
                <div class="col-md-6">
                    <label class="small fw-bold">PRODUIT SÉLECTIONNÉ</label>
                    <input type="text" id="prod_nom" class="form-control bg-light" readonly>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold">PRIX UNITAIRE</label>
                    <input type="number" id="prod_prix" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold">QUANTITÉ</label>
                    <input type="number" id="prod_qte" class="form-control" value="1">
                </div>
            </div>
            <button onclick="ajouterAuPanier()" class="btn btn-primary w-100 mt-3 fw-bold">
                <i class="bi bi-cart-plus"></i> AJOUTER AU PANIER
            </button>
        </div>

        <div class="card-section">
            <label class="small fw-bold mb-3">MODE DE PAIEMENT</label>
            <div class="pay-grid">
                <div class="pay-btn active">Espèce</div>
                <div class="pay-btn">Orange</div>
                <div class="pay-btn">MTN</div>
                <div class="pay-btn">Wave</div>
                <div class="pay-btn">Moov</div>
            </div>
        </div>
    </div>

    <div class="cart-sidebar">
        <h6 class="fw-bold border-bottom pb-2">VOTRE PANIER</h6>
        <div id="liste-panier" class="flex-grow-1 overflow-auto py-2">
            <p class="text-muted text-center mt-5 small">Panier vide</p>
        </div>
        <div class="border-top pt-3">
            <div class="d-flex justify-content-between fs-5 fw-bold">
                <span>TOTAL</span>
                <span id="total-final" class="text-primary">0 FCFA</span>
            </div>
            <button onclick="validerVente()" class="btn btn-primary w-100 p-3 mt-3 fw-bold shadow">
                VALIDER LA VENTE
            </button>
        </div>
    </div>
</div>

<script>
let panier = [];
let produitActuel = null;

// Recherche de produit (Autocomplete)
document.getElementById('recherche').addEventListener('input', function() {
    let q = this.value;
    if(q.length >= 2) {
        fetch('rechercher_produit.php?q=' + q)
        .then(res => res.json())
        .then(data => {
            let html = '';
            data.forEach(p => {
                html += `<div class="suggestion-item" onclick='selectionnerProduit(${JSON.stringify(p)})'>
                            ${p.designation} - <span class="text-primary">${p.prix_vente} F</span>
                         </div>`;
            });
            document.getElementById('suggestions').innerHTML = html;
            document.getElementById('suggestions').style.display = 'block';
        });
    } else {
        document.getElementById('suggestions').style.display = 'none';
    }
});

function selectionnerProduit(p) {
    produitActuel = p;
    document.getElementById('prod_nom').value = p.designation;
    document.getElementById('prod_prix').value = p.prix_vente;
    document.getElementById('recherche').value = '';
    document.getElementById('suggestions').style.display = 'none';
}

function ajouterAuPanier() {
    if(!produitActuel) return alert("Sélectionnez un produit d'abord !");
    
    let qte = parseInt(document.getElementById('prod_qte').value);
    let item = {
        id: produitActuel.id,
        nom: produitActuel.designation,
        prix: parseInt(document.getElementById('prod_prix').value),
        qte: qte
    };
    
    panier.push(item);
    majPanier();
}

function majPanier() {
    let html = '';
    let total = 0;
    panier.forEach((item, index) => {
        total += item.prix * item.qte;
        html += `<div class="d-flex justify-content-between border-bottom py-2 small">
                    <div><b>${item.nom}</b><br>${item.qte} x ${item.prix} F</div>
                    <button class="btn btn-sm text-danger" onclick="panier.splice(${index},1);majPanier()"><i class="bi bi-trash"></i></button>
                 </div>`;
    });
    document.getElementById('liste-panier').innerHTML = html || '<p class="text-muted text-center mt-5 small">Panier vide</p>';
    document.getElementById('total-final').innerText = total + ' FCFA';
}

function validerVente() {
    if (panier.length === 0) return alert("Le panier est vide !");

    // On prépare les données à envoyer
    let données = {
        panier: panier,
        total: document.getElementById('total-final').innerText.replace(' FCFA', '')
    };

    // On envoie au fichier PHP
    fetch('enregistrer_vente.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(données)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Vente validée et stock mis à jour !");
            panier = [];
            majPanier();
            window.location.reload(); // Pour actualiser les chiffres
        } else {
            alert("Erreur : " + data.message);
        }
    });
}
</script>

</body>
</html>