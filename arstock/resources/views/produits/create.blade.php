<!DOCTYPE html>
<html>
<head><title>Ajouter Produit</title></head>
<body>
    <h1>Ajouter un produit</h1>
    <a href="{{ route('produits.index') }}">← Retour</a>
    <br><br>

    <form action="{{ route('produits.store') }}" method="POST">
        @csrf
        <p>Nom: <input type="text" name="nom" required></p>
        <p>Référence: <input type="text" name="reference"></p>
        <p>Quantité: <input type="number" name="quantite" value="0" required></p>
        <p>Prix Achat: <input type="number" step="0.01" name="prix_achat" value="0"></p>
        <p>Prix Vente: <input type="number" step="0.01" name="prix_vente" value="0" required></p>
        <p>Description: <textarea name="description"></textarea></p>
        <button type="submit">Enregistrer</button>
    </form>
</body>
</html>