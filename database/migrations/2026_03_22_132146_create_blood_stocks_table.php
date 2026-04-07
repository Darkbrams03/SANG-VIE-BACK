<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::create('blood_stocks', function (Blueprint $table) {
        $table->id();
        $table->string('center_name');
        $table->string('blood_group');
        $table->integer('quantity')->default(0);
        $table->string('status')->default('OPTIMAL');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_stocks');
    }
};
