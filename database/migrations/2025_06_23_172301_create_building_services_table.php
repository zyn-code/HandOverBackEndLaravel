<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class CreateBuildingServicesTable extends Migration
{
    public function up()
    {
        Schema::create('building_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('building_services');
    }
};
