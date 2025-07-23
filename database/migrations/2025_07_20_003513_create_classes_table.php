<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 10);
            $table->enum('major', ['TKJ', 'RPL', 'MM']);
            $table->integer('grade')->unsigned()->between(10, 12);
            $table->string('teacher_name', 100);
            $table->integer('max_students')->unsigned();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('classes');
    }
};