<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Layanan yang sudah ada tetap tampil di situs.
        Schema::table('services', function (Blueprint $table) {
            $table->string('status', 10)->default('terbit')->after('detail');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
