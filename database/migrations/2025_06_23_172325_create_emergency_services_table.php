<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('emergency_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('emergency_services');
    }
};
