<?php
// 1. CONNEXION
$conn = new mysqli("localhost", "root", "", "shop_db");

// 2. ACTIONS
if (isset($_POST['action_ajout'])) {
    $nom = mysqli_real_escape_string($conn, $_POST['designation']);
    $cat = mysqli_real_escape_string($conn, $_POST['categorie']);
    $prix = $_POST['prix'];
    $qte = $_POST['quantite'];
    $conn->query("INSERT INTO produits (designation, categorie, prix_vente, quantite_reel) VALUES ('$nom', '$cat', '$prix', '$qte')");
    header("Location: index.php?page=stocks");
}

if (isset($_POST['action_vente'])) {
    $id_p = $_POST['id_produit'];
    $qte_v = $_POST['qte_vendre'];
    $res = $conn->query("SELECT * FROM produits WHERE id = $id_p");
    if($res && $p = $res->fetch_assoc()) {
        if($p['quantite_reel'] >= $qte_v) {
            $total = $p['prix_vente'] * $qte_v;
            $conn->query("INSERT INTO ventes (id_produit, quantite_vendue, montant_total) VALUES ($id_p, $qte_v, $total)");
            $conn->query("UPDATE produits SET quantite_reel = quantite_reel - $qte_v WHERE id = $id_p");
        }
    }
    header("Location: index.php?page=dashboard");
}

// 3. DONNÉES DASHBOARD (Vérification si tables existent)
$page = $_GET['page'] ?? 'dashboard';
$ventes_jour = 0; $nb_trans = 0; $stock_total = 0; $ruptures = 0;

$check_v = $conn->query("SELECT SUM(montant_total), COUNT(*) FROM ventes WHERE DATE(date_vente) = CURDATE()");
if($check_v) { list($ventes_jour, $nb_trans) = $check_v->fetch_row(); }

