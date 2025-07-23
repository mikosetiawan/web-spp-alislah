<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 20)->unique();
            $table->string('name', 100);
            $table->string('email')->unique();
            $table->string('phone', 15);
            $table->text('address');
            $table->foreignId('class_id')->constrained('classes');
            $table->enum('gender', ['L', 'P']);
            $table->date('birth_date');
            $table->string('birth_place', 100);
            $table->string('photo')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('parent_name', 100);  // Pastikan ada
            $table->string('parent_phone', 15);  // Pastikan ada
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
};