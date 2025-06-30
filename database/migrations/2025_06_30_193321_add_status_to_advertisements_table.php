<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
{
    Schema::table('advertisements', function (Blueprint $table) {
        $table->string('status')->default('new')->after('price');
    });
}

public function down()
{
    Schema::table('advertisements', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}

};
