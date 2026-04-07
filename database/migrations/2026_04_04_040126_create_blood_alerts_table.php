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
    Schema::create('blood_alerts', function (Blueprint $table) {
        $table->id();
        $table->string('group'); // O-, A+, etc.
        $table->integer('needed_pockets'); // Quantité
        $table->string('location')->default('CNHU-HKM (COTONOU)');
        $table->boolean('is_active')->default(false);
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_alerts');
    }
};
