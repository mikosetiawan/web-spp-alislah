<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->date('month'); // Format YYYY-MM-01 (hanya menyimpan bulan dan tahun)
            $table->decimal('amount', 12, 2); // Jumlah pembayaran
            $table->date('payment_date'); // Tanggal pembayaran
            $table->string('payment_method'); // cash, transfer, qris, etc
            $table->string('receipt_number')->unique()->nullable(); 
            $table->string('status')->default('paid'); // paid, pending, cancelled
            $table->text('note')->nullable(); // Catatan tambahan
            $table->foreignId('admin_id')->constrained('users'); // Petugas yang mencatat
            $table->foreignId('spp_cost_id')->constrained()->onDelete('cascade'); // Referensi biaya SPP
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['student_id', 'month']); // Untuk pencarian cepat
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};