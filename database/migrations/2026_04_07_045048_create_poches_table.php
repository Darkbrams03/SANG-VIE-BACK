<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('poches', function (Blueprint $table) {
        $table->id();
        $table->string('code_barre')->unique(); // ID Unique de la poche
        $table->string('type_produit'); // CGR, PFC, etc.
        $table->date('date_prelevement');
        $table->date('date_peremption');
        $table->string('groupe'); // O+, A-, etc.
        $table->string('status')->default('Disponible'); // Disponible, Sorti, Périmé
        $table->string('service_destinataire')->nullable(); // Pour les sorties
        $table->string('motif_sortie')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poches');
    }
};
