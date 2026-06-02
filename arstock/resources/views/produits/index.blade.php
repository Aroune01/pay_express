<!DOCTYPE html>
<html>
<head><title>AR STOCK - Produits</title></head>
<body>
    <h1>AR STOCK - Liste des produits</h1>
    <a href="{{ route('produits.create') }}">+ Ajouter un produit</a>
    <br><br>
    
    @if(session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif

    <table border="1" cellpadding="10">
        <tr>
            <th>Nom</th>
            <th>Référence</th>
            <th>Quantité</th>
            <th>Prix Vente</th>
        </tr>
        @foreach($produits as $produit)
        <tr>
            <td>{{ $produit->nom }}</td>
            <td>{{ $produit->reference }}</td>
            <td>{{ $produit->quantite }}</td>
            <td>{{ $produit->prix_vente }} FCFA</td>
        </tr>
        @endforeach
    </table>
</body>
</html>