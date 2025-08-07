<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeYearColumnTypeInSppCosts extends Migration
{
    public function up()
    {
        Schema::table('spp_costs', function (Blueprint $table) {
            $table->string('year')->change();
        });
    }

    public function down()
    {
        Schema::table('spp_costs', function (Blueprint $table) {
            $table->integer('year')->change();
        });
    }
}