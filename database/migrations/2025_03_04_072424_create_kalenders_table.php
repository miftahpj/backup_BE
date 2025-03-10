<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kalenders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_course')->constrained('course')->onDelete('cascade');
            $table->date('start'); // Menggunakan tipe date untuk format YYYY-MM-DD
            $table->date('end');   // Menggunakan tipe date untuk format YYYY-MM-DD
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kalenders');
    }
};

