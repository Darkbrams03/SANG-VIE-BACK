<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('donors', function (Blueprint $table) {
        // Si status n'existe pas encore :
        if (!Schema::hasColumn('donors', 'status')) {
            $table->enum('status', ['pending', 'verified', 'deferred'])->default('pending')->after('city');
        }
       
    });
}

public function down(): void
{
    Schema::table('donors', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}
};
