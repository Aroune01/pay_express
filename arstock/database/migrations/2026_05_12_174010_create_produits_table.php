<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('reference')->unique()->nullable();
            $table->integer('quantite')->default(0);
            $table->decimal('prix_achat', 10, 2)->default(0);
            $table->decimal('prix_vente', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};