$check_p = $conn->query("SELECT SUM(quantite_reel), COUNT(CASE WHEN quantite_reel <= 3 THEN 1 END) FROM produits");
if($check_p) { list($stock_total, $ruptures) = $check_p->fetch_row(); }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>ShopManager</title>
</head>
<body class="bg-[#0f172a] text-slate-300">
    <div class="flex h-screen overflow-hidden">
        <!-- SIDEBAR -->
        <aside class="w-64 bg-[#1e293b]/50 border-r border-slate-800 p-6">
            <div class="mb-10 text-white text-xl font-bold italic underline">ShopManager</div>
            <nav class="space-y-4">
                <a href="?page=dashboard" class="block p-3 rounded-xl <?php echo $page == 'dashboard' ? 'bg-blue-600 text-white' : ''; ?>"><i class="fa-solid fa-chart-line mr-2"></i> Dashboard</a>
                <a href="?page=stocks" class="block p-3 rounded-xl <?php echo $page == 'stocks' ? 'bg-blue-600 text-white' : ''; ?>"><i class="fa-solid fa-box mr-2"></i> Stocks</a>
            </nav>
        </aside>

        <!-- CONTENT -->
        <main class="flex-1 overflow-y-auto">
            <header class="p-8 flex justify-between items-center">
                <h2 class="text-3xl font-bold text-white"><?php echo strtoupper($page); ?></h2>
                <div class="flex gap-2">
                    <button onclick="document.getElementById('mStock').classList.remove('hidden')" class="bg-slate-700 px-4 py-2 rounded-lg text-sm font-bold border border-slate-600 hover:bg-slate-600">📦 + Marchandise</button>
                    <button onclick="document.getElementById('mVente').classList.remove('hidden')" class="bg-blue-600 px-4 py-2 rounded-lg text-sm font-bold text-white shadow-lg hover:bg-blue-500">💰 + Vendre</button>
                </div>
            </header>

            <div class="p-8">
                <?php if($page == 'dashboard'): ?>
                    <div class="grid grid-cols-4 gap-6">
                        <div class="bg-[#1e293b] p-6 rounded-2xl border border-slate-800"><p class="text-slate-500 text-xs mb-1">VENTES DU JOUR</p><p class="text-2xl font-bold text-white"><?php echo number_format((float)$ventes_jour, 0, '.', ' '); ?> F</p></div>
                        <div class="bg-[#1e293b] p-6 rounded-2xl border border-slate-800"><p class="text-slate-500 text-xs mb-1">TRANSACTIONS</p><p class="text-2xl font-bold text-white"><?php echo (int)$nb_trans; ?></p></div>
                        <div class="bg-[#1e293b] p-6 rounded-2xl border border-slate-800"><p class="text-slate-500 text-xs mb-1">STOCK TOTAL</p><p class="text-2xl font-bold text-white"><?php echo (int)$stock_total; ?></p></div>
                        <div class="bg-[#1e293b] p-6 rounded-2xl border border-slate-800"><p class="text-slate-500 text-xs mb-1">RUPTURES</p><p class="text-2xl font-bold text-red-500"><?php echo (int)$ruptures; ?></p></div>
                    </div>
                <?php else: ?>
                    <div class="bg-[#1e293b] rounded-2xl border border-slate-800 overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-slate-800/50 text-xs text-slate-500 font-bold">
                                <tr><th class="p-4">Désignation</th><th class="p-4">Catégorie</th><th class="p-4">Prix</th><th class="p-4">Quantité</th></tr>
                            </thead>
                            <tbody>
                                <?php $res = $conn->query("SELECT * FROM produits"); if($res) while($p = $res->fetch_assoc()): ?>
                                <tr class="border-t border-slate-800">
                                    <td class="p-4 text-white font-bold"><?php echo $p['designation']; ?></td>
                                    <td class="p-4"><?php echo $p['categorie']; ?></td>
                                    <td class="p-4 text-blue-400 font-mono"><?php echo number_format($p['prix_vente'], 0, '.', ' '); ?> F</td>
                                    <td class="p-4"><span class="bg-blue-900/30 text-blue-400 px-2 py-1 rounded"><?php echo $p['quantite_reel']; ?></span></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- MODAL VENTE -->
    <div id="mVente" class="hidden fixed inset-0 bg-black/90 flex items-center justify-center z-50">
        <form method="POST" class="bg-[#1e293b] p-8 rounded-3xl w-80 border border-slate-700">
            <h3 class="font-bold text-white mb-4">VENDRE UN ARTICLE</h3>
            <select name="id_produit" required class="w-full bg-[#0f172a] p-3 rounded-lg mb-3 border border-slate-700">
                <option value="">Choisir...</option>
                <?php $ps = $conn->query("SELECT * FROM produits WHERE quantite_reel > 0"); if($ps) while($r = $ps->fetch_assoc()) echo "<option value='".$r['id']."'>".$r['designation']." (".$r['quantite_reel'].")</option>"; ?>
            </select>
            <input type="number" name="qte_vendre" placeholder="Quantité" required class="w-full bg-[#0f172a] p-3 rounded-lg mb-4 border border-slate-700">
            <button name="action_vente" class="w-full bg-blue-600 p-3 rounded-lg font-bold text-white">VALIDER</button>
            <button type="button" onclick="this.parentElement.parentElement.classList.add('hidden')" class="w-full mt-2 text-slate-500 text-sm">Annuler</button>
        </form>
    </div>

    <!-- MODAL STOCK -->
    <div id="mStock" class="hidden fixed inset-0 bg-black/90 flex items-center justify-center z-50">
        <form method="POST" class="bg-[#1e293b] p-8 rounded-3xl w-80 border border-slate-700">
            <h3 class="font-bold text-white mb-4">NOUVEL ARTICLE</h3>
            <input type="text" name="categorie" placeholder="Catégorie" required class="w-full bg-[#0f172a] p-3 rounded-lg mb-3 border border-slate-700">
            <input type="text" name="designation" placeholder="Nom du produit" required class="w-full bg-[#0f172a] p-3 rounded-lg mb-3 border border-slate-700">
            <input type="number" name="prix" placeholder="Prix" required class="w-full bg-[#0f172a] p-3 rounded-lg mb-3 border border-slate-700">
            <input type="number" name="quantite" placeholder="Quantité" required class="w-full bg-[#0f172a] p-3 rounded-lg mb-4 border border-slate-700">
            <button name="action_ajout" class="w-full bg-green-600 p-3 rounded-lg font-bold text-white">AJOUTER</button>
            <button type="button" onclick="this.parentElement.parentElement.classList.add('hidden')" class="w-full mt-2 text-slate-500 text-sm">Annuler</button>
        </form>
    </div>
</body>
</html>