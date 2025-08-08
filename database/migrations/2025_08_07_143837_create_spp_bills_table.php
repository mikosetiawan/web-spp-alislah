<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSppBillsTable extends Migration
{
    public function up()
    {
        Schema::create('spp_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('spp_cost_id')->constrained()->onDelete('cascade');
            $table->string('academic_year')->nullable();
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('unpaid');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('spp_bills');
    }
}