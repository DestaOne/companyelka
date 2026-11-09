<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gallery_images', function (Blueprint $table) {
            $table->unsignedInteger('position')->default(0)->index()->after('title');
        });

        // Urutan lama mengikuti id; jadikan itu posisi awal agar tampilan tidak berubah.
        DB::table('gallery_images')->orderBy('id')->pluck('id')->each(
            fn ($id, $index) => DB::table('gallery_images')->where('id', $id)->update(['position' => $index + 1])
        );
    }

    public function down(): void
    {
        Schema::table('gallery_images', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
