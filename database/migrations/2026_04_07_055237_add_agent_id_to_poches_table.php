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
    Schema::table('poches', function (Blueprint $table) {
        // On lie la poche à l'ID de l'utilisateur (agent)
        $table->foreignId('agent_id')->nullable()->constrained('users')->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('poches', function (Blueprint $table) {
            //
        });
    }
};
