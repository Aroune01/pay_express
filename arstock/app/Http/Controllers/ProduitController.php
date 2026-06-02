<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function index()
    {
        $produits = Produit::all();
        return view('produits.index', compact('produits'));
    }

    public function create()
    {
        return view('produits.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'quantite' => 'required|integer',
            'prix_vente' => 'required|numeric',
        ]);

        Produit::create($request->all());
        return redirect()->route('produits.index')->with('success', 'Produit ajouté !');
    }
}