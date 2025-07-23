<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('spp_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->year('year'); // Tahun ajaran
            $table->decimal('amount', 12, 2); // Jumlah SPP per bulan
            $table->text('note')->nullable(); // Keterangan tambahan
            $table->timestamps();
            
            // Satu kelas hanya boleh memiliki satu biaya SPP per tahun
            $table->unique(['class_id', 'year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('spp_costs');
    }
};