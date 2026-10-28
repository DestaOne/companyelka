<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('phone', 30);
            $table->string('email');
            $table->string('subject', 150);
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Pesan masuk juga tercatat sebagai aktivitas agar muncul di lonceng notifikasi.
        Schema::table('activities', function (Blueprint $table) {
            $table->foreignId('contact_message_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contact_message_id');
        });

        Schema::dropIfExists('contact_messages');
    }
};